<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(["username" => "Jett", "is_bot" => true], ["password" => null, "email" => "jett@valorantbets.com", "profile_image" => "/storage/profile_images/add6443a-41bd-e414-f6ad-e58d267f4e95.png"]);
        User::updateOrCreate(["username" => "Sage", "is_bot" => true], ["password" => null, "email" => "sage@valorantbets.com", "profile_image" => "/storage/profile_images/569fdd95-4d10-43ab-ca70-79becc718b46.png"]);
        User::updateOrCreate(["username" => "Viper", "is_bot" => true], ["password" => null, "email" => "viper@valorantbets.com", "profile_image" => "/storage/profile_images/707eab51-4836-f488-046a-cda6bf494859.png"]);
    }
}
