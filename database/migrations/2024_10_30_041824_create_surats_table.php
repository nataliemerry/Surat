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
        Schema::create('surats', function (Blueprint $table) {
            $table->id();
            $table->integer('type');
            $table->date('date');
            $table->string('kode');
            $table->string('perihal');
            $table->string('tujuan');
            $table->boolean('isKonsumsi')->nullable();
            $table->boolean('isPengelolaan')->nullable();
            $table->string('filepath')->nullable();
            $table->string('nomor')->nullable();
            $table->string('link')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surats');
    }
};