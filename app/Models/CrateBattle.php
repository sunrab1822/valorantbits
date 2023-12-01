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
}
