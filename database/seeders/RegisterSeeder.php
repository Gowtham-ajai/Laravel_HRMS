<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RegisterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('registers')->insert([
            // HR accounts
            [
                'name' => 'Gowtham HR',
                'email' => 'gowtham.hr@gmail.com',
                'phone' => '9876543210',
                'password' => Hash::make('gowthamhr123'),
                'role' => 'HR',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@rsoft.com',
                'phone' => '9999999999',
                'password' => Hash::make('admin123'),
                'role' => 'Admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]
           
        ]);
    }
}
