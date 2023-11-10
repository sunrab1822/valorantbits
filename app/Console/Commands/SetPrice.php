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

            $min_max = $this->getMinMax($Skin->tier->rank);
            $min = $min_max['min'];
            $max = $min_max['max'];

            $Skin->price = $this->calculatePrice($min, $max, $json_data[$key]['hype'], $json_data[$key]['appeal'], $this->getWeaponTypeMultiplier($Skin->category_id), $Skin->name);
            $Skin->save();

            echo $Skin->name . ": " . $Skin->price . "\r\n";
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
                return 1.3;
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
                return ['min' => 201, 'max' => 750];
            case 3:
                return ['min' => 751, 'max' => 3000];
            case 4:
                return ['min' => 3001, 'max' => 7000];
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
