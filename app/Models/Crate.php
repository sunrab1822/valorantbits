<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Crate extends Model
{
    use HasFactory;

    public function contents() {
        return $this->hasManyThrough(Skin::class, CrateItems::class, 'crate_id', 'id', 'id', 'skin_id')->select('*');
    }

    public function crate_items() {
        return $this->hasMany(CrateItems::class);
    }
}
