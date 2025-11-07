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
        Schema::create('sesi_soals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesi_ujian_id')
                ->constrained('sesi_ujian')
                ->cascadeOnDelete();
            $table->foreignId('soal_id')
                ->constrained('soal')
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sesi_soals');
    }
};
