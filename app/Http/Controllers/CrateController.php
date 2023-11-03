<?php

namespace App\Http\Controllers;

use App\Models\Crate;
use App\Models\CrateItems;
use Illuminate\Http\Request;

class CrateController extends Controller
{
    public function index() {
        $crates = Crate::all();

        return view('crates/index')->with(['crates' => $crates]);
    }

    public function view($id) {
        $crate = Crate::find($id);
        $crate->contents;
        return view('crates/view')->with(['crate' => $crate]);
    }

    function getCrateList() {
        $crates = Crate::all();

        return response(json_encode(["status" => 200, "data" => $crates]));
    }

    function getCrateContents($crate_id) {
        $Crate = Crate::find($crate_id);
        $Crate->contents;

        return response(json_encode(["status" => 200, "data" => $Crate]));
    }
}
