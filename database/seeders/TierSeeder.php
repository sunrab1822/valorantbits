<?php

namespace Database\Seeders;

use App\Models\Tier;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use function Laravel\Prompts\table;

class TierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Tier::insert([
            [
                "uuid" => "0cebb8be-46d7-c12a-d306-e9907bfc5a25",
                "name" => "Deluxe Edition",
                "devName" => "Deluxe",
                "rank" => 1,
                "color" => "009587",
            ],
            [
                "uuid" => "e046854e-406c-37f4-6607-19a9ba8426fc",
                "name" => "Deluxe Edition",
                "devName" => "Exclusive",
                "rank" => 3,
                "color" => "f5955b",
            ],
            [
                "uuid" => "60bca009-4182-7998-dee7-b8a2558dc369",
                "name" => "Premium Edition",
                "devName" => "Premium",
                "rank" => 2,
                "color" => "d1548d",
            ],
            [
                "uuid" => "12683d76-48d7-84a3-4e09-6985794f0445",
                "name" => "Select Edition",
                "devName" => "Select",
                "rank" => 0,
                "color" => "5a9fe2",
            ],
            [
                "uuid" => "411e4a55-4e59-7757-41f0-86a53f101bb5",
                "name" => "Ultra Edition",
                "devName" => "Ultra",
                "rank" => 4,
                "color" => "fad663",
            ]
        ]);
    }
}
