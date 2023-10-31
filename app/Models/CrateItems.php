<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrateItems extends Model
{
    use HasFactory;

    public function getCrate() {
        return $this->hasMany(Skin::class);
    }
}
