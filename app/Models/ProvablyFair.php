<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Random\Engine\Xoshiro256StarStar;
use Random\Randomizer;

class ProvablyFair extends Model
{
    use HasFactory;

    protected $seed = null;
    protected $random = null;

    function __construct(int $seed)
    {
        $this->seed = $seed;
        $this->random = new Randomizer(new Xoshiro256StarStar($seed));
    }

    public static function generateBattleSeed() {
        $lowercase = "abcdefghijklmnopqrstuvwxyz";
        $uppercase = strtoupper($lowercase);
        $numbers = "0123456789";
        $alphabet = $lowercase . $uppercase . $numbers;
        $result = "";
        for($i = 0; $i < 32; $i++) {
            $result .= $alphabet[random_int(0, strlen($alphabet)-1)];
        }

        return $result;
    }

    public static function generateCoinflipSeed() {
        $lowercase = "abcdefghijklmnopqrstuvwxyz";
        $uppercase = strtoupper($lowercase);
        $numbers = "0123456789";
        $alphabet = $lowercase . $uppercase . $numbers;
        $result = "";

        for($i = 0; $i < 16; $i++) {
            $result .= $alphabet[random_int(0, strlen($alphabet)-1)];
            if($i % 2 == 0){
                $result .= $lowercase[random_int(0, strlen($lowercase)-1)] . $numbers[random_int(0, strlen($numbers)-1)];
            }
        }

        return $result;
    }

    public static function generateCoinflipFloat($client_seed, $server_seed) {
        //$hash = hash('sha256', $server_seed . ":" . $client_seed);
        $random = new Randomizer(new Xoshiro256StarStar($server_seed));

        return $random->getInt(0, 999999) / 999999;
    }

    public function generateBattleTicket() {
        //$hash = hash('sha256', $server_seed . ":" . $client_seed);
        return $this->random->getInt(0, 10000);
    }

    public static function generateCrateBattleFloat($server_seed) {
        //$hash = hash('sha256', $server_seed . ":" . $client_seed);
        $random = new Randomizer(new Xoshiro256StarStar($server_seed));

        return $random->getInt(0, 999999) / 999999;
    }
}
