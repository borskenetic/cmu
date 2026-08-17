@extends('layouts.app')

@section('title', 'School Setup')

@push('styles')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="{{ asset('css/prospectus/index.css') }}">
    <style>
        body { display: block !important; height: auto !important; }
        .school-setup-panel { display: none; height: auto !important; overflow: visible !important; }
        .school-setup-panel.is-open { display: block !important; }
        .school-setup-header {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 1rem;
            background: #1f2937;
            color: #fff;
            cursor: pointer;
            user-select: none;
        }
        .school-setup-header-title { font-weight: 600; flex: 1 1 auto; min-width: 12rem; }
        .school-setup-header-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            align-items: center;
        }
        .school-setup-header-actions button {
            border: 0;
            padding: 0.35rem 0.65rem;
            font-size: 0.875rem;
            color: #fff;
            cursor: pointer;
        }
        .school-setup-btn-expand { background: #4b5563; }
        .school-setup-btn-edit { background: #eab308; }
        .school-setup-btn-delete { background: #dc2626; }
        .school-setup-form {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            align-items: center;
        }
        .school-setup-form input[name="program_code"],
        .school-setup-form input[name="course_code"] { flex: 1 1 140px; }
        .school-setup-form input[name="program_name"],
        .school-setup-form input[name="course_name"] { flex: 2 1 220px; }
        .school-setup-form input[name="total_years"] { flex: 0 0 5.5rem; }
        .school-setup-form button[type="submit"] { flex: 0 0 auto; min-height: 2.5rem; }
        .school-setup-form input {
            border: 1px solid #cbd5e1 !important;
            background: #fff !important;
            color: #1f2937 !important;
            padding: 0.5rem 0.75rem;
            min-height: 2.5rem;
        }
        .school-setup-add-course {
            border: 1px solid #e5e7eb;
            background: #f9fafb;
            border-radius: 0;
            padding: 0.75rem;
            margin-bottom: 1rem;
        }
        .school-setup-modal {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
            z-index: 2000;
        }
        .school-setup-modal.is-open { display: flex !important; }
    </style>
@endpush

@section('content')
    <div class="prospectus-page max-w-6xl mx-auto px-2 py-2 text-gray-800">
        <h1 class="text-2xl font-bold mb-6">School Setup</h1>

        @if(session('success'))
        <div class="p-3 mb-4 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif

        @if($errors->any())
        <div class="p-3 mb-4 bg-red-100 text-red-800 rounded">
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="bg-white rounded shadow p-4 mb-6">
            <h2 class="font-semibold mb-3">Add College Department</h2>
            <form method="POST" action="{{ route('prospectus.storeCollege') }}"
                class="grid grid-cols-1 md:grid-cols-4 gap-3">
                @csrf
                <input type="text" name="name" value="{{ old('name') }}"
                    placeholder="College of Education"
                    class="border px-3 py-2 md:col-span-3" required>
                <button type="submit" class="btn btn-primary bg-blue-600 text-white px-4 py-2 rounded">Add</button>
            </form>
        </div>

        @forelse($colleges as $college)
        <div id="college-block-{{ $college->id }}" class="bg-white rounded shadow mb-6">
            <div class="school-setup-header" onclick="toggleSchoolSetupPanel('#college-{{ $college->id }}')" role="button" tabindex="0">
                <span id="college-name-{{ $college->id }}" class="school-setup-header-title">
                    {{ $college->name }}
                </span>
                <div class="school-setup-header-actions" onclick="event.stopPropagation()">
                    <button type="button" class="school-setup-btn-expand"
                        onclick="toggleSchoolSetupPanel('#college-{{ $college->id }}')">
                        Expand / Collapse
                    </button>
                    <button type="button" class="school-setup-btn-edit"
                        onclick="openCollegeEditModal({{ $college->id }}, @js($college->name))">
                        Edit College
                    </button>
                    <button type="button" class="school-setup-btn-delete"
                        onclick="openCollegeDeleteModal({{ $college->id }}, @js($college->name))">
                        Delete
                    </button>
                </div>
            </div>

            <div id="college-{{ $college->id }}" class="p-4 school-setup-panel">
                <div class="school-setup-add-course">
                    <h3 class="font-semibold mb-3">Add Course</h3>
                    <form method="POST" action="{{ route('prospectus.storeProgram') }}"
                        class="school-setup-form">
                        @csrf
                        <input type="hidden" name="college_id" value="{{ $college->id }}">
                        <input type="text" name="program_code" placeholder="Program Code" required>
                        <input type="text" name="program_name" placeholder="Course Name" required>
                        <input type="number" name="total_years" placeholder="Years" min="1" max="6" required>
                        <button type="submit" class="btn btn-primary bg-blue-600 text-white px-4 py-2">Add</button>
                    </form>
                </div>

                @forelse($college->programs as $program)
                    @include('prospectus.partials.program_item', ['program' => $program])
                @empty
                    <p class="text-gray-500 mb-0">No courses yet. Add one above.</p>
                @endforelse
            </div>
        </div>
        @empty
        <p class="text-gray-500 mb-6">No college departments yet. Add one above.</p>
        @endforelse

        @if($unassignedPrograms->isNotEmpty())
        <div class="bg-white rounded shadow mb-6">
            <div class="school-setup-header" onclick="toggleSchoolSetupPanel('#unassigned-programs')" role="button" tabindex="0">
                <span class="school-setup-header-title">Unassigned Courses</span>
                <div class="school-setup-header-actions" onclick="event.stopPropagation()">
                    <button type="button" class="school-setup-btn-expand"
                        onclick="toggleSchoolSetupPanel('#unassigned-programs')">
                        Expand / Collapse
                    </button>
                </div>
            </div>
            <div id="unassigned-programs" class="p-4 school-setup-panel">
                <p class="text-sm text-gray-600 mb-3">These courses are not under a college yet. Use Edit Course to assign one.</p>
                @foreach($unassignedPrograms as $program)
                    @include('prospectus.partials.program_item', ['program' => $program])
                @endforeach
            </div>
        </div>
        @endif
    </div>

    <div id="deleteModal" class="school-setup-modal">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h2 class="text-lg font-bold mb-4">Confirm Delete</h2>
            <p id="deleteMessage" class="mb-4 text-gray-700"></p>
            <form id="deleteForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeDeleteModal()"
                        class="px-4 py-2 bg-gray-400 rounded text-white">Cancel</button>
                    <button type="submit" id="deleteBtn"
                        class="px-4 py-2 bg-red-600 rounded text-white flex items-center gap-2">
                        <span class="btn-text">Delete</span>
                        <span
                            class="spinner hidden w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="editModal" class="school-setup-modal">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h2 class="text-lg font-bold mb-4">Edit Subject</h2>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="mb-3">
                    <label class="block text-sm font-medium">Subject Code</label>
                    <input type="text" id="editCourseCode" name="course_code" class="border px-3 py-2 w-full" required>
                </div>
                <div class="mb-3">
                    <label class="block text-sm font-medium">Subject Name</label>
                    <input type="text" id="editCourseName" name="course_name" class="border px-3 py-2 w-full" required>
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeEditModal()"
                        class="px-4 py-2 bg-gray-400 rounded text-white">Cancel</button>
                    <button type="submit" id="editBtn"
                        class="px-4 py-2 bg-yellow-600 rounded text-white flex items-center gap-2">
                        <span class="btn-text">Update</span>
                        <span
                            class="spinner hidden w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="editProgramModal" class="school-setup-modal">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h2 class="text-lg font-bold mb-4">Edit Course</h2>
            <form id="editProgramForm" method="POST">
                @csrf
                @method('PUT')
                <select name="college_id" id="editProgramCollege" class="w-full border rounded px-3 py-2 mb-3">
                    <option value="">Unassigned</option>
                    @foreach($colleges as $collegeOption)
                        <option value="{{ $collegeOption->id }}">{{ $collegeOption->name }}</option>
                    @endforeach
                </select>
                <input type="text" name="program_code" id="editProgramCode" class="w-full border rounded px-3 py-2 mb-3"
                    placeholder="Program Code" required>
                <input type="text" name="program_name" id="editProgramName" class="w-full border rounded px-3 py-2 mb-3"
                    placeholder="Course Name" required>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeProgramEditModal()"
                        class="px-3 py-1 border rounded">Cancel</button>
                    <button id="editProgramBtn" type="submit"
                        class="bg-blue-600 text-white px-3 py-1 rounded flex items-center">
                        <span class="btn-text">Save</span>
                        <span
                            class="spinner hidden ml-2 w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="deleteProgramModal" class="school-setup-modal">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h2 class="text-lg font-bold mb-4">Delete Course</h2>
            <p class="mb-4">Are you sure you want to delete <span id="deleteProgramCode"></span>? This action cannot be undone.</p>
            <form id="deleteProgramForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeProgramDeleteModal()" class="px-3 py-1 border rounded">Cancel</button>
                    <button id="deleteProgramBtn" type="submit" class="bg-red-600 text-white px-3 py-1 rounded flex items-center">
                        <span class="btn-text">Delete</span>
                        <span class="spinner hidden ml-2 w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="editCollegeModal" class="school-setup-modal">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h2 class="text-lg font-bold mb-4">Edit College</h2>
            <form id="editCollegeForm" method="POST">
                @csrf
                @method('PUT')
                <input type="text" name="name" id="editCollegeName" class="w-full border rounded px-3 py-2 mb-3"
                    placeholder="College of Education" required>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeCollegeEditModal()"
                        class="px-3 py-1 border rounded">Cancel</button>
                    <button id="editCollegeBtn" type="submit"
                        class="bg-blue-600 text-white px-3 py-1 rounded flex items-center">
                        <span class="btn-text">Save</span>
                        <span
                            class="spinner hidden ml-2 w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="deleteCollegeModal" class="school-setup-modal">
        <div class="bg-white p-6 rounded-lg shadow-lg w-96">
            <h2 class="text-lg font-bold mb-4">Delete College</h2>
            <p class="mb-4">Are you sure you want to delete <span id="deleteCollegeName" class="font-semibold"></span>?</p>
            <form id="deleteCollegeForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeCollegeDeleteModal()" class="px-3 py-1 border rounded">Cancel</button>
                    <button id="deleteCollegeBtn" type="submit" class="bg-red-600 text-white px-3 py-1 rounded flex items-center">
                        <span class="btn-text">Delete</span>
                        <span class="spinner hidden ml-2 w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="toastContainer" class="fixed bottom-5 right-5 space-y-2" style="z-index:2100;"></div>
@endsection

@push('scripts')
<script>
window.toggleSchoolSetupPanel = function (selector) {
    var target = document.querySelector(selector);
    if (!target) return;
    target.classList.toggle('is-open');
    target.classList.remove('hidden', 'collapsing', 'collapse', 'show');
    target.style.height = '';
    target.style.overflow = '';
};

window.openSchoolSetupModal = function (id) {
    var modal = document.getElementById(id);
    if (modal) modal.classList.add('is-open');
};

window.closeSchoolSetupModal = function (id) {
    var modal = document.getElementById(id);
    if (modal) modal.classList.remove('is-open');
};

window.openCollegeEditModal = function (collegeId, collegeName) {
    var form = document.getElementById('editCollegeForm');
    form.action = '/prospectus/college/' + collegeId;
    document.getElementById('editCollegeName').value = collegeName;
    window.openSchoolSetupModal('editCollegeModal');
};
window.closeCollegeEditModal = function () {
    window.closeSchoolSetupModal('editCollegeModal');
};
window.openCollegeDeleteModal = function (collegeId, collegeName) {
    var form = document.getElementById('deleteCollegeForm');
    form.action = '/prospectus/college/' + collegeId;
    document.getElementById('deleteCollegeName').textContent = collegeName;
    window.openSchoolSetupModal('deleteCollegeModal');
};
window.closeCollegeDeleteModal = function () {
    window.closeSchoolSetupModal('deleteCollegeModal');
};

window.openDeleteModal = function (courseId, courseCode) {
    document.getElementById('deleteForm').action = '/prospectus/course/' + courseId;
    document.getElementById('deleteMessage').innerText = 'Are you sure you want to delete course "' + courseCode + '"?';
    window.openSchoolSetupModal('deleteModal');
};
window.closeDeleteModal = function () {
    window.closeSchoolSetupModal('deleteModal');
};
window.openEditModal = function (courseId, code, name) {
    document.getElementById('editForm').action = '/prospectus/course/' + courseId;
    document.getElementById('editCourseCode').value = code;
    document.getElementById('editCourseName').value = name;
    window.openSchoolSetupModal('editModal');
};
window.closeEditModal = function () {
    window.closeSchoolSetupModal('editModal');
};
window.openProgramEditModal = function (programId, collegeId, programCode, programName) {
    document.getElementById('editProgramForm').action = '/prospectus/program/' + programId;
    document.getElementById('editProgramCollege').value = collegeId || '';
    document.getElementById('editProgramCode').value = programCode;
    document.getElementById('editProgramName').value = programName;
    window.openSchoolSetupModal('editProgramModal');
};
window.closeProgramEditModal = function () {
    window.closeSchoolSetupModal('editProgramModal');
};
window.openProgramDeleteModal = function (programId, programCode) {
    document.getElementById('deleteProgramForm').action = '/prospectus/program/' + programId;
    document.getElementById('deleteProgramCode').textContent = programCode;
    window.openSchoolSetupModal('deleteProgramModal');
};
window.closeProgramDeleteModal = function () {
    window.closeSchoolSetupModal('deleteProgramModal');
};

(function () {
    var openId = new URLSearchParams(window.location.search).get('open');
    if (openId) {
        var openEl = document.getElementById(openId);
        if (openEl) openEl.classList.add('is-open');
    }
})();
</script>
<script src="{{ asset('js/prospectus.js') }}?v={{ filemtime(public_path('js/prospectus.js')) }}"></script>
@endpush
