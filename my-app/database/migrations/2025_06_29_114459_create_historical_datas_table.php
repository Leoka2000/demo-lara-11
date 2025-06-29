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
        Schema::create('historical_datas', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('charge_level');
            $table->float('voltage', 6, 1.5);
            $table->integer('temperature');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historical_datas');
    }
};
