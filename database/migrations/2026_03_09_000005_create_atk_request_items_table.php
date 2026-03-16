<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atk_request_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_id');
            $table->unsignedBigInteger('item_id');
            $table->integer('qty_requested');
            $table->integer('qty_approved')->nullable();
            $table->timestamps();

            $table->foreign('request_id')->references('id')->on('atk_requests')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('atk_items')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atk_request_items');
    }
};
