<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentBarcodesImportTemplateExport implements FromArray, WithHeadings
{
    public function headings(): array
    {
        return [
            'Barcode - Patron',
            'District ID',
            'Name - First',
            'Name - Last',
        ];
    }

    public function array(): array
    {
        return [
            [
                '2020301118',
                '23-3617',
                'Merry Fel',
                'Beltran',
            ],
            [
                '2020301121',
                '2020301121',
                'Prince Kailer',
                'Camanay',
            ],
        ];
    }
}
