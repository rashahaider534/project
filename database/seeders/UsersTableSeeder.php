<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'phone' => '+96391111111',
                'password' => Hash::make('password123'),
                'role'=>'driver'
            ],
            [
                'phone' => '+96392222222',
                'password' => Hash::make('driver2024'),
                'role'=>'driver'
            ],
            [
                'phone' => '+96393333333',
                'password' => Hash::make('secure1234'),
                'role'=>'driver'
            ],
            [
                'phone' => '+96394444444',
                'password' => Hash::make('passkey456'),
                'role'=>'driver'
            ],
            [
                'phone' => '+96395555555',
                'password' => Hash::make('welcome789'),
                'role'=>'driver'
            ],
        ]);
    }
}
