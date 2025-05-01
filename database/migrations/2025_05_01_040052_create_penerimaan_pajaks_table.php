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
        Schema::create('penerimaan_pajaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('target_pajak_id')->constrained()->onDelete('cascade');
            $table->date('tanggal_penerimaan');
            $table->decimal('nilai_penerimaan', 20, 2);
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('penerimaan_pajaks');
    }
};
