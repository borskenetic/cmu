<?php

namespace Database\Seeders;

use App\Models\College;
use Illuminate\Database\Seeder;

class CollegeSeeder extends Seeder
{
    public function run(): void
    {
        $colleges = [
            'College of Agriculture',
            'College of Arts and Sciences',
            'College of Business and Management',
            'College of Education',
            'College of Engineering',
            'College of Forestry and Environmental Science',
            'College of Human Ecology',
            'College of Nursing',
            'College of Veterinary Medicine',
        ];

        foreach ($colleges as $name) {
            College::updateOrCreate(['name' => $name]);
        }
    }
}
