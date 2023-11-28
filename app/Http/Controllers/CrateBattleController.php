<?php

namespace App\Http\Controllers;

use App\Events\CrateBattleJoined;
use App\Events\CrateBattleStart;
use App\Jobs\CrateBattleRollResult;
use App\Models\Crate;
use App\Models\CrateBattle;
use App\Models\ProvablyFair;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CrateBattleController extends Controller
{
    public function index() {
        $crate_battles = CrateBattle::whereIn('state', [0, 1]);

        return view('crates.battles.index')->with(['battles' => $crate_battles]);
    }

    public function view($id) {
        $battle = CrateBattle::find($id);

        return view('crates.battles.view')->with(['battle' => $battle]);
    }

    public function getCrateBattles() {
        $battles = CrateBattle::whereIn('game_state', [0, 1])->get();

        foreach($battles as $battle) {
            $crate_list = json_decode($battle->crates, true);
            $player_list = json_decode($battle->players, true);
            $battle->crate_list = Crate::find(array_values($crate_list))->sortBy(function($item) use ($crate_list) {
                return array_search($item->id, $crate_list);
            })->values();
            $battle->player_list = User::find(array_values($player_list))->sortBy(function($item) use ($player_list) {
                return array_search($item->id, $player_list);
            })->values();
        }

        return json_encode(["error" => false, "data" => $battles]);
    }

    public function getCrateBattle($id) {
        $CrateBattle = CrateBattle::find($id);

        $crate_list = json_decode($CrateBattle->crates, true);
        $player_list = json_decode($CrateBattle->players, true);
        $CrateBattle->crate_list = Crate::find(array_values($crate_list))->sortBy(function($item) use ($crate_list) {
            return array_search($item->id, $crate_list);
        })->values();
        $CrateBattle->player_list = User::select(["username", "profile_image", "is_bot"])->whereIn("id", array_values($player_list))->get()->sortBy(function($item) use ($player_list) {
            return array_search($item->id, $player_list);
        })->values();

        foreach($CrateBattle->crate_list as $crate) {
            $crate->contents;
        }

        return json_encode(["error" => false, "data" => $CrateBattle]);
    }

    public function joinGame(Request $request) {
        if(!Auth::check()) return json_encode(["error" => true, "data" => "Unauthorized"]);

        if(!$request->has("spot") || !$request->has("battleId")) return json_encode(["error" => true, "data" => "Invalid request"]);

        $CrateBattle = CrateBattle::find($request->get("battleId"));
        $player_list = json_decode($CrateBattle->players, true);

        if($request->get("spot") == -1) {
            $botId = 1;
            foreach($player_list as $spot => $player) {
                if($player == null) {
                    $player_list[$spot] = $botId;
                    broadcast(new CrateBattleJoined($CrateBattle->id, User::find($botId), $spot));
                    $botId++;
                }
            }
        } else {
            if($player_list[$request->get("spot")] == null) {
                $player_list[$request->get("spot")] = Auth::user()->id;
                broadcast(new CrateBattleJoined($CrateBattle->id, Auth::user(), $request->get("spot")));
            }
        }

        $CrateBattle->players = json_encode($player_list);

        $result = [];

        if(count(array_filter($player_list)) == $this->getMaxPlayers($CrateBattle->battle_type)) {
            $crate_list = json_decode($CrateBattle->crates, true);

            foreach($crate_list as $index => $crate_id) {
                $Crate = Crate::find($crate_id);
                $chances = array_map(function($item){ return $item['chance']; }, $Crate->contents->toArray());
                $items = array_map(function($item){ return $item['skin_id']; }, $Crate->contents->toArray());
                for($i = 0; $i < count(array_filter($player_list)); $i++) {
                    $wonItemId = $this->determineItemOutcome($chances, $items);
                    $result[$index][$i] = $wonItemId;
                }
            }

            $CrateBattle->result = json_encode($result);

            CrateBattleRollResult::dispatch($CrateBattle, $result, 0)->delay(now()->addSeconds(2))->onQueue("battle");
        }

        //$CrateBattle->save();

        return json_encode(["error" => false, "data" => "Joined"]);
    }

    private function getMaxPlayers($battle_type) {
        switch ($battle_type) {
            case 1:
                return 2;
            case 2:
                return 3;
            case 3:
            case 4:
                return 4;
        }
    }

    private function determineItemOutcome($chances, $items) {
        $rand = rand(0, 10000);
        $cumulativeProbability = 0;
        foreach ($chances as $index => $chance) {
            $cumulativeProbability += 10000*($chance/100);
            if ($rand < $cumulativeProbability) {
                return $items[$index];
            }
        }
        return null; // Return null if no item is found
    }
}
