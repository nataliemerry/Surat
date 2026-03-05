<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('letter_sequences', function (Blueprint $table) {
            $table->id();
            $table->enum('group', ['tugas', 'gabungan']);
            $table->smallInteger('year')->unsigned();
            $table->integer('last_sequence')->unsigned()->default(0);
            $table->unique(['group', 'year']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letter_sequences');
    }
};
