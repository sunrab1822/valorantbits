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
    protected $signature = 'val:fileto-crate {file}';

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
        $arguments = $this->arguments();
        $crateId = null;

        if (isset($arguments['file']))
        {
            $file = fopen($arguments['file'], 'r');

            while (($line = fgets($file)) !== false){
                $line = trim($line);
                if ($line == "")
                {
                    continue;

                }

                print_r($line);
                if(str_starts_with($line, '*')){
                    $crate = new Crate();
                    $crate->name = substr($line, 1, strlen($line)-1);
                    $crate->price = 0;
                    $crate->save();
                    $crateId = $crate->id;
                    continue;
                }

                if($crateId != null)
                {
                    $bits = explode(',' ,$line);
                    $skin = Skin::where("name", $bits[0])->first();

                    $crateItem = new CrateItems();
                    $crateItem->crate_id = $crateId;
                    $crateItem->skin_id = $skin->id;
                    $crateItem->chance = $bits[1];
                    $crateItem->save();
                }

            }
        }

    }
}
