@extends('layouts.public')

@section('title', 'Registration')

@push('styles')
    <style>
        .reg-card { border-radius: 12px; max-width: 720px; margin: 0 auto; }
        .reg-card h3 { font-size: 2rem; font-weight: 700; color: #000; }
        .hidden { display: none; }
        canvas {
            touch-action: none;
            border: 1px solid #ccc;
            width: 100%;
            max-width: 500px;
            height: 150px;
            border-radius: 6px;
            background-color: #fff;
        }
        .reg-toggle .btn-outline-primary { color: #1f7a1f; border-color: #1f7a1f; font-weight: 600; }
        .reg-toggle .btn-outline-primary.active,
        .reg-toggle .btn-primary { background-color: #1f7a1f; border-color: #1f7a1f; }
        .reg-submit-student { background-color: #1f7a1f; border-color: #1f7a1f; font-weight: 600; }
        .reg-submit-employee { background-color: #1f7a1f; border-color: #1f7a1f; font-weight: 600; }
    </style>
@endpush

@section('content')
    <div class="card shadow-sm reg-card">
        <div class="card-body">
            <h3 class="text-center mb-4">Registration</h3>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0 ps-3">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="text-center mb-4 reg-toggle">
                <button type="button" class="btn btn-primary me-2" id="btnStudent">Student</button>
                <button type="button" class="btn btn-outline-primary" id="btnEmployee">Employee</button>
            </div>

            {{-- STUDENT FORM --}}
            <form id="studentForm" method="POST" action="{{ route('pending.store') }}" enctype="multipart/form-data">
                @csrf
                <h5 class="mb-3">Student Information</h5>

                <div class="row g-3">
                    <div class="col-md-6">
                        <input type="text" name="firstname" class="form-control" placeholder="First Name" required>
                    </div>
                     <div class="col-md-6">
                        <input type="text" name="middle_initial" class="form-control" placeholder="Middle Initial" >
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="lastname" class="form-control" placeholder="Last Name" required>
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="district_id" class="form-control" placeholder="District ID" value="{{ old('district_id') }}">
                        <small class="text-danger" hidden>
                            Please leave it blank if you dont know your District ID. Dont input N/A
                        </small>
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="barcode" class="form-control" placeholder="Barcode (legacy IDs)" value="{{ old('barcode') }}">
                    </div>
                    <div class="col-md-6">
                        <input type="date" name="birth_date" class="form-control" required>
                        <small class="text-danger" hidden>
                            Please enter your <strong>actual birthdate</strong>. Do NOT use today’s date.
                        </small>
                    </div>
                    <div class="col-md-6" hidden>
                        <input type="text" name="blood_type" class="form-control" placeholder="Blood Type">
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="mobile_number" class="form-control" placeholder="Mobile Number" required>
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="course" class="form-control" placeholder="Course" required>
                    </div>

                    <div class="col-md-6">
                        <select name="year" class="form-select" required>
                            <option value="">Select Year</option>
                            @foreach(['1st','2nd','3rd','4th','5th','6th'] as $y)
                                <option value="{{ $y }} Year">{{ $y }} Year</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <input type="text" name="emergency_person" class="form-control" placeholder="Emergency Contact Name" required>
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="emergency_relationship" class="form-control" placeholder="Relationship" required>
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="emergency_number" class="form-control" placeholder="Contact Number" required>
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="emergency_address" class="form-control" placeholder="Address">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Profile Picture</label>
                        <div class="alert alert-warning py-2 mb-2" style="font-size: 14px;">
                            Please upload a <strong>1x1 ID picture</strong> with a <strong>plain white background</strong>.
                        </div>
                        <input type="file" name="profile_picture" class="form-control" accept=".jpg,.jpeg,.png">
                    </div>

                    <div class="col-12">
                        <label class="form-label">Signature (draw below)</label>
                        <canvas id="studentSignaturePad"></canvas>
                        <input type="hidden" name="student_signature" id="studentSignatureInput">
                        <button type="button" id="clearStudentSignature" class="btn btn-sm btn-outline-danger mt-2">Clear</button>
                    </div>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary reg-submit-student">Submit Student Registration</button>
                </div>
            </form>

            {{-- EMPLOYEE FORM --}}
            <form id="employeeForm" method="POST" action="{{ route('pendingEmployee.store') }}" enctype="multipart/form-data" class="hidden">
                @csrf

                <h5 class="mb-3">Employee Information</h5>

                <div class="row g-3">

                    <div class="col-md-6">
                        <input type="text" name="firstname" class="form-control" placeholder="First Name" required>
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="lastname" class="form-control" placeholder="Last Name" required>
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="department" class="form-control" placeholder="Department" required>
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="position" class="form-control" placeholder="Position" required>
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="employee_id" class="form-control" placeholder="Employee ID" required>
                    </div>

                    <div class="col-md-6">
                        <input type="date" name="birth_date" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <select name="sex" class="form-select" required>
                            <option value="">Select Sex</option>
                            <option value="MALE">MALE</option>
                            <option value="FEMALE">FEMALE</option>
                            <option value="OTHER">OTHER</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <input type="text" name="tin_id_number" class="form-control" placeholder="TIN ID Number">
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="philhealth_number" class="form-control" placeholder="PhilHealth Number">
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="sss_number" class="form-control" placeholder="SSS Number">
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="hdmf_number" class="form-control" placeholder="HDMF Number">
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="blood_type" class="form-control" placeholder="Blood Type">
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="civil_status" class="form-control" placeholder="Civil Status">
                    </div>

                    <div class="col-md-6">
                        <input type="text" name="emergency_contact_name" class="form-control" placeholder="Emergency Contact Name" required>
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="emergency_contact_relationship" class="form-control" placeholder="Relationship" required>
                    </div>
                    <div class="col-md-6">
                        <input type="text" name="emergency_contact_number" class="form-control" placeholder="Contact Number" required>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Formal Picture</label>
                        <input type="file" name="formal_picture" class="form-control" accept=".jpg,.jpeg,.png">
                    </div>

                    <div class="col-md-12">
                        <label class="form-label">Address</label>
                        <textarea name="address" class="form-control" rows="2" placeholder="Home Address"></textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Signature (draw below)</label>
                        <canvas id="employeeSignaturePad"></canvas>
                        <input type="hidden" name="employee_signature" id="employeeSignatureInput">
                        <button type="button" id="clearEmployeeSignature" class="btn btn-sm btn-outline-danger mt-2">Clear</button>
                    </div>
                </div>

                <div class="d-grid mt-4">
                    <button type="submit" class="btn btn-primary reg-submit-employee">Submit Employee Registration</button>
                </div>
            </form>

        </div>
    </div>

<script>
/* SIGNATURE PAD FUNCTION */
function setupSignaturePad(canvasId, inputId, clearBtnId) {
    const canvas = document.getElementById(canvasId);
    const ctx = canvas.getContext('2d');
    let drawing = false;
    let points = [];

    function resizeCanvas() {
        const dataUrl = canvas.toDataURL();
        canvas.width = canvas.offsetWidth;
        canvas.height = canvas.offsetHeight;
        const img = new Image();
        img.src = dataUrl;
        img.onload = () => ctx.drawImage(img, 0, 0);
    }

    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);

    canvas.style.touchAction = 'none';

    function getPos(e) {
        const rect = canvas.getBoundingClientRect();
        const scaleX = canvas.width / rect.width;
        const scaleY = canvas.height / rect.height;
        const clientX = e.touches ? e.touches[0].clientX : e.clientX;
        const clientY = e.touches ? e.touches[0].clientY : e.clientY;
        return {
            x: (clientX - rect.left) * scaleX,
            y: (clientY - rect.top) * scaleY
        };
    }

    function startDrawing(e) {
        e.preventDefault();
        drawing = true;
        points = [];
        points.push(getPos(e));
    }

    function draw(e) {
        e.preventDefault();
        if (!drawing) return;

        const pos = getPos(e);
        points.push(pos);

        ctx.lineCap = 'round';
        ctx.strokeStyle = '#000';

        if (points.length === 1) {
            ctx.beginPath();
            ctx.arc(pos.x, pos.y, 1.5, 0, Math.PI * 2);
            ctx.fill();
            return;
        }

        ctx.beginPath();

        const last = points[points.length - 2];
        const dx = pos.x - last.x;
        const dy = pos.y - last.y;
        const speed = Math.sqrt(dx*dx + dy*dy);

        ctx.lineWidth = Math.max(1, 4 - speed / 2);
        ctx.moveTo(last.x, last.y);
        ctx.lineTo(pos.x, pos.y);

        ctx.stroke();
    }

    function stopDrawing() {
        if (!drawing) return;
        drawing = false;
        document.getElementById(inputId).value = canvas.toDataURL();
    }

    canvas.addEventListener('mousedown', startDrawing);
    canvas.addEventListener('mousemove', draw);
    canvas.addEventListener('mouseup', stopDrawing);
    canvas.addEventListener('mouseleave', stopDrawing);

    canvas.addEventListener('touchstart', startDrawing);
    canvas.addEventListener('touchmove', draw);
    canvas.addEventListener('touchend', stopDrawing);

    document.getElementById(clearBtnId).addEventListener('click', () => {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        points = [];
        document.getElementById(inputId).value = '';
    });

    return { resize: resizeCanvas };
}

const studentPad = setupSignaturePad('studentSignaturePad', 'studentSignatureInput', 'clearStudentSignature');
const employeePad = setupSignaturePad('employeeSignaturePad', 'employeeSignatureInput', 'clearEmployeeSignature');

/* FORM TOGGLE */
const btnStudent = document.getElementById('btnStudent');
const btnEmployee = document.getElementById('btnEmployee');

btnStudent.addEventListener('click', () => {
    document.getElementById('studentForm').classList.remove('hidden');
    document.getElementById('employeeForm').classList.add('hidden');
    btnStudent.className = 'btn btn-primary me-2';
    btnEmployee.className = 'btn btn-outline-primary';
    setTimeout(() => studentPad.resize(), 50);
});

btnEmployee.addEventListener('click', () => {
    document.getElementById('employeeForm').classList.remove('hidden');
    document.getElementById('studentForm').classList.add('hidden');
    btnEmployee.className = 'btn btn-primary me-2';
    btnStudent.className = 'btn btn-outline-primary';
    setTimeout(() => employeePad.resize(), 50);
});
</script>
@endsection
