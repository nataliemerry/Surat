<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Account;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $account = Account::create(['name' => 'BPS Kabupaten Magelang']);

        User::factory()->create([
            'account_id' => $account->id,
            'first_name' => 'Retno',
            'last_name' => 'Ningsih',
            'email' => 'retno@example.com',
            'password' => 'secret',
            'owner' => true,
        ]);

        User::factory(5)->create(['account_id' => $account->id]);
    }
}
