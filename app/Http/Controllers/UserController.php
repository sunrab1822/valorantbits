<?php

namespace App\Http\Controllers;

use App\Enums\GameType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index() {
        return view('user.profile', ["user" => Auth::user()]);
    }

    public function getUserProfile() {
        if(!Auth::check()) return json_encode(["error" => true, "data" => "Access denied"]);

        $most_won_game_sql = DB::table("games_played")->select(["game_type", DB::raw("COUNT(game_type)")])->where("user_id", "=", Auth::user()->id)->orderBy("won_amount")->groupBy("game_type")->first();
        $most_played_game_sql = DB::table("games_played")->select(["game_type", DB::raw("COUNT(game_type) as game_type_count")])->where("user_id", "=", Auth::user()->id)->orderBy("game_type_count")->groupBy("game_type")->first();
        $wagered = DB::table("games_played")->where("user_id", "=", Auth::user()->id)->sum("wager_amount");
        $top_win_sql = DB::table("games_played")->select("won_amount")->where("user_id", "=", Auth::user()->id)->orderBy("won_amount")->first();
        $profit_array = DB::select(DB::raw("SELECT @b := @b + gp.won_amount-gp.wager_amount AS profit_amount FROM (SELECT @b := 0.0) AS dummy CROSS JOIN `games_played` AS gp WHERE user_id = :user_id")->getValue(DB::connection()->getQueryGrammar()), [
            'user_id' => Auth::user()->id
        ]);

        $most_won_game = isset($most_won_game_sql) ? GameType::fromValue($most_won_game_sql->game_type)->description : "---";
        $most_played_game = isset($most_played_game_sql) ? GameType::fromValue($most_played_game_sql->game_type)->description : "---";

        return json_encode(["error" => false, "data" => [
            "wagered" => $wagered,
            "top_win" => isset($top_win_sql) ? $top_win_sql->won_amount : 0,
            "most_won_game" => $most_won_game,
            "most_played_game" => $most_played_game,
            "profit" => end($profit_array)->profit_amount,
            "profit_data" => $profit_array
        ]]);
    }

    public function getProfitChartData(Request $request) {
        if(!Auth::check()) return json_encode(["error" => true, "data" => "Access denied"]);

        if(!$request->has('days')) return json_encode(["error" => true, "data" => "Invalid request"]);

        $sql = "SELECT @b := @b + gp.won_amount-gp.wager_amount AS profit_amount FROM (SELECT @b := 0.0) AS dummy CROSS JOIN `games_played` AS gp WHERE user_id = :user_id";
        $sql_values = [
            'user_id' => Auth::user()->id
        ];

        if($request->get('days') != 'all') {
            $sql .= " AND created_at > :created_at";
            $sql_values['created_at'] = Carbon::today()->subDays($request->get('days')) ;
        }

        $profit_array = DB::select(DB::raw($sql)->getValue(DB::connection()->getQueryGrammar()), $sql_values);

        return json_encode(["error" => false, "data" => $profit_array]);
    }

    public function getUser() {
        if(!Auth::check()) return json_encode(['error' => true, 'data' => "User not found"]);
        return json_encode(['error' => false, 'data' => Auth::user()]);
    }

    public function vaultDeposit(Request $request) {
        if(!Auth::check()) return json_encode(['error' => true, 'data' => "You are not logged in"]);
        if(!$request->has("amount")) return json_encode(['error' => true, 'data' => "Invalid request"]);
        $User = User::find(Auth::id());

        $amount_to_deposit = $request->get("amount") * 100;
        if(!$User->hasBalance($amount_to_deposit)) return json_encode(['error' => true, 'data' => "Not enough balance to deposit"]);
        $User->balance -= $amount_to_deposit;
        $User->vault_balance += $amount_to_deposit;
        $User->save();
        return json_encode(['error' => false, 'data' => [
            "message" => number_format($request->get("amount"), 2) . " has been deposited to Vault",
            "balance" => $User->balance,
            "vault_balance" => $User->vault_balance
        ]]);
    }

    public function vaultWithdraw(Request $request) {
        if(!Auth::check()) return json_encode(['error' => true, 'data' => "You are not logged in"]);
        if(!$request->has("amount")) return json_encode(['error' => true, 'data' => "Invalid request"]);
        $User = User::find(Auth::id());

        $amount_to_withdraw = $request->get("amount") * 100;
        if(!$User->hasBalanceVault($amount_to_withdraw)) return json_encode(['error' => true, 'data' => "Not enough balance to withdraw"]);
        $User->balance += $amount_to_withdraw;
        $User->vault_balance -= $amount_to_withdraw;
        $User->save();
        return json_encode(['error' => false, 'data' => [
            "message" => number_format($request->get("amount"), 2) . " has been withdrawn from Vault",
            "balance" => $User->balance,
            "vault_balance" => $User->vault_balance
        ]]);
    }
}
