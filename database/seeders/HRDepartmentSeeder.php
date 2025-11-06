<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HRDepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hrdepartments = ['HR Administration', 'Recruitment Team', 'Payroll Department', 'Employee Relations', 'Performance Management'];

        foreach($hrdepartments as $dept){
            DB::table('hrdepartments')->updateOrInsert(
                ['department_name' => $dept],
                ['created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
