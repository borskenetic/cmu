<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create User</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('public/css/books/createuser.css') }}">
    
</head>
<body>
    <!-- Header with Left Logo and Right Logout Button -->
<div class="d-flex align-items-center px-4 py-2 flex-wrap" style="background-color: white; position: relative;">
    <img src="{{ asset('images/pantasLogo.png') }}" alt="New Logo" class="header-logo-img" />
    <h1 class="school-name mb-0 ms-2"></h1>

    <!-- Hamburger Toggle (visible only on small screens) -->
    <button id="customMenuToggle" class="d-md-none toggle-btn">
        &#9776;
    </button>

    <!-- Navigation Wrapper -->
    <div id="routeWrapper" class="d-flex gap-2 flex-wrap ms-auto responsive-nav">
        <!-- Close Button (for mobile view) -->
        <button id="customMenuClose" class="d-md-none close-btn">
            &times;
        </button>

        <a href="{{ route('book.index') }}" class="btn0 btn-sm">Home</a>

        <div class="attendance_dropdown">
            <button class="attendance_dropdown-button">Attendance</button>
            <div class="attendance_dropdown-content">
                <a href="{{ route('attendance.scan') }}">Attendance</a>
                <a href="{{ route('attendance_logs.index') }}">Attendance-logs</a>
            </div>
        </div>

        <a href="{{ route('landing') }}" class="btn2 btn-sm {{ request()->routeIs('books.landing') ? 'active-btn' : '' }}"> OPAC</a>
        <a href="{{ route('users.create') }}" class="btn3 btn-sm">Create Account</a>
         <a href="{{ route('prospectus.index') }}" class="btn3 btn-sm">School Setup</a>

        <div class="logs_dropdown">
            <button class="logs_dropdown-button">Logs</button>
            <div class="logs_dropdown-content">
                <a href="{{ route('logs.index') }}">Logs</a>
                <a href="{{ route('rfid.scanner') }}">RFID Scanner</a>
                <a href="{{ route('book.report.download') }}">Download Book Report</a>
                <a href="{{ route('students.report') }}">Student Report</a>
            </div>
        </div>
        
        <a href="https://area51lmslibrary.com/user-account/?fbclid=IwY2xjawLvE-xleHRuA2FlbQIxMABicmlkETFHTzhpTjBrRURpVWFFdW9hAR7tC4LGq_N7YomZscUpiyZKJxd0BCy69WYZuj5CxaseF8G5ctGQnauMPJnheg_aem_ZvE4NOhe8ZwtNtoumemmyg" 
           class="btn8 btn-sm" 
           target="_blank" 
           rel="noopener noreferrer" hidden>
           51 Learned
        </a>        

        <a href="{{ route('files.index') }}" class="btn4 btn-sm">Repository</a>
        <form action="{{ route('logout') }}" method="POST" class="mb-0">
            @csrf
            <button type="submit" class="btn5">Logout</button>
        </form>
    </div>
</div>

<!-- ✅ JavaScript Toggle Functions -->
<script>
    const toggleBtn = document.getElementById('customMenuToggle');
    const closeBtn = document.getElementById('customMenuClose');
    const routeWrapper = document.getElementById('routeWrapper');

    toggleBtn.addEventListener('click', () => {
        routeWrapper.classList.add('open');
    });

    closeBtn.addEventListener('click', () => {
        routeWrapper.classList.remove('open');
    });

    window.addEventListener('resize', () => {
        if (window.innerWidth >= 768) {
            routeWrapper.classList.remove('open');
        }
    });
</script>
    
<div class="container">
    <div class="card p-4">
        <h3 class="text-center mb-4">Create New User</h3>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('users.store') }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="lname" class="form-label">Last Name</label>
                <input type="text" id="lname" name="lname" value="{{ old('lname') }}" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label for="fname" class="form-label">First Name</label>
                <input type="text" id="fname" name="fname" value="{{ old('fname') }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select id="role" name="role" class="form-select" required>
                    <option value="">Select Role</option>
                    <option value="student" {{ old('role') == 'student' ? 'selected' : '' }}>Student</option>
                    <option value="staff" {{ old('role') == 'staff' ? 'selected' : '' }}>Staff</option>
                    <option value="faculty" {{ old('role') == 'faculty' ? 'selected' : '' }}>Faculty</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary w-100">Create User</button>
        </form>
    </div>
</div>


<!-- Bootstrap JS (Optional) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
