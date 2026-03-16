<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atk_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->string('name');
            $table->string('satuan');
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('atk_categories')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atk_items');
    }
};
