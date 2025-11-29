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
        Schema::create('outgoing_letters', function (Blueprint $table) {
            $table->id();
            $table->string('no_surat')->nullable();
            $table->string('tujuan')->nullable(); // Penerima surat keluar
            $table->date('tanggal_surat');
            $table->string('perihal');
            $table->foreignId('kategori_id')->constrained('categories')->onDelete('cascade');
            $table->string('file_path')->nullable();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outgoing_letters');
    }
};