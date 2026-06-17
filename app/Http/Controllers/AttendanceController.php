<?php

namespace App\Http\Controllers;

use App\Console\Commands\NormalizeStudentNames;
use App\Models\AttendanceLog;
use App\Models\Setting;
use App\Models\Student;
use App\Services\AttendanceSessionService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function showScanner()
    {
        return view('attendance.scan', [
            'logoutFeedbackEnabled' => Setting::logoutFeedbackEnabled(),
            'sectionPickerEnabled' => Setting::sectionPickerEnabled(),
            'attendanceSections' => Setting::attendanceSections(),
        ]);
    }

    public function feedbackSettings()
    {
        return view('attendance.feedback_settings', [
            'enabled' => Setting::logoutFeedbackEnabled(),
        ]);
    }

    public function updateFeedbackSettings(Request $request)
    {
        $request->validate([
            'enabled' => 'required|in:0,1',
        ]);

        Setting::setLogoutFeedbackEnabled($request->input('enabled') === '1');

        return back()->with(
            'success',
            $request->input('enabled') === '1'
                ? 'Logout feedback is now enabled on the attendance scanner.'
                : 'Logout feedback is now disabled on the attendance scanner.'
        );
    }

    public function sectionSettings()
    {
        return view('attendance.section_settings', [
            'enabled' => Setting::sectionPickerEnabled(),
            'sections' => Setting::attendanceSections(),
        ]);
    }

    public function updateSectionSettings(Request $request)
    {
        $request->validate([
            'enabled' => 'required|in:0,1',
            'sections' => 'required|array|min:1',
            'sections.*' => 'required|string|max:120|distinct',
        ]);

        $sections = array_values(array_unique(array_filter(array_map(
            fn ($name) => trim((string) $name),
            $request->input('sections', [])
        ))));

        Setting::setSectionPickerEnabled($request->input('enabled') === '1');
        Setting::setAttendanceSections($sections);

        $pickerOn = $request->input('enabled') === '1';

        return back()->with(
            'success',
            $pickerOn
                ? 'Section picker enabled with '.count($sections).' section(s) on the scanner.'
                : 'Section picker disabled. '.count($sections).' section(s) saved for logs and filters.'
        );
    }

    public function scan(Request $request)
    {
        $request->validate(['qrcode' => 'required|string']);

        $student = $this->resolveStudent($request->qrcode);

        if (! $student) {
            return response()->json([
                'type' => 'error',
                'message' => 'RFID or QR code not recognized.',
            ]);
        }

        app(AttendanceSessionService::class)->closeStaleOpenInForStudent($student);

        $sessions = app(AttendanceSessionService::class);
        $lastLog = AttendanceLog::where('student_id', $student->id)
            ->orderByDesc('scanned_at')
            ->orderByDesc('id')
            ->first();

        $nextStatus = ($lastLog && $sessions->isInStatus($lastLog->status)) ? 'OUT' : 'IN';

        return response()->json([
            'type' => 'student',
            'next_status' => $nextStatus,
            'student_id' => $student->id,
            'logout_feedback_enabled' => Setting::logoutFeedbackEnabled(),
            'section_picker_enabled' => Setting::sectionPickerEnabled(),
            'student' => [
                'id' => $student->id,
                'firstname' => $student->firstname,
                'lastname' => $student->lastname,
                'profile_picture' => $student->profile_picture,
            ],
        ]);
    }

    public function processSection(Request $request)
    {
        $request->validate([
            'student_id' => 'required|integer|exists:students,id',
            'section' => 'nullable|string|max:255',
        ]);

        $section = $request->section ? trim((string) $request->section) : null;
        if ($section !== null && $section !== '') {
            $allowed = Setting::attendanceSections();
            if (! in_array($section, $allowed, true)) {
                return response()->json(['message' => 'Invalid section selected.'], 422);
            }
        } else {
            $section = null;
        }

        $student = Student::findOrFail($request->student_id);
        $sessions = app(AttendanceSessionService::class);
        $sessions->closeStaleOpenInForStudent($student);

        $lastLog = AttendanceLog::where('student_id', $student->id)
            ->orderByDesc('scanned_at')
            ->orderByDesc('id')
            ->first();

        $newStatus = ($lastLog && $sessions->isInStatus($lastLog->status)) ? 'OUT' : 'IN';

        $log = AttendanceLog::create([
            'student_id' => $student->id,
            'section' => $section,
            'status' => $newStatus,
            'scanned_at' => Carbon::now('Asia/Manila'),
        ]);

        $this->sendScanSms($student, $newStatus);

        return response()->json([
            'status' => $newStatus,
            'scanned_at' => $log->scanned_at->timezone('Asia/Manila')->format('Y-m-d h:i:s A'),
            'logout_feedback_enabled' => Setting::logoutFeedbackEnabled(),
        ]);
    }

    public function showChangeVideo()
    {
        return view('attendance.change_video');
    }

    public function uploadVideo(Request $request)
    {
        $request->validate([
            'video' => 'required|file|mimes:mp4|max:512000',
        ]);

        $video = $request->file('video');
        $filename = 'area51_product_slideshow.mp4';
        $video->move(base_path('videos'), $filename);

        return redirect()->route('attendance.changeVideo')->with('success', 'Video uploaded successfully!');
    }

    private function resolveStudent(string $raw): ?Student
    {
        $token = trim(str_replace("\r", '', $raw));
        $student = $this->findStudentByScanToken($token);

        $parsed = $this->parseQr($raw);

        if (! $student && $parsed['student_no']) {
            $student = $this->findStudentByScanToken($parsed['student_no']);
        }

        if (! $student && $parsed['full_name']) {
            $qrName = NormalizeStudentNames::normalizeFullName($parsed['full_name']);
            $student = Student::where('normalized_name', $qrName)->first();
        }

        return $student;
    }

    private function findStudentByScanToken(string $token): ?Student
    {
        if ($token === '') {
            return null;
        }

        return Student::query()
            ->where(function ($q) use ($token) {
                $q->where('district_id', $token)
                    ->orWhere('barcode', $token);
            })
            ->first();
    }

    private function parseQr(string $raw): array
    {
        $raw = trim(str_replace("\r", '', $raw));

        if (str_contains($raw, "\n")) {
            $lines = array_values(array_filter(array_map('trim', explode("\n", $raw))));

            return [
                'student_no' => $lines[0] ?? null,
                'full_name' => $lines[1] ?? null,
                'course' => $lines[2] ?? null,
            ];
        }

        $parts = array_map('trim', explode(',', $raw));

        if (preg_match('/^\d{2}-\d+$/', $parts[0] ?? '')) {
            return [
                'student_no' => $parts[0] ?? null,
                'full_name' => $parts[1] ?? null,
                'course' => $parts[2] ?? null,
            ];
        }

        return [
            'student_no' => null,
            'full_name' => $parts[0] ?? null,
            'course' => $parts[1] ?? null,
        ];
    }

    private function sendScanSms(Student $student, string $status): void
    {
        if (empty($student->mobile_number)) {
            return;
        }

        $template = Setting::where('key', Setting::KEY_SCAN_SMS)->value('value')
            ?? 'Hello {name}, you scanned {status} at the library at {time}.';

        $message = str_replace(
            ['{name}', '{status}', '{time}'],
            [
                trim($student->firstname.' '.$student->lastname),
                $status,
                Carbon::now('Asia/Manila')->format('h:i A'),
            ],
            $template
        );

        app(SmsController::class)->sendDirect($student->mobile_number, $message);
    }
}
