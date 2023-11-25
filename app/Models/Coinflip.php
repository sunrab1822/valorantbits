<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coinflip extends Model
{
    use HasFactory;
    public function userHeads() {
        return $this->belongsTo(User::class, 'heads', 'id');
    }

    public function userTails() {
        return $this->belongsTo(User::class, 'tails', 'id');
    }

}
