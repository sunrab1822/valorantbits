<?php

namespace App\Http\Controllers;

use App\Enums\GameType;
use App\Events\CoinflipJoin;
use App\Models\Coinflip;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoinflipController extends Controller
{

    public function index() {

        return view("coinflips.coinflip");
    }

    public function joinGame(Request $request) {
        if(!$request->has("game_id") || !$request->has("bet_side") || !$request->has("bet_amount")) {
            return json_encode(["error" => true, "data" => "Missing Game Info"]);
        }

        $Coinflip = Coinflip::find($request->get("game_id"));
        $User = User::find(Auth::id());

        $bet_amount = $request->get("bet_amount");
        $bet_side = $request->get("bet_side");

        if(!$User->hasBalance($bet_amount)) return json_encode(["error" => true, "data" => "Not enough balance"]);

        if((in_array($bet_side, ["heads", "tails"])) && ($bet_side == "heads" && $Coinflip->heads != null) || ($bet_side == "tails" && $Coinflip->tails != null)) {
            return json_encode(["error" => true, "data" => "Could not join"]);
        }

        $User->balance -= $bet_amount;
        //$User->save();
        //$User->wager($bet_amount, null, GameType::Coinflip);
        $Coinflip->{$bet_side} = $User->id;
        $Coinflip->{$bet_side . "_amount"} = $bet_amount;

        if($Coinflip->heads != null && $Coinflip->tails != null) {
            $Coinflip->game_state = 1;
            broadcast(new CoinflipJoin($Coinflip->id, $User, $bet_side));
        }

        return json_encode(["error" => false, "data" => "joined"]);
    }
}
