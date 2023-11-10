<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CoinflipController extends Controller
{

    public function index() {

        return view("coinflips.coinflip");
    }

}
