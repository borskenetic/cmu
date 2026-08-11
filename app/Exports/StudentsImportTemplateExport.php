<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentsImportTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        // Matches library card export: CRWReportJob*.xlsx
        return [
            'Barcode - Patron',
            'Course',
            'District ID',
            'Name - First',
            'Name - Last',
            'Card Expires',
            'Name - Middle',
        ];
    }

    public function array(): array
    {
        return [
            [
                '00000000000100',
                'BS Nursing',
                '2016400290',
                'Kristine Jane',
                'Antonio',
                '',
                'Dela Cruz',
            ],
            [
                '2020301121',
                'BS Information Technology',
                '23-3617',
                'Prince Kailer',
                'Camanay',
                '',
                'Caburnay',
            ],
        ];
    }
}
