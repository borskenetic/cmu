<?php

namespace App\Support;

class StudentSpreadsheetRow
{
    public static function normalize(array $row): array
    {
        $districtId = self::cell($row, [
            'district_id',
            'district id',
            'districtid',
            'id_number',
            'student_id',
        ]);

        $barcode = self::cell($row, [
            'barcode',
            'barcode_patron',
            'barcode_-_patron',
            'barcode - patron',
        ]);

        return [
            'district_id' => $districtId,
            'barcode' => $barcode,
            'firstname' => self::cell($row, [
                'firstname',
                'name_first',
                'name_-_first',
                'name - first',
            ]),
            'lastname' => self::cell($row, [
                'lastname',
                'name_last',
                'name_-_last',
                'name - last',
            ]),
            'middle_initial' => self::cell($row, [
                'middle_initial',
                'name_middle',
                'name_-_middle',
                'name - middle',
            ]),
            'year' => self::cell($row, ['year']),
            'mobile_number' => self::cell($row, ['mobile_number', 'mobile number']),
            'birth_date' => self::cell($row, ['birth_date', 'birth date']),
            'qrcode' => self::cell($row, ['qrcode']),
        ];
    }

    public static function cell(array $row, array $keys): string
    {
        foreach ($keys as $key) {
            if (! array_key_exists($key, $row)) {
                continue;
            }

            $value = trim((string) $row[$key]);
            if ($value !== '') {
                return $value;
            }
        }

        return '';
    }
}
