<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            $table->dropColumn('filepath');
            $table->string('drive_file_id')->nullable()->after('link');
        });
    }

    public function down(): void
    {
        Schema::table('surats', function (Blueprint $table) {
            $table->dropColumn('drive_file_id');
            $table->string('filepath')->nullable()->after('isPengelolaan');
        });
    }
};
