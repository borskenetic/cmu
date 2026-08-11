<?php

namespace App\Imports;

use App\Console\Commands\NormalizeStudentNames;
use App\Models\Student;
use App\Support\StudentSpreadsheetRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Validators\Failure;

class StudentsImport implements ToModel, WithHeadingRow, SkipsEmptyRows, WithValidation, SkipsOnFailure
{
    public int $created = 0;

    public int $updated = 0;

    public int $skipped = 0;

    public function prepareForValidation($data, $index): array
    {
        return StudentSpreadsheetRow::normalize($data);
    }

    public function rules(): array
    {
        return [
            'district_id' => 'nullable|string|max:255',
            'barcode' => 'nullable|string|max:255',
            'course' => 'nullable|string|max:255',
            'firstname' => 'nullable|string|max:255',
            'lastname' => 'nullable|string|max:255',
            'middle_initial' => 'nullable|string|max:255',
        ];
    }

    public function onFailure(Failure ...$failures): void
    {
        $this->skipped += count($failures);
    }

    public function model(array $row)
    {
        $row = StudentSpreadsheetRow::normalize($row);

        $districtId = $row['district_id'];
        $barcode = $row['barcode'];
        $firstname = $row['firstname'];
        $lastname = $row['lastname'];

        // Need at least one identifier and a full name.
        if (($districtId === '' && $barcode === '') || $firstname === '' || $lastname === '') {
            $this->skipped++;

            return null;
        }

        // Some legacy exports put the same value in both ID columns.
        if ($districtId !== '' && $barcode !== '' && $districtId === $barcode) {
            $districtId = '';
        }

        $normalizedName = NormalizeStudentNames::normalizeFullName($firstname.' '.$lastname);

        $incoming = [
            'district_id' => $districtId !== '' ? $districtId : null,
            'barcode' => $barcode !== '' ? $barcode : null,
            'course' => $row['course'] !== '' ? $row['course'] : null,
            'firstname' => $firstname,
            'lastname' => $lastname,
            'middle_initial' => $row['middle_initial'] !== '' ? $row['middle_initial'] : null,
            'year' => $row['year'] !== '' ? $row['year'] : null,
            'mobile_number' => $row['mobile_number'] !== '' ? $row['mobile_number'] : null,
            'birth_date' => $this->parseDate($row['birth_date'] ?? null),
            'normalized_name' => $normalizedName,
        ];

        $existing = $this->findExistingStudent(
            $incoming['district_id'],
            $incoming['barcode'],
            $normalizedName,
            $firstname,
            $lastname
        );

        if ($existing) {
            $attributes = $this->attributesForUpdate($existing, $incoming);

            if (empty($existing->qrcode)) {
                $attributes['qrcode'] = $this->nextStudentQrCode();
            }

            if ($attributes !== []) {
                $existing->update($attributes);
            }

            $this->updated++;

            return null;
        }

        $attributes = array_filter(
            $incoming,
            static fn ($value) => $value !== null && $value !== ''
        );

        // Required name fields always set for create.
        $attributes['firstname'] = $firstname;
        $attributes['lastname'] = $lastname;
        $attributes['normalized_name'] = $normalizedName;
        $attributes['qrcode'] = $row['qrcode'] !== '' ? $row['qrcode'] : $this->nextStudentQrCode();

        $this->created++;

        return new Student($attributes);
    }

    /**
     * Prefer non-empty spreadsheet values; keep existing DB values when file cell is blank.
     */
    private function attributesForUpdate(Student $existing, array $incoming): array
    {
        $attributes = [];

        foreach (['district_id', 'barcode', 'course', 'middle_initial', 'year', 'mobile_number', 'birth_date'] as $field) {
            if (! array_key_exists($field, $incoming)) {
                continue;
            }

            $value = $incoming[$field];
            if ($value === null || $value === '') {
                continue;
            }

            if ((string) $existing->{$field} !== (string) $value) {
                $attributes[$field] = $value;
            }
        }

        foreach (['firstname', 'lastname', 'normalized_name'] as $field) {
            if (! array_key_exists($field, $incoming) || $incoming[$field] === null || $incoming[$field] === '') {
                continue;
            }

            if ((string) $existing->{$field} !== (string) $incoming[$field]) {
                $attributes[$field] = $incoming[$field];
            }
        }

        return $attributes;
    }

    private function findExistingStudent(
        ?string $districtId,
        ?string $barcode,
        string $normalizedName,
        string $firstname,
        string $lastname
    ): ?Student {
        if ($barcode) {
            $match = Student::where('barcode', $barcode)->first();
            if ($match) {
                return $match;
            }
        }

        if ($districtId) {
            $match = Student::where('district_id', $districtId)->first();
            if ($match) {
                return $match;
            }
        }

        if ($normalizedName !== '') {
            $match = Student::where('normalized_name', $normalizedName)->first();
            if ($match) {
                return $match;
            }
        }

        if ($firstname !== '' && $lastname !== '') {
            return Student::query()
                ->where('firstname', $firstname)
                ->where('lastname', $lastname)
                ->first();
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
