<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kodes', function (Blueprint $table) {
            $table->id();
            $table->string('kode');
            $table->string('detail');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kodes');
    }
};
