<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('surats', function (Blueprint $table) {
            $table->id();
            $table->integer('type');
            $table->boolean('isRahasia')->default(false);
            $table->string('kode')->nullable();
            $table->string('perihal');
            $table->string('tujuan');
            $table->boolean('isKonsumsi')->nullable();
            $table->boolean('isPengelolaan')->nullable();
            $table->boolean('isRuangan')->nullable()->default(0);
            $table->string('filepath')->nullable();
            $table->string('nomor')->nullable();
            $table->string('link')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('surats');
    }
};
