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
        $json_data = json_decode(file_get_contents("data.json"), true);
        $skin_names = array_keys($json_data);

        $Skins = Skin::where("category_id", "!=", "19")->get();
        $key = "";

        foreach($Skins as $Skin) {
            if(in_array($Skin->name, $skin_names)) {
                $key = $Skin->name;
            } else if(in_array(str_replace(" " . $Skin->category->name, "", $Skin->name), $skin_names)) {
                $key = str_replace(" " . $Skin->category->name, "", $Skin->name);
            }

            if(!isset($key)) continue;

            $min_max = $this->getMinMax($Skin->category_id);
            $min = $min_max['min'];
            $max = $min_max['max'];

            $Skin->price = $this->calculatePrice($min, $max, $json_data[$key]['hype'], $json_data[$key]['appeal'], $this->getMultiplier($Skin->tier->rank), $Skin->name);
            $Skin->save();

            echo $Skin->name . ": " . $Skin->price . "\r\n";
        }
    }

    public function getMinMax($weapon_type) {
        switch($weapon_type) {
            case 1: //odin
                return ['min' => 2300, 'max' => 3500];
            case 2: //ares
                return ['min' => 300, 'max' => 1000];
            case 3: //vandal
                return ['min' => 3900, 'max' => 5800];
            case 4: //bulldog
                return ['min' => 980, 'max' => 2400];
            case 5: //phantom
                return ['min' => 3900, 'max' => 5800];
            case 6: //judge
                return ['min' => 500, 'max' => 1200];
            case 7: //bucky
                return ['min' => 50, 'max' => 800];
            case 8: //frenzy
                return ['min' => 15, 'max' => 100];
            case 9: //classic
                return ['min' => 0.1, 'max' => 80];
            case 10: //ghost
                return ['min' => 400, 'max' => 1400];
            case 11: //sherif
                return ['min' => 3000, 'max' => 4700];
            case 12: //shorty
                return ['min' => 70, 'max' => 500];
            case 13: //OP
                return ['min' => 5090, 'max' => 7300];
            case 14: //guardian
                return ['min' => 2300, 'max' => 3500];
            case 15: //marshal
                return ['min' => 1700, 'max' => 2800];
            case 16: //spectre
                return ['min' => 1500, 'max' => 2600];
            case 17: //stinger
                return ['min' => 100, 'max' => 990];
            case 18: //melee
                return ['min' => 6500, 'max' => 8000];
            case 19: //spray
                return ['min' => 0.1, 'max' => 10];
        }
    }

    public function getMultiplier($tier) {
        switch ($tier) {
            case 0:
                return 0.1;
            case 1:
                return 0.7;
            case 2:
                return 1;
            case 3:
                return 1.3;
            case 4:
                return 1.8;
        }
    }

    public function calculatePrice($min, $max, $hype, $appeal, $weaponCategoryMultiplier, $name) {
        $x = [0 => 10, 1 => 25000];
        $y = [0 => 0.5, 1 => 5000];

        $a = $appeal;
        $b = $hype;
        $aW = 0.6;
        $bW = 0.4;
        $avg = $this->getWeightedAverage($a, $b, $aW, $bW);
        $normal_scale = (10/9)*$avg - 10/9;

        $base_value = ($max-$min)/10 * $normal_scale + $min;
        $deviation = $this->getDeviation($x, $y, $base_value);

        $min_price = $base_value - $deviation;
        $max_price = $base_value + $deviation;

        mt_srand(crc32($name) % 1000 + 1000);
        $rand = mt_rand() / mt_getrandmax();

        return round((($max_price-$min_price)/10 * ($rand * 10) + $min_price) * $weaponCategoryMultiplier, 2) * 100;
    }

    public function getDeviation($x, $y, $base_value) {
        // Find A and B in quadratic formula with 2 points
        $x[2] = ($x[0]**2) * ($x[1] / $x[0]);
        $y[2] = $y[0] * ($x[1] / $x[0]);

        $a = ($y[1] - $y[2]) / ($x[1]**2 - $x[2]);
        $b = ($y[0] - ($a * $x[0]**2)) / $x[0];

        return $a*$base_value**2 + $b*$base_value;
    }

    public function getWeightedAverage($a, $b, $aW, $bW) {
        return $aW * $a + $bW * $b;
    }
}
