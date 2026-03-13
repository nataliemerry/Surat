<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('atk_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('pegawai_id')->nullable()->after('id');
            $table->foreign('pegawai_id')->references('id')->on('pegawais')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('atk_requests', function (Blueprint $table) {
            $table->dropForeign(['pegawai_id']);
            $table->dropColumn('pegawai_id');
        });
    }
};
