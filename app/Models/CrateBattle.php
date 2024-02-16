<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrateBattle extends Model
{
    use HasFactory;



    public static function getNumberOfSpots($type) {
        switch($type) {
            case 1:
                return 2;
            case 2:
                return 3;
            case 3:
            case 4:
                return 4;
        }
    }

    public function determineItemOutcome($seed, $chances, $items, $round, $player) {
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
