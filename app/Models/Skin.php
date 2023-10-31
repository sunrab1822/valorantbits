<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Skin extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'uuid', 'tier_uuid', 'weapon'];

}
