<?php

namespace App\Imports;

use App\Console\Commands\NormalizeStudentNames;
use App\Models\Student;
use App\Support\StudentSpreadsheetRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class StudentsImport implements ToModel, WithHeadingRow, SkipsEmptyRows, WithValidation
{
    public function prepareForValidation($data, $index): array
    {
        return StudentSpreadsheetRow::normalize($data);
    }

    public function rules(): array
    {
        return [
            'district_id' => 'nullable|string|max:255',
            'barcode' => 'nullable|string|max:255',
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
        ];
    }

    public function model(array $row)
    {
        $row = StudentSpreadsheetRow::normalize($row);

        $districtId = $row['district_id'];
        $barcode = $row['barcode'];
        $firstname = $row['firstname'];
        $lastname = $row['lastname'];

        if (($districtId === '' && $barcode === '') || $firstname === '' || $lastname === '') {
            return null;
        }

        if ($districtId !== '' && $barcode !== '' && $districtId === $barcode) {
            $districtId = '';
        }

        $attributes = [
            'district_id' => $districtId !== '' ? $districtId : null,
            'barcode' => $barcode !== '' ? $barcode : null,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'middle_initial' => $row['middle_initial'] !== '' ? $row['middle_initial'] : null,
            'year' => $row['year'] !== '' ? $row['year'] : null,
            'mobile_number' => $row['mobile_number'] !== '' ? $row['mobile_number'] : null,
            'birth_date' => $this->parseDate($row['birth_date'] ?? null),
            'normalized_name' => NormalizeStudentNames::normalizeFullName($firstname.' '.$lastname),
        ];

        $existing = $this->findExistingStudent($attributes['district_id'], $attributes['barcode']);

        if ($existing) {
            if (empty($existing->qrcode)) {
                $attributes['qrcode'] = $this->nextStudentQrCode();
            }

            $existing->update($attributes);

            return null;
        }

        $attributes['qrcode'] = $row['qrcode'] !== '' ? $row['qrcode'] : $this->nextStudentQrCode();

        return new Student($attributes);
    }

    private function findExistingStudent(?string $districtId, ?string $barcode): ?Student
    {
        if ($barcode) {
            $match = Student::where('barcode', $barcode)->first();
            if ($match) {
                return $match;
            }
        }

        if ($districtId) {
            return Student::where('district_id', $districtId)->first();
        }

        return null;
    }

    private function nextStudentQrCode(): string
    {
        $last = Student::whereNotNull('qrcode')
            ->where('qrcode', 'like', 'S-%')
            ->orderByDesc('id')
            ->value('qrcode');

        $nextNumber = 1;
        if ($last && preg_match('/S-(\d+)/', $last, $matches)) {
            $nextNumber = (int) $matches[1] + 1;
        }

        return 'S-'.str_pad((string) $nextNumber, 8, '0', STR_PAD_LEFT);
    }

    private function parseDate(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if ($value instanceof \DateTimeInterface) {
            return $value->format('Y-m-d');
        }

        $str = trim((string) $value);
        if ($str === '') {
            return null;
        }

        try {
            return \Carbon\Carbon::parse($str)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }
}
