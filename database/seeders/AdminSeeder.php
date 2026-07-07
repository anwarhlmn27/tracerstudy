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
            ['email' => 'admin@tracer.ac.id'],
            [
                'id' => Str::uuid(),
                'name' => 'Administrator',
                'password' => Hash::make('Password#@!234'),
                'role' => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'alumni@tracer.ac.id'],
            [
                'id' => Str::uuid(),
                'name' => 'Alumni User',
                'password' => Hash::make('password123'),
                'role' => 'alumni',
            ]
        );

        User::updateOrCreate(
            ['email' => 'dosen@tracer.ac.id'],
            [
                'id' => Str::uuid(),
                'name' => 'Dosen User',
                'password' => Hash::make('password123'),
                'role' => 'dosen',
            ]
        );

        User::updateOrCreate(
            ['email' => 'atasan@tracer.ac.id'],
            [
                'id' => Str::uuid(),
                'name' => 'Atasan User',
                'password' => Hash::make('password123'),
                'role' => 'atasan',
            ]
        );
    }
}