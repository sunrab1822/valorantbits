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
        Schema::create('coinflips', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("heads")->nullable();
            $table->unsignedBigInteger("tails")->nullable();
            $table->unsignedInteger("heads_amount")->nullable();
            $table->unsignedInteger("tails_amount")->nullable();
            $table->float("chance_float")->nullable();
            $table->integer("game_state")->default(0);
            $table->string("created_by");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coinflips');
    }
};
