<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class GetProfileImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'valo:get-profile-images';

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
        $request = $client->get('https://valorant-api.com/v1/agents');
        $body = json_decode($request->getBody()->getContents(), true);

        foreach ($body['data'] as $item) {
            if($item['isPlayableCharacter']) {
                $imageContent = file_get_contents($item['displayIcon']);

                Storage::disk("public")->put("profile_images/" . $item["uuid"] . ".png", $imageContent);
            }
        }
    }
}
