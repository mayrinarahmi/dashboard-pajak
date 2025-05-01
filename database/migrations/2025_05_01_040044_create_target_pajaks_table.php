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
        Schema::create('target_pajaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_anggaran_id')->constrained();
            $table->foreignId('kode_rekening_id')->constrained();
            $table->decimal('pagu_anggaran', 20, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['tahun_anggaran_id', 'kode_rekening_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('target_pajaks');
    }
};
