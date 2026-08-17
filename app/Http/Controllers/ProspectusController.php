<?php

namespace App\Http\Controllers;

use App\Models\College;
use App\Models\Program;
use App\Models\ProgramYear;
use App\Models\ProgramCourse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProspectusController extends Controller
{
    public function index()
    {
        $colleges = College::with('programs.years.courses')
            ->orderBy('name')
            ->get();

        $unassignedPrograms = Program::with('years.courses')
            ->whereNull('college_id')
            ->orderBy('program_name')
            ->get();

        return view('prospectus.index', compact('colleges', 'unassignedPrograms'));
    }

    public function storeCollege(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:colleges,name',
        ]);

        $college = College::create($data);

        return redirect()
            ->route('prospectus.index', ['open' => 'college-'.$college->id])
            ->with('success', 'College department added.');
    }

    public function updateCollege(Request $request, College $college)
    {
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('colleges', 'name')->ignore($college->id),
            ],
        ]);

        $college->update(['name' => $request->name]);

        return response()->json([
            'id' => $college->id,
            'name' => $college->name,
        ]);
    }

    public function destroyCollege(College $college)
    {
        if ($college->programs()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Remove or reassign courses in this college first.',
            ], 422);
        }

        $college->delete();

        return response()->json([
            'success' => true,
            'id' => $college->id,
        ]);
    }

    public function storeProgram(Request $request)
    {
        $data = $request->validate([
            'college_id'   => 'required|exists:colleges,id',
            'program_code' => 'required|unique:programs,program_code',
            'program_name' => 'required',
            'total_years'  => 'required|integer|min:1|max:6',
        ]);

        $program = Program::create($data);

        for ($i = 1; $i <= $program->total_years; $i++) {
            ProgramYear::create([
                'program_id' => $program->id,
                'year_level' => $i,
            ]);
        }

        return redirect()
            ->route('prospectus.index', ['open' => 'college-'.$program->college_id])
            ->with('success', 'Course added successfully.');
    }

    public function getProgramYears($programId)
    {
        $program = Program::with('years.courses')->findOrFail($programId);
        return response()->json(['years' => $program->years]);
    }

    public function storeCourse(Request $request, $yearId)
    {
        $data = $request->validate([
            'course_code' => 'required',
            'course_name' => 'required',
        ]);

        $course = ProgramCourse::create([
            'program_year_id' => $yearId,
            'course_code'     => $data['course_code'],
            'course_name'     => $data['course_name'],
        ]);

        if ($request->ajax()) {
            return view('prospectus.partials.course_item', compact('course'))->render();
        }

        return redirect()
            ->route('prospectus.index')
            ->with('success', 'Subject added successfully.');
    }

    public function updateCourse(Request $request, ProgramCourse $course)
    {
        $request->validate([
            'course_code' => 'required|string|max:50',
            'course_name' => 'required|string|max:255',
        ]);

        $course->update([
            'course_code' => $request->course_code,
            'course_name' => $request->course_name,
        ]);

        if ($request->ajax()) {
            return view('prospectus.partials.course_item', compact('course'))->render();
        }

        return redirect()->back()->with('success', 'Subject updated successfully.');
    }

    public function destroyCourse(Request $request, ProgramCourse $course)
    {
        $course->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Subject deleted successfully.');
    }

    public function updateProgram(Request $request, Program $program)
    {
        $request->validate([
            'college_id'   => 'nullable|exists:colleges,id',
            'program_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('programs', 'program_code')->ignore($program->id),
            ],
            'program_name' => 'required|string|max:255',
        ]);

        $previousCollegeId = $program->college_id;

        $program->update([
            'college_id'   => $request->college_id ?: null,
            'program_code' => $request->program_code,
            'program_name' => $request->program_name,
        ]);

        return response()->json([
            'id' => $program->id,
            'college_id' => $program->college_id,
            'college_changed' => (int) $previousCollegeId !== (int) $program->college_id,
            'program_code' => $program->program_code,
            'program_name' => $program->program_name,
        ]);
    }

    public function destroyProgram(Program $program)
    {
        $program->delete();

        return response()->json([
            'success' => true,
            'id' => $program->id,
        ]);
    }
}
