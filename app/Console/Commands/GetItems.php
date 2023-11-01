<?php

namespace App\Console\Commands;

use App\Models\Skin;
use App\Models\Weapon;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

use function Ramsey\Uuid\v4;

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

    protected $spray_uuid = "5bb0b1ff-f288-4177-ad2d-750be7b49f95";

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

        $client = new Client();
        $request = $client->get('https://valorant-api.com/v1/sprays');
        $body = json_decode($request->getBody()->getContents(), true);

        $category = Weapon::firstOrCreate([
            'uuid' => $this->spray_uuid,
        ],
        [
            'name' => "Spray"
        ]);

        foreach ($body['data'] as $spray){
            Skin::updateOrCreate([
                'uuid' => $spray['uuid'],
            ],
            [
                'name' => $spray['displayName'],
                'category' => $category->id,
            ]);
        }


    }


}
