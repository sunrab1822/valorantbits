<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skin extends Model
{
    protected $hidden = ['laravel_through_key'];

    use HasFactory;
    protected $fillable = ['name', 'uuid', 'tier_id', 'category_id', 'image'];

    public function tier() {
        return $this->belongsTo(Tier::class);
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }
}
