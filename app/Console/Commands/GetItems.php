<?php

namespace App\Console\Commands;

use App\Models\Skin;
use App\Models\Weapon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class GetItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'val:get-items';

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
        $client = new Client();
        $request = $client->get('https://valorant-api.com/v1/weapons');
        $body = json_decode($request->getBody()->getContents(), true);

        foreach ($body['data'] as $item) {
            Weapon::updateOrCreate([
                'uuid' => $item['uuid'],
            ],
            [
                'name' => $item['displayName'],
            ]);

            foreach ($item['skins'] as $skin){
                if ($skin['contentTierUuid'] == null){
                    continue;
                }
                Skin::updateOrCreate([
                    'uuid' => $skin['uuid'],
                ],
                [
                    'name' => $skin['displayName'],
                    'tier_uuid' => $skin['contentTierUuid'],
                    'weapon_uuid' => $item['uuid'],
                ]);
            }
        }
    }


}
