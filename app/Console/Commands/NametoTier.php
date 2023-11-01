<?php

namespace App\Console\Commands;

use App\Models\Skin;
use App\Models\Tier;
use Illuminate\Console\Command;

class NametoTier extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'val:nameto-tier';

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
        $skins = Skin::all();

        $file = fopen("skinsandtires.txt", "w");
        foreach ($skins as $skin) {

            $skinAndTier = $skin->name . " -> " . Tier::where("uuid", "=", $skin->tier_uuid)->first()->devName;

            fwrite($file, $skinAndTier . "\r\n");
        }
        fclose($file);

    }
}
