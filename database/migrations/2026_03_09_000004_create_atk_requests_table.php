<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('atk_requests', function (Blueprint $table) {
            $table->id();
            $table->string('requester_name');
            $table->unsignedBigInteger('team_id');
            $table->string('activity');
            $table->enum('status', ['pending', 'approved'])->default('pending');
            $table->timestamps();

            $table->foreign('team_id')->references('id')->on('atk_teams')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('atk_requests');
    }
};
