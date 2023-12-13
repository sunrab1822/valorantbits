<?php

namespace App\Console\Commands;

use App\Models\Crate;
use App\Models\CrateItems;
use App\Models\Skin;
use Illuminate\Console\Command;

use function Psy\bin;

class FiletoCrate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'valo:crates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $caseTypes = [
        'low' => 0,
        'medium' => 0,
        'high' => 0,
        'fifty_fifty' => 0,
        'one_percent' => 0
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $crates = json_decode(file_get_contents("crates.json"), true);
        $crateId = null;

        foreach($crates as $crate) {
            switch($crate["type"]) {
                case "1%":
                    createOnePercentCrate();
                    break;
                case "10%":
                    createTenPercentCrate();
                    break;
                case "50/50":
                    createFiftyFiftyCrate();
                    break;
                case "high":
                    createHighRiskCrate();
                    break;
                case "medium":
                    createMediumRiskCrate();
                    break;
                case "low":
                    $this->createLowRiskCrate();
                    break;
            }
        }

        if (isset($arguments['file']))
        {
            $file = fopen($arguments['file'], 'r');
            $crate = null;
            $crate_price = 0;

            while (($line = fgets($file)) !== false){
                $line = trim($line);
                if ($line == "")
                {
                    if($crate != null) {
                        $crate->price = $crate_price;
                        $crate->save();
                    }
                    continue;
                }

                print_r($line . "\r\n");
                if(str_starts_with($line, '*')){
                    $crate_price = 0;
                    $crate = new Crate();
                    $crate->name = substr($line, 1, strlen($line)-2);
                    $crate->price = $crate_price;
                    $crate->image = "/storage/crate_images/crate_red.png";
                    $crate->save();
                    $crateId = $crate->id;
                    continue;
                }

                if($crateId != null)
                {
                    $bits = explode(',', $line);
                    $skin = Skin::where("name", $bits[0])->first();

                    $crateItem = new CrateItems();
                    $crateItem->crate_id = $crateId;
                    $crateItem->skin_id = $skin->id;
                    $crateItem->chance = $bits[1];
                    $crateItem->save();

                    $calculated_price = round((($skin->price / 100) * 0.07 + ($skin->price / 100)) * ($crateItem->chance / 100), 2) * 100 + 1;
                    print_r($calculated_price . "\r\n");

                    $crate_price += $calculated_price;
                }
            }
        }
    }

    function createLowRiskCrate() {
        $target_price = 30;
        $random_num = mt_rand(4, 10);
        for($i = 0; $i < $random_num; $i++) {
            Skin::where("category_id")->order_by("price", "ASC");
        }
    }
}
