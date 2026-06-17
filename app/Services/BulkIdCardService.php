<?php

namespace App\Services;

use App\Http\Controllers\EmployeeIdCardController;
use App\Http\Controllers\IdCardController;
use App\Models\Employee;
use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use ZipArchive;

class BulkIdCardService
{
    public const MAX_BULK_IDS = 100;

    public function downloadStudentsZip(Collection $students): BinaryFileResponse
    {
        $zipPath = storage_path('app/bulk_student_ids_'.uniqid().'.zip');
        $idCard = app(IdCardController::class);

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Could not create ZIP archive.');
        }

        foreach ($students as $student) {
            $folder = $this->safePathSegment(
                ($student->district_id ?? $student->barcode ?? 'student').'_'.$student->lastname.'_'.$student->firstname
            );

            try {
                $zip->addFromString("{$folder}/front.png", $idCard->front($student->id)->getContent());
                $zip->addFromString("{$folder}/back.png", $idCard->back($student->id)->getContent());
            } catch (\Throwable) {
                continue;
            }
        }

        $zip->close();

        $filename = 'student_ids_'.now()->format('Y-m-d_His').'.zip';

        return response()->download($zipPath, $filename)->deleteFileAfterSend(true);
    }

    public function downloadEmployeesZip(Collection $employees): BinaryFileResponse
    {
        $zipPath = storage_path('app/bulk_employee_ids_'.uniqid().'.zip');
        $idCard = app(EmployeeIdCardController::class);

        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            abort(500, 'Could not create ZIP archive.');
        }

        foreach ($employees as $employee) {
            $folder = $this->safePathSegment(
                ($employee->employee_id ?? 'employee').'_'.$employee->lastname.'_'.$employee->firstname
            );

            try {
                $zip->addFromString("{$folder}/front.png", $idCard->front($employee->id)->getContent());
                $zip->addFromString("{$folder}/back.png", $idCard->back($employee->id)->getContent());
            } catch (\Throwable) {
                continue;
            }
        }

        $zip->close();

        $filename = 'employee_ids_'.now()->format('Y-m-d_His').'.zip';

        return response()->download($zipPath, $filename)->deleteFileAfterSend(true);
    }

    private function safePathSegment(string $name): string
    {
        $name = Str::slug($name, '_');

        return $name !== '' ? $name : 'record';
    }
}
