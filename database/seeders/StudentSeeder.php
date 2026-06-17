<?php

namespace Database\Seeders;

use App\Console\Commands\NormalizeStudentNames;
use App\Models\Student;
use Illuminate\Database\Seeder;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $students = [
            [
                'district_id' => '2024-00001',
                'barcode' => null,
                'firstname' => 'Juan',
                'lastname' => 'Dela Cruz',
                'middle_initial' => 'M',
                'course' => 'BSCS',
                'year' => '3rd Year',
                'mobile_number' => '09171234501',
                'birth_date' => '2002-03-15',
                'qrcode' => 'S-00000001',
            ],
            [
                'district_id' => '2024-00002',
                'barcode' => null,
                'firstname' => 'Maria',
                'lastname' => 'Santos',
                'middle_initial' => 'L',
                'course' => 'BSIT',
                'year' => '2nd Year',
                'mobile_number' => '09181234502',
                'birth_date' => '2003-07-22',
                'qrcode' => 'S-00000002',
            ],
            [
                'district_id' => '2024-00003',
                'barcode' => null,
                'firstname' => 'Jose',
                'lastname' => 'Reyes',
                'middle_initial' => null,
                'course' => 'BSED',
                'year' => '4th Year',
                'mobile_number' => '09191234503',
                'birth_date' => '2001-11-08',
                'qrcode' => 'S-00000003',
            ],
            [
                'district_id' => '2024-00004',
                'barcode' => null,
                'firstname' => 'Ana',
                'lastname' => 'Garcia',
                'middle_initial' => 'P',
                'course' => 'BSBA',
                'year' => '1st Year',
                'mobile_number' => '09201234504',
                'birth_date' => '2005-01-30',
                'qrcode' => 'S-00000004',
            ],
            [
                'district_id' => '2024-00005',
                'barcode' => 'LEGACY-00005',
                'firstname' => 'Mark',
                'lastname' => 'Lopez',
                'middle_initial' => 'D',
                'course' => 'BSA',
                'year' => '3rd Year',
                'mobile_number' => '09211234505',
                'birth_date' => '2002-09-12',
                'qrcode' => 'S-00000005',
            ],
        ];

        foreach ($students as $row) {
            $fullName = trim($row['firstname'].' '.$row['lastname']);
            $row['normalized_name'] = NormalizeStudentNames::normalizeFullName($fullName);
            $row['role_id'] = null;

            $matchKey = $row['district_id']
                ? ['district_id' => $row['district_id']]
                : ['barcode' => $row['barcode']];

            Student::updateOrCreate($matchKey, $row);
        }
    }
}
