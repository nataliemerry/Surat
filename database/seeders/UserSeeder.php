<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $accountId = DB::table('accounts')->where('name', 'BPS Kabupaten Magelang')->value('id');

        User::create([
            'account_id'        => $accountId,
            'first_name'        => 'Retno',
            'last_name'         => 'Ningsih',
            'email'             => 'retno@example.com',
            'email_verified_at' => now(),
            'password'          => Hash::make('secret'),
            'owner'             => true,
        ]);

        User::factory(5)->create(['account_id' => $accountId]);
    }
}
