<?php

namespace App\Http\Controllers;

use App\Enums\GameType;
use App\Events\CoinflipCreated;
use App\Events\CoinflipJoin;
use App\Jobs\CloseCoinflip;
use App\Models\Coinflip;
use App\Models\ProvablyFair;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CoinflipController extends Controller
{

    public function index() {

        return view("coinflips.coinflip");
    }

    public function joinGame(Request $request) {
        if(!$request->has("game_id") || !$request->has("bet_amount")) {
            return json_encode(["error" => true, "data" => "Missing Game Info"]);
        }

        $Coinflip = Coinflip::find($request->get("game_id"));
        $User = User::find(Auth::id());

        $bet_amount = $request->get("bet_amount");
        $bet_side = $Coinflip->heads == null ? "heads" : "tails";

        if(!$User->hasBalance($bet_amount)) return json_encode(["error" => true, "data" => "Not enough balance"]);

        if((!in_array($bet_side, ["heads", "tails"])) || ($bet_side == "heads" && $Coinflip->heads != null) ||
            ($bet_side == "tails" && $Coinflip->tails != null) || $Coinflip->heads == Auth::user()->id || $Coinflip->tails == Auth::user()->id)
        {
            return json_encode(["error" => true, "data" => "Could not join"]);
        }

        $User->balance -= $bet_amount;
        $User->save();
        //$User->wager($bet_amount, null, GameType::Coinflip);
        $Coinflip->{$bet_side} = $User->id;
        $Coinflip->{$bet_side . "_amount"} = $bet_amount;

        $total_amount = $Coinflip->{$bet_side . "_amount"} + $Coinflip->{$Coinflip->created_by . "_amount"};
        $Coinflip->created_float = $Coinflip->{$Coinflip->created_by . "_amount"} / $total_amount;

        if($Coinflip->heads != null && $Coinflip->tails != null) {
            $Coinflip->chance_float = ProvablyFair::generateCoinflipFloat("", $Coinflip->seed);
            $Coinflip->game_state = 1;
            $Coinflip->save();
            broadcast(new CoinflipJoin($Coinflip, $User, $bet_side));
            CloseCoinflip::dispatch($Coinflip)->delay(now()->addSeconds(5));
            return json_encode(["error" => false, "data" => "joined"]);
        }

        return json_encode(["error" => true, "data" => "Unknown Error"]);
    }

    public function callBots(Request $request) {
        if(!$request->has("game_id")) {
            return json_encode(["error" => true, "data" => "Missing Game Info"]);
        }

        $Coinflip = Coinflip::find($request->get("game_id"));
        $bet_side = $Coinflip->heads == null ? "heads" : "tails";

        if((in_array($bet_side, ["heads", "tails"])) && ($bet_side == "heads" && $Coinflip->heads != null) || ($bet_side == "tails" && $Coinflip->tails != null)) {
            return json_encode(["error" => true, "data" => "Could not call bots"]);
        }

        $User = User::find(1);

        $Coinflip->{$bet_side} = $User->id;
        $Coinflip->{$bet_side . "_amount"} = $Coinflip->{$Coinflip->created_by . "_amount"} * (1 + (mt_rand(5, 10) / 100));

        $total_amount = $Coinflip->{$bet_side . "_amount"} + $Coinflip->{$Coinflip->created_by . "_amount"};
        $Coinflip->created_float = $Coinflip->{$Coinflip->created_by . "_amount"} / $total_amount;

        if($Coinflip->heads != null && $Coinflip->tails != null) {
            $Coinflip->chance_float = ProvablyFair::generateCoinflipFloat("", $Coinflip->seed);
            $Coinflip->game_state = 1;
            $Coinflip->save();
            broadcast(new CoinflipJoin($Coinflip, $User, $bet_side));
            CloseCoinflip::dispatch($Coinflip)->delay(now()->addSeconds(5));
            return json_encode(["error" => false, "data" => "joined"]);
        }

        return json_encode(["error" => true, "data" => "Unknown Error"]);
    }

    public function getCoinflips() {
        $coinflips = Coinflip::whereIn('game_state', [0,1])->get();

        foreach($coinflips as $coinflip){
            if($coinflip->heads){
                $coinflip->userHeads;
            }
            if($coinflip->tails){
                $coinflip->userTails;
            }
        }

        return json_encode(["error" => false, "data" => $coinflips]);
    }

    public function createCoinflip(Request $request) {
        try {
            $this->validate($request, [
                'bet_amount' => 'required|min:1|max:' . Auth::user()->balance,
                'bet_side' => ['required', Rule::in(["heads", "tails"])]
            ]);
        } catch (ValidationException $ex) {
            return json_encode(["error" => true, "error_type" => "validation", "data" => $ex->errors()]);
        }

        if(!$request->has("bet_side") || !$request->has("bet_amount")) {
            return json_encode(["error" => true, "data" => "Missing Game Info"]);
        }


        $User = User::find(Auth::id());

        $bet_amount = $request->get("bet_amount");
        $bet_side = $request->get("bet_side");

        if(!$User->hasBalance($bet_amount)) return json_encode(["error" => true, "data" => "Not enough balance"]);

        $coinflip = new Coinflip();

        $User->balance -= $bet_amount;
        $User->save();
        //$User->wager($bet_amount, null, GameType::Coinflip);
        $coinflip->{$bet_side} = $User->id;
        $coinflip->{$bet_side . "_amount"} = $bet_amount;
        $coinflip->created_by = $bet_side;
        $coinflip->seed = ProvablyFair::generateCoinflipSeed();
        $coinflip->save();

        broadcast(new CoinflipCreated($coinflip));

        return json_encode(["error" => false, "data" => $coinflip->id]);
    }

    function getCoinflip($id) {

        $coinflip = Coinflip::find($id);

        if (!$coinflip){
            return json_encode(["error" => true, "data" => "Game not found"]);
        }

        if($coinflip->heads){
            $coinflip->userHeads;
        }
        if($coinflip->tails){
            $coinflip->userTails;
        }

        return json_encode(["error" => false, "data" => $coinflip]);
    }

}
