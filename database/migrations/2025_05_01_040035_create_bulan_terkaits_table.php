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
        Schema::create('bulan_terkaits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('target_kelompok_id')->constrained()->onDelete('cascade');
            $table->integer('bulan'); // 1-12
            $table->timestamps();

            $table->unique(['target_kelompok_id', 'bulan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bulan_terkaits');
    }
};
