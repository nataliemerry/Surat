<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AccountSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('accounts')->insert([
            'name' => 'BPS Kabupaten Magelang',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
