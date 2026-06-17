<?php

namespace App\Imports;

use App\Console\Commands\NormalizeStudentNames;
use App\Models\Student;
use App\Support\StudentSpreadsheetRow;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class StudentBarcodesImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    public int $updated = 0;

    public int $skipped = 0;

    public int $notFound = 0;

    /** @var list<string> */
    public array $notFoundLabels = [];

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $this->processRow($row->toArray());
        }
    }

    private function processRow(array $raw): void
    {
        $row = StudentSpreadsheetRow::normalize($raw);
        $districtId = $row['district_id'];
        $barcode = $row['barcode'];
        $firstname = $row['firstname'];
        $lastname = $row['lastname'];

        if ($barcode === '') {
            $this->skipped++;

            return;
        }

        $student = $this->findStudent($districtId, $barcode, $firstname, $lastname);

        if (! $student) {
            $this->notFound++;
            $this->notFoundLabels[] = $this->rowLabel($districtId, $barcode, $firstname, $lastname);

            return;
        }

        if ($student->barcode === $barcode) {
            $this->skipped++;

            return;
        }

        $conflict = Student::query()
            ->where('barcode', $barcode)
            ->where('id', '!=', $student->id)
            ->exists();

        if ($conflict) {
            $this->skipped++;

            return;
        }

        $student->update(['barcode' => $barcode]);
        $this->updated++;
    }

    private function findStudent(
        string $districtId,
        string $barcode,
        string $firstname,
        string $lastname
    ): ?Student {
        if ($districtId !== '' && $districtId !== $barcode) {
            $match = Student::where('district_id', $districtId)->first();
            if ($match) {
                return $match;
            }
        }

        foreach (array_unique(array_filter([$districtId, $barcode])) as $identifier) {
            $match = Student::where('district_id', $identifier)->first();
            if ($match) {
                return $match;
            }
        }

        if ($firstname !== '' && $lastname !== '') {
            $normalized = NormalizeStudentNames::normalizeFullName($firstname.' '.$lastname);
            $match = Student::where('normalized_name', $normalized)->first();
            if ($match) {
                return $match;
            }

            $match = Student::query()
                ->where('firstname', $firstname)
                ->where('lastname', $lastname)
                ->first();
            if ($match) {
                return $match;
            }
        }

        return null;
    }

    private function rowLabel(
        string $districtId,
        string $barcode,
        string $firstname,
        string $lastname
    ): string {
        if ($districtId !== '') {
            return $districtId;
        }

        if ($firstname !== '' || $lastname !== '') {
            return trim($firstname.' '.$lastname);
        }

        return $barcode;
    }
}
