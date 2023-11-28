<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProvablyFair extends Model
{
    use HasFactory;

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
}
