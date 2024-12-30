<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserFactory::$defaultPassword = Hash::make('12345');

        $rows = DB::connection('online_db')->table('users')->get();
        foreach ($rows as $row) {
            DB::connection()->table('users')->insert((array)$row);
        }

        // user mentor sudah ada di online_db
        User::factory()->create([
            'name' => 'Mentor',
            'email' => 'mentor@example.com',
            'active' => 1,
        ]);
    }
}
