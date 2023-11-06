<?php

namespace App\Console\Commands;

use App\Models\Skin;
use Illuminate\Console\Command;

class SetPrice extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'valo:price';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        $Skins = Skin::all();

        $appeal = 5;
        $hype = 7;
        $multiplier = 4;
        $divisor = 10;

        $json_data = json_decode(file_get_contents("data.json"), true);

        foreach($json_data as $name => $data) {
            if($data['hype'] == null || $data['appeal'] == null) continue;

            $Skins = Skin::where('name', 'LIKE', '%' . $name . '%')->where("category_id", "!=", "19")->get();

            foreach($Skins as $Skin) {
                $min_max = $this->getMinMax($Skin->tier->rank);
                $min = $min_max['min'];
                $max = $min_max['max'];

                $float_value = ($appeal + $hype) / ($multiplier * $divisor);

                $float_value *= $this->getWeaponTypeMultiplier($Skin->category_id);

                $price = round($min + $float_value * ($max - $min));

                $Skin->price = $price;
                $Skin->save();

                echo $Skin->name . ": " . $price . "\r\n";
            }
        }
    }

    public function getWeaponTypeMultiplier($weapon_type) {
        switch($weapon_type) {
            case 1:
                return 0.9;
            case 2:
                return 0.7;
            case 3:
                return 1.3;
            case 4:
                return 1;
            case 5:
                return 1.3;
            case 6:
                return 0.8;
            case 7:
                return 0.7;
            case 8:
                return 0.9;
            case 9:
                return 1;
            case 10:
                return 1;
            case 11:
                return 1.2;
            case 12:
                return 0.9;
            case 13:
                return 1.4;
            case 14:
                return 1.1;
            case 15:
                return 1;
            case 16:
                return 1;
            case 17:
                return 0.9;
            case 18:
                return 1.6;
            case 19:
                return 1;
        }
    }

    public function getMinMax($tier) {
        switch ($tier) {
            case 0:
                return ['min' => 1, 'max' => 50];
            case 1:
                return ['min' => 51, 'max' => 200];
            case 2:
                return ['min' => 201, 'max' => 800];
            case 3:
                return ['min' => 801, 'max' => 3000];
            case 4:
                return ['min' => 3001, 'max' => 8000];
        }
    }
}
