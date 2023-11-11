<?php

namespace App\Http\Controllers;

use App\Models\Crate;
use App\Models\Skin;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CrateController extends Controller
{
    public function index() {
        $crates = Crate::all();

        return view('crates/index')->with(['crates' => $crates]);
    }

    public function view($id) {
        $crate = Crate::find($id);
        $crate->contents;
        foreach ($crate->contents as $content){
            $content->tier;
        }
        return view('crates/view')->with(['crate' => $crate]);
    }

    public function getCrateList() {
        $crates = Crate::all();

        return response(json_encode(["status" => 200, "data" => $crates]));
    }


    /**
     * @deprecated
     */
    public function getCrateContents($crate_id) {
        $Crate = Crate::find($crate_id);
        $Crate->contents;

        return response(json_encode(["status" => 200, "data" => $Crate]));
    }

    function getServerHash() {

    }

    public function openCrate(Request $request) {
        if(!Auth::check()) {
            return response(json_encode(["status" => 403, "data" => "Unauthorized"]));
        }

        $clientSeed = $request->get("clientSeed");
        $case_id = $request->get("id");

        $Crate = Crate::find($case_id);
        $User = User::find(Auth::id());

        if($Crate) {
            if($User->balance < $Crate->price) {
                return ["status" => "500", "data" => "Not enough balance"];
            }

            $User->balance -= $Crate->price;

            $chances = array_map(function($item){ return $item['chance']; }, $Crate->contents->toArray());
            $items = array_map(function($item){ return $item['skin_id']; }, $Crate->contents->toArray());

            $wonItemId = $this->determineItemOutcome($chances, $items);

            $wonItem = Skin::find($wonItemId);

            $User->balance += $wonItem->price;
            $User->save();

            return response(["status" => 200, "data" => ["drop" => $wonItem]]);
        }

        return response(["status" => 404, "data" => "An error has occured."]);
    }

    function determineItemOutcome($chances, $items) {
        $rand = rand(0, 10000);
        $cumulativeProbability = 0;
        foreach ($chances as $index => $chance) {
            $cumulativeProbability += 10000*($chance/100);
            if ($rand < $cumulativeProbability) {
                return $items[$index];
            }
        }
        return null; // Return null if no item is found
    }
}
