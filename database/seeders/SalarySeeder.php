<?php

namespace Database\Seeders;

use App\Models\Salary;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SalarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $salaries = ["100000", "200000", "300000", "400000", "500000"];
        foreach ($salaries as $salary) {
            Salary::create([
                'price' => $salary
            ]);
        }
    }
}
