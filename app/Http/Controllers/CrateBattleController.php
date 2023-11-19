<?php

namespace App\Http\Controllers;

use App\Models\CrateBattle;
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

    }
}
