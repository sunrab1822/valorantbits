<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\DB;

trait WagerTrait {

    public function wager($id, $wager_amount, $won_amount) {
        DB::table("played_games")->insert(["user_id" => $id, "wager_amount" => $wager_amount, "won_amount" => $won_amount]);
    }
}
