<?php

namespace Database\Seeders;

use App\Models\User;
use Database\Factories\UserFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        UserFactory::$defaultPassword = Hash::make('12345');
        User::factory()->create([
            'name' => 'Fahmi Fauzi Rahman',
            'email' => 'fahmifauzirahman@gmail.com',
            'active' => 1,
        ]);
        User::factory()->create([
            'name' => 'Neni Nurniah',
            'email' => 'neninurniah@gmail.com',
            'active' => 1,
        ]);
    }
}
