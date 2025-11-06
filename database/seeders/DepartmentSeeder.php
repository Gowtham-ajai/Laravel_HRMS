<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = ['Developer', 'Testing', 'Sales', 'Designing'];

        foreach ($departments as $dept) {
            DB::table('departments')->updateOrInsert(
                ['department_name' => $dept],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
