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
        Schema::table('crate_items', function (Blueprint $table) {
            $table->float('chance')->after('skin_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('crate_items', function (Blueprint $table) {
            $table->dropColumn(['chance']);
        });
    }
};
