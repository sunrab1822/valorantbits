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
        Schema::table('coinflips', function (Blueprint $table) {
            $table->float("created_float", 8, 7)->nullable()->after("chance_float");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coinflips', function (Blueprint $table) {
            $table->dropColumn(["created_float"]);
        });
    }
};
