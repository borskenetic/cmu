<?php

namespace App\Support;

class StudentSpreadsheetRow
{
    /**
     * Normalize spreadsheet rows from the library card export / import template.
     *
     * Expected columns (any heading style Maatwebsite may emit):
     * - Barcode - Patron
     * - Course
     * - District ID
     * - Name - First
     * - Name - Last
     * - Card Expires (ignored; no student column)
     * - Name - Middle
     */
    public static function normalize(array $row): array
    {
        $row = self::normalizeKeys($row);

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
            'course' => self::cell($row, [
                'course',
                'program',
                'program_code',
            ]),
            'firstname' => self::cell($row, [
                'firstname',
                'first_name',
                'name_first',
                'name_-_first',
                'name - first',
            ]),
            'lastname' => self::cell($row, [
                'lastname',
                'last_name',
                'name_last',
                'name_-_last',
                'name - last',
            ]),
            'middle_initial' => self::cell($row, [
                'middle_initial',
                'middle_name',
                'name_middle',
                'name_-_middle',
                'name - middle',
            ]),
            'year' => self::cell($row, ['year']),
            'mobile_number' => self::cell($row, ['mobile_number', 'mobile number']),
            'birth_date' => self::cell($row, ['birth_date', 'birth date', 'birthday']),
            'qrcode' => self::cell($row, ['qrcode', 'qr_code']),
            'card_expires' => self::cell($row, [
                'card_expires',
                'card_expire',
                'card_expiration',
            ]),
        ];
    }

    public static function cell(array $row, array $keys): string
    {
        foreach ($keys as $key) {
            $lookup = self::keyCandidates($key);
            foreach ($lookup as $candidate) {
                if (! array_key_exists($candidate, $row)) {
                    continue;
                }

                $value = trim((string) $row[$candidate]);
                if ($value !== '') {
                    return $value;
                }
            }
        }

        return '';
    }

    /**
     * Lowercase keys and strip odd unicode spaces so heading variants match.
     */
    private static function normalizeKeys(array $row): array
    {
        $normalized = [];

        foreach ($row as $key => $value) {
            if (! is_string($key)) {
                $normalized[$key] = $value;
                continue;
            }

            $clean = strtolower(trim(preg_replace('/\s+/u', ' ', $key) ?? $key));
            $normalized[$clean] = $value;

            // Also keep slug-style keys Maatwebsite may already have produced.
            $slug = preg_replace('/[^a-z0-9]+/', '_', $clean) ?? $clean;
            $slug = trim($slug, '_');
            if ($slug !== '' && ! array_key_exists($slug, $normalized)) {
                $normalized[$slug] = $value;
            }
        }

        return $normalized;
    }

    /** @return list<string> */
    private static function keyCandidates(string $key): array
    {
        $key = strtolower(trim($key));
        $slug = trim(preg_replace('/[^a-z0-9]+/', '_', $key) ?? $key, '_');

        return array_values(array_unique(array_filter([$key, $slug])));
    }
}
