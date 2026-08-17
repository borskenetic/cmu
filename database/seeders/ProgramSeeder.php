<?php

namespace Database\Seeders;

use App\Models\College;
use App\Models\Program;
use Illuminate\Database\Seeder;

class ProgramSeeder extends Seeder
{
    public function run(): void
    {
        $collegeId = function (string $name): ?int {
            return College::where('name', $name)->value('id');
        };

        $programs = [
            [
                'program_code' => 'BSCS',
                'program_name' => 'Bachelor of Science in Computer Science',
                'total_years' => 4,
                'college_id' => $collegeId('College of Arts and Sciences'),
            ],
            [
                'program_code' => 'BSIT',
                'program_name' => 'Bachelor of Science in Information Technology',
                'total_years' => 4,
                'college_id' => $collegeId('College of Arts and Sciences'),
            ],
            [
                'program_code' => 'BSED',
                'program_name' => 'Bachelor of Secondary Education',
                'total_years' => 4,
                'college_id' => $collegeId('College of Education'),
            ],
            [
                'program_code' => 'BSBA',
                'program_name' => 'Bachelor of Science in Business Administration',
                'total_years' => 4,
                'college_id' => $collegeId('College of Business and Management'),
            ],
            [
                'program_code' => 'BSA',
                'program_name' => 'Bachelor of Science in Accountancy',
                'total_years' => 4,
                'college_id' => $collegeId('College of Business and Management'),
            ],
        ];

        foreach ($programs as $row) {
            Program::updateOrCreate(
                ['program_code' => $row['program_code']],
                $row
            );
        }
    }
}
