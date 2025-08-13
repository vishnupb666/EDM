<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\Department;
use App\Models\Employees;
use Illuminate\Support\Str;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $departments = Department::all();

        if ($departments->count() === 0) {
            $this->call(DepartmentSeeder::class);
            $departments = Department::all();
        }

        for ($i = 1; $i <= 10; $i++) {
            Employees::create([
                'name' => 'Employee ' . $i,
                'email' => 'employee' . $i . '@example.com',
                'department_id' => $departments->random()->id,
                'salary' => rand(20000, 80000),
                'joining_date' => now()->subDays(rand(1, 1000)),
            ]);
        }
    }
}
