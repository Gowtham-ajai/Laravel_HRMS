<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DesignationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = DB::table('departments')->pluck('id', 'department_name');

        $designations = [
            'Developer' => ['React Developer', 'Python Developer', 'Java Developer', 'Laravel Developer', 'Angular Developer'],
            'Testing' => ['Manual Testing', 'Automation Testing'],
            'Sales' => ['Sales Executive', 'Sales Manager'],
            'Designing' => ['UI/UX Designer', 'Graphic Designer'],
        ];

        foreach ($designations as $deptName => $roles) {
            $deptId = $departments[$deptName] ?? null;
            if ($deptId) {
                foreach ($roles as $role) {
                    DB::table('designations')->updateOrInsert(
                        ['department_id' => $deptId, 'designation' => $role],
                        ['created_at' => now(), 'updated_at' => now()]
                    );
                }
            }
        }
    }
}
