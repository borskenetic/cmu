<div id="program-block-{{ $program->id }}" class="bg-white rounded shadow mb-3">
    <div class="flex justify-between items-center px-4 py-3 bg-gray-700 text-white">
        <span id="program-name-{{ $program->id }}" class="font-semibold">
            {{ $program->program_code }} — {{ $program->program_name }}
        </span>
        <div class="flex gap-2">
            <button type="button"
                onclick="openProgramEditModal({{ $program->id }}, @js($program->college_id), @js($program->program_code), @js($program->program_name))"
                class="bg-yellow-500 text-white px-2 py-1 rounded text-sm">
                Edit Course
            </button>
            <button type="button"
                onclick="openProgramDeleteModal({{ $program->id }}, @js($program->program_code))"
                class="bg-red-600 text-white px-2 py-1 rounded text-sm">
                Delete
            </button>
            <button type="button" data-panel-toggle="#program-{{ $program->id }}"
                class="bg-gray-600 px-2 py-1 rounded text-sm text-white">
                Toggle
            </button>
        </div>
    </div>

    <div id="program-{{ $program->id }}" class="p-4 hidden school-setup-panel">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($program->years as $year)
            <div class="bg-gray-50 rounded shadow">
                <div class="flex justify-between items-center px-3 py-2 border-b">
                    <span class="font-semibold">Year {{ $year->year_level }}</span>
                    <button type="button" data-panel-toggle="#year-{{ $year->id }}"
                        class="text-sm text-gray-600">
                        Toggle
                    </button>
                </div>
                <div id="year-{{ $year->id }}" class="p-3 hidden school-setup-panel">
                    <ul class="space-y-2 mb-3 max-h-52 overflow-y-auto">
                        @forelse($year->courses as $course)
                        <li id="course-{{ $course->id }}"
                            class="flex justify-between items-center border-b pb-1">
                            <span><strong>{{ $course->course_code }}</strong> — {{ $course->course_name }}</span>
                            <div class="flex gap-2">
                                <button type="button" class="bg-yellow-500 text-white px-2 py-1 rounded text-xs"
                                    onclick="openEditModal({{ $course->id }}, @js($course->course_code), @js($course->course_name))">
                                    Edit
                                </button>
                                <button type="button" class="bg-red-600 text-white px-2 py-1 rounded text-xs"
                                    onclick="openDeleteModal({{ $course->id }}, @js($course->course_code))">
                                    Delete
                                </button>
                            </div>
                        </li>
                        @empty
                        <li class="text-gray-500">No subjects yet.</li>
                        @endforelse
                    </ul>

                    <form method="POST" action="{{ route('prospectus.storeCourse', $year->id) }}"
                        class="add-course-form school-setup-form"
                        data-year="{{ $year->id }}">
                        @csrf
                        <input type="text" name="course_code" placeholder="Subject Code" required>
                        <input type="text" name="course_name" placeholder="Subject Name" required>
                        <button type="submit" class="bg-green-600 text-white px-3 py-1 rounded">
                            <span class="btn-text">Add</span>
                            <span
                                class="spinner hidden w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
