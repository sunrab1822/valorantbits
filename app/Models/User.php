<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'password',
        'profile_image'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function wager($wager_amount = null, $won_amount = null, $game_type, $game_id = null) {
        // Bot check
        if($this->id < 4) return;

        $date_time = Carbon::now();

        if($wager_amount == null) {
            DB::table("games_played")->where("game_id", $game_id)->where("game_type", $game_type)->update([
                "won_amount" => $won_amount,
                "updated_at" => $date_time
            ]);
        } else {
            DB::table("games_played")->insert([
                "user_id" => $this->id,
                "wager_amount" => $wager_amount,
                "won_amount" => $won_amount,
                "game_type" => $game_type,
                "game_id" => $game_id,
                "created_at" => $date_time,
                "updated_at" => $date_time
            ]);
        }
    }

    public function hasBalance($amount) {
        return $this->balance >= $amount;
    }

    public function hasBalanceVault($amount) {
        return $this->vault_balance >= $amount;
    }
}
