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
        Schema::create('tanaman_togas', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // Nama tanaman
            $table->text('manfaat'); // Manfaat dalam format teks HTML
            $table->text('pengolahan'); // Pengolahan dalam format teks HTML
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tanaman_togas');
    }
};
