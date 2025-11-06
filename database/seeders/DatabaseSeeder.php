<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed departments and designations first
        $this->call([
            DepartmentSeeder::class,
            DesignationSeeder::class,
            RegisterSeeder::class,
        ]);

        // Seed a default test user safely
        User::firstOrCreate(
            ['email' => 'test@example.com'], // check by email
            [
                'name' => 'Test User',
                'password' => bcrypt('password123'), // default password
            ]
        );
    }
}
