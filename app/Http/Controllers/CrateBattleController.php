<?php

namespace App\Http\Controllers;

use App\Models\Crate;
use App\Models\CrateBattle;
use App\Models\User;
use Illuminate\Http\Request;

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
}
