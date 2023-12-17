<?php

namespace App\Http\Controllers;

use App\Events\CrateBattleCallBots;
use App\Events\CrateBattleCreated;
use App\Events\CrateBattleJoined;
use App\Jobs\CrateBattleRollResult;
use App\Models\Crate;
use App\Models\CrateBattle;
use App\Models\ProvablyFair;
use App\Models\Skin;
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

            $crates = Crate::whereIn('id', array_values($crate_list));
            $crates_list = [];

            foreach($crate_list as $key => $crate) {
                $crates_list[$key] = $crates->get()->first();
            }

            $battle->crate_list = $crates_list;
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
        $crates = Crate::whereIn('id', array_values($crate_list));
        $crates_list = [];

        foreach($crate_list as $key => $crate) {
            $crates_list[$key] = $crates->get()->first();
        }

        $CrateBattle->crate_list = $crates_list;
        $CrateBattle->player_list = User::select(["id", "username", "profile_image", "is_bot"])->whereIn("id", array_values($player_list))->get()->sortBy(function($item) use ($player_list) {
            return array_search($item->id, $player_list);
        })->values();

        $wonItems = [];
        $totalEarnings = [];
        $totalEarningsTerminal = [];
        if($CrateBattle->result != null) {
            $result = json_decode($CrateBattle->result, true);
            for($i = 0; $i < count($result); $i++) {
                $wonItems[$i] = [];
                for($x = 0; $x < count($result[$i]); $x++) {
                    $skin = Skin::find($result[$i][$x]);
                    $wonItems[$i][$x] = $skin;
                    if(isset($totalEarnings[$x])) {
                        $totalEarnings[$x] += $skin->price;
                    } else {
                        $totalEarnings[$x] = $skin->price;
                    }

                    if($i == count($result) - 1) {
                        $totalEarningsTerminal[$x] = $skin->price;
                    }
                }
            }
        }
        $CrateBattle->wonItems = $wonItems;
        $CrateBattle->totalEarnings = $totalEarnings;
        $CrateBattle->totalEarningsTerminal = $totalEarningsTerminal;

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

        if(in_array(Auth::user()->id, $player_list)) return json_encode(["error" => true, "data" => "Failed to join"]);

        if($player_list[$request->get("spot")] == null) {
            $player_list[$request->get("spot")] = Auth::user()->id;
            broadcast(new CrateBattleJoined($CrateBattle->id, Auth::user(), $request->get("spot")));
        }

        $CrateBattle->players = json_encode($player_list);
        $CrateBattle->save();

        $this->startGame($CrateBattle, $player_list);

        return json_encode(["error" => false, "data" => "Joined"]);
    }

    public function callBots(Request $request) {
        if(!Auth::check()) return json_encode(["error" => true, "data" => "Unauthorized"]);
        if(!$request->has("battleId")) return json_encode(["error" => true, "data" => "Invalid request"]);

        $CrateBattle = CrateBattle::find($request->get("battleId"));
        $player_list = json_decode($CrateBattle->players, true);

        $bots = [];
        $botId = 1;
        foreach($player_list as $spot => $player) {
            if($player == null) {
                $player_list[$spot] = $botId;
                $bots[$spot] = User::find($botId);
                $botId++;
            }
        }

        $CrateBattle->players = json_encode($player_list);
        $CrateBattle->save();
        broadcast(new CrateBattleCallBots($CrateBattle->id, $bots));
        $this->startGame($CrateBattle, $player_list);

        return json_encode(["error" => false, "data" => "call bots"]);
    }

    public function startGame($CrateBattle, $player_list) {
        $result = [];

        $CrateBattle->seed = ProvablyFair::generateBattleSeed();
        $CrateBattle->tie_float = ProvablyFair::generateCrateBattleFloat($CrateBattle->seed);

        if(count(array_filter($player_list)) == $this->getMaxPlayers($CrateBattle->battle_type)) {
            $crate_list = json_decode($CrateBattle->crates, true);

            foreach($crate_list as $index => $crate_id) {
                $Crate = Crate::find($crate_id);
                $chances = array_map(function($item){ return $item['chance']; }, $Crate->contents->toArray());
                $items = array_map(function($item){ return $item['skin_id']; }, $Crate->contents->toArray());
                for($i = 0; $i < count(array_filter($player_list)); $i++) {
                    $wonItemId = $this->determineItemOutcome($CrateBattle->seed, $chances, $items, $index+1, $i+1);
                    $result[$index][$i] = $wonItemId;
                }
            }

            $CrateBattle->result = json_encode($result);
            $CrateBattle->game_state = 1;
            $CrateBattle->save();

            CrateBattleRollResult::dispatch($CrateBattle, $result, 0)->delay(now()->addSeconds(2))->onQueue("battle");
        }
    }

    public function createCrateBattle(Request $request) {
        if(!Auth::check()) return json_encode(["error" => true, "data" => "Unauthorized"]);
        if(!$request->has("crates") || !$request->has("type") || !$request->has("options")) return json_encode(["error" => true, "data" => "Invalid request"]);

        $option = $request->get("options");
        $crates = $request->get("crates");
        $type = $request->get("type");
        $User = User::find(Auth::id());

        $total_price = 0;
        foreach($crates as $crate) {
            $Crate = Crate::find($crate);
            $total_price += $Crate->price;
        }

        if(!$User->hasBalance($total_price)) return json_encode(["error" => true, "data" => "Not enough balance"]);

        $User->balance -= $total_price;
        $CrateBattle = new CrateBattle();
        $CrateBattle->created_by = $User->id;
        $CrateBattle->game_state = 0;
        $CrateBattle->battle_type = $type;
        $CrateBattle->price = $total_price;
        $CrateBattle->crates = json_encode($crates);

        if(in_array($option, ["normal", "group", "terminal"])) {
            $CrateBattle->{"is_" . $option} = true;
        }

        $players = array_fill(0, CrateBattle::getNumberOfSpots($type), null);
        $players[0] = $User->id;
        $CrateBattle->players = json_encode($players);

        $CrateBattle->save();
        $User->save();

        broadcast(new CrateBattleCreated($CrateBattle));

        return json_encode(["error" => false, "data" => ['id' => $CrateBattle->id, 'price' => $CrateBattle->price]]);
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

    private function determineItemOutcome($seed, $chances, $items, $round, $player) {
        $rand = ProvablyFair::generateBattleTicket(str_pad($seed . ":" . $round . ":" . $player, 32, "\x00"));
        $cumulativeProbability = 0;
        foreach ($chances as $index => $chance) {
            $cumulativeProbability += 100000*($chance/100);
            if ($rand < $cumulativeProbability) {
                return $items[$index];
            }
        }
        return null; // Return null if no item is found
    }
}
