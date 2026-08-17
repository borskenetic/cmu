<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttendanceLog;
use App\Models\College;
use App\Models\Program;
use App\Models\Setting;
use App\Models\Student;
use App\Services\PatronAttendanceReportService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Schema;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceLogsExport;

class AttendanceLogController extends Controller
{
    public function index(Request $request)
    {
        $logs = $this->filteredLogs($request)
            ->paginate(10)
            ->withQueryString();

        $colleges = Schema::hasTable('colleges')
            ? College::orderBy('name')->get()
            : collect();

        $sections = Setting::attendanceSections();

        return view('attendance_logs.index', compact('logs', 'colleges', 'sections'));
    }

    private function filteredLogs(Request $request)
    {
        return AttendanceLog::with('student')

            ->when($request->from,
                fn($q) => $q->whereDate('scanned_at', '>=', $request->from))

            ->when($request->to,
                fn($q) => $q->whereDate('scanned_at', '<=', $request->to))

            ->when($request->section,
                fn($q) => $q->where('section', $request->section))

            ->when($request->year_level,
                fn($q) => $q->whereHas('student',
                    fn($q2) => $q2->where('year', $request->year_level)
                ))

            ->when($request->college_id, function ($q) use ($request) {
                $this->applyCollegeFilter($q, $request->college_id);
            })

            ->when($request->course,
                fn($q) => $q->whereHas('student',
                    fn($q2) => $q2->where('course', $request->course)
                ))

            ->when($request->status,
                fn($q) => $q->where('status', strtoupper($request->status))
            )

            ->when($request->search, function ($q) use ($request) {
                $search = $request->search;

                $q->where(function ($query) use ($search) {

                    $query->where('section', 'like', "%{$search}%")

                        ->orWhereHas('student', function ($q2) use ($search) {
                            $q2->where('firstname', 'like', "%{$search}%")
                               ->orWhere('lastname', 'like', "%{$search}%")
                               ->orWhere('course', 'like', "%{$search}%");
                        });

                });
            })

            ->orderBy('scanned_at', 'desc');
    }

    private function applyCollegeFilter($query, $collegeId)
    {
        if (! Schema::hasTable('programs')) {
            return $query->whereRaw('1 = 0');
        }

        $programs = Program::where('college_id', $collegeId)->get(['program_code', 'program_name']);
        $values = $programs->pluck('program_code')
            ->merge($programs->pluck('program_name'))
            ->filter()
            ->unique()
            ->values();

        if ($values->isEmpty()) {
            return $query->whereRaw('1 = 0');
        }

        return $query->whereHas('student', function ($studentQuery) use ($values) {
            $studentQuery->whereIn('course', $values);
        });
    }

    public function create()
    {
        $students = Student::all();
        return view('attendance_logs.create', compact('students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'status' => 'required|in:in,out',
            'scanned_at' => 'required|date',
        ]);

        AttendanceLog::create($request->only(['student_id', 'status', 'scanned_at']));

        return redirect()->route('attendance_logs.index')
            ->with('success', 'Attendance logged!');
    }

    public function exportPdf(Request $request)
    {
        $logs = $this->filteredLogs($request)->get();

        $pdf = Pdf::loadView('attendance_logs.pdf', compact('logs'));
        return $pdf->download('attendance_logs.pdf');
    }

    public function exportExcel(Request $request)
    {
        $logs = $this->filteredLogs($request)->get();

        return Excel::download(
            new AttendanceLogsExport($logs),
            'attendance_logs.xlsx'
        );
    }

    public function reportsHub()
    {
        return view('attendance_logs.reports_hub');
    }

    public function reportsDashboard(Request $request, PatronAttendanceReportService $patronReports)
    {
        $programNameByCode = collect();
        $only = $request->query('only');
        $from = $request->query('from');
        $to = $request->query('to');

        return view('attendance_logs.reports_dashboard', array_merge(
            compact('programNameByCode', 'only', 'from', 'to'),
            $patronReports->build($from, $to)
        ));
    }

    public function reportsExportCsv(Request $request, PatronAttendanceReportService $patronReports)
    {
        return $patronReports->streamCsvResponse(
            $request->query('from'),
            $request->query('to')
        );
    }
}