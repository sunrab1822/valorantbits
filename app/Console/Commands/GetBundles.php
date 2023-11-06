<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;

class GetBundles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'valo:bundles';

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
        $request = $client->get('https://valorant-api.com/v1/bundles');
        $body = json_decode($request->getBody()->getContents(), true);

        $json_data = [];

        foreach ($body['data'] as $item) {
            $json_data[$item['displayName']] = ['image' => $item['displayIcon'], 'hype' => null, 'appeal' => null];
        }

        file_put_contents("data.json", json_encode($json_data, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES));
    }
}
