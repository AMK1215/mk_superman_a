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
        Schema::table('with_draw_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('before_amount')->nullable();
            $table->unsignedBigInteger('after_amount')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('with_draw_requests', function (Blueprint $table) {
            //
        });
    }
};
