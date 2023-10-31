<?php

namespace App\Http\Controllers;

use App\Models\Crate;
use Illuminate\Http\Request;

class CrateController extends Controller
{
    public function index() {


        $crates = Crate::all();


        return view('crates')->with(['crates' => $crates]);
    }

}
