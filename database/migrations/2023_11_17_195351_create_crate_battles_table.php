<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('crate_battles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("created_by");
            $table->unsignedBigInteger("price");
            $table->integer("battle_type")->default(1);
            $table->boolean("is_normal")->default(true);
            $table->boolean("is_crazy")->default(false);
            $table->boolean("is_terminal")->default(false);
            $table->boolean("is_group")->default(false);
            $table->boolean("game_state")->default(0);
            $table->string("seed");
            $table->json("crates");
            $table->json("players");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('crate_battles');
    }
};
