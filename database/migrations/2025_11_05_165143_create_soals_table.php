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
        Schema::create('soals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_id')->constrained('jenis_ujians')->onDelete('restrict');
            $table->string('pertanyaan_text')->nullable();
            $table->enum('media_type', ['none', 'image', 'audio'])->default('none');
            $table->string('media_path')->nullable();
            $table->string('skor')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('soals');
    }
};
