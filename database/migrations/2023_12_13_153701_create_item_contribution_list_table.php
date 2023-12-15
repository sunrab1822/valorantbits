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
        Schema::create('item_contribution_list', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("skin_id");
            $table->float("percentage");
            $table->float("contribution");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_contribution_list');
    }
};
