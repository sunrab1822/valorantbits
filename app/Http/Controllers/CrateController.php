<?php

namespace App\Http\Controllers;

use App\Enums\GameType;
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

        return response(json_encode(["error" => false, "data" => $crates]));
    }


    /**
     * @deprecated
     */
    public function getCrate($crate_id) {
        $Crate = Crate::find($crate_id);
        $Crate->contents;

        foreach ($Crate->contents as $content){
            if($content->chance <= 3) {
                $content->tier = "ultra";
                continue;
            } else if($content->chance <= 8) {
                $content->tier = "exclusive";
                continue;
            } else if($content->chance <= 20) {
                $content->tier = "premium";
                continue;
            } else if($content->chance <= 50) {
                $content->tier = "deluxe";
                continue;
            } else if($content->chance <= 100) {
                $content->tier = "select";
                continue;
            }
        }

        return response(json_encode(["error" => false, "data" => $Crate]));
    }

    function getServerHash() {

    }

    public function openCrate(Request $request) {
        if(!Auth::check()) {
            return response(json_encode(["error" => true, "data" => "Unauthorized"]));
        }

        $clientSeed = $request->get("clientSeed");
        $case_id = $request->get("id");

        $Crate = Crate::find($case_id);
        $User = User::find(Auth::id());

        if($Crate) {
            if($User->balance < $Crate->price) {
                return ["error" => true, "data" => "Not enough balance"];
            }

            $User->balance -= $Crate->price;

            $chances = array_map(function($item){ return $item['chance']; }, $Crate->contents->toArray());
            $items = array_map(function($item){ return $item['skin_id']; }, $Crate->contents->toArray());

            $wonItemId = $this->determineItemOutcome($chances, $items);

            $wonItem = Skin::find($wonItemId);

            $User->balance += $wonItem->price;
            $User->save();

            $User->wager($Crate->price, $wonItem->price, GameType::Crates);

            return response(["error" => false, "data" => ["drop" => $wonItem]]);
        }

        return response(["error" => true, "data" => "An error has occured."]);
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
