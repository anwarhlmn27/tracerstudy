<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'ace@horizon.ac.id'],
            [
                'id' => Str::uuid(),
                'name' => 'Administrator',
                'password' => Hash::make('Password@234'),
                'role' => 'admin',
            ]
        );
    }
}