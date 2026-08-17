<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('public/css/books/logs.css') }}">
</head>



<body class="bg">
    
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

        <a href="{{ route('landing') }}" class="btn1 btn-sm {{ request()->routeIs('books.landing') ? 'active-btn' : '' }}"> OPAC</a>
        <a href="{{ route('users.create') }}" class="btn2 btn-sm">Create Account</a>
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
           rel="noopener noreferrer"hidden>
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
    
    <header>
        <div class="header d-flex align-items-center justify-content-center position-relative">
        <!--<img src="{{ asset('images/logo.png') }}" alt="Logo" 
            style="height: 150px; position: absolute; left: 0;">-->
        <h1 class="text-center w-100">Book Check-In & Check-Out Kiosk</h1>
    </div>
    </header>
    
<div class="container mt-5">
    

        <!-- Display Success Message -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Display Error Message -->
    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif



    <form action="{{ route('logs.store') }}" method="POST" class="mt-4">
        @csrf
        <div class="mb-3">
            <label for="rfid" class="form-label">RFID Tag:</label>
            <input type="text" style="border-radius: 50px;" class="form-control" name="rfid" value="{{ request('rfid') }}" placeholder="" required>
        </div>
        <div class="mb-3">
            <label for="patron_name" class="form-label">Patron's Name:</label>
            <input type="text" style="border-radius: 50px;" class="form-control" name="patron_name" value="{{ request('patron_name') }}" placeholder="" required>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Action:</label>
            <select class="form-select" style="border-radius: 50px;" name="status" required>
                <option value="checked_out" {{ request('status') == 'checked_out' ? 'selected' : '' }}>Check Out</option>
                <option value="checked_in" {{ request('status') == 'checked_in' ? 'selected' : '' }}>Check In</option>
            </select>
        </div>
        <div class="button-container">
        <div class="left-buttons but_buttons">
                <button type="submit" class="btn11">Record Transaction</button>
                <a href="{{ url('/export-transactions') }}" class="btn12">Download Transactions Report</a>
        </div>
                <a href="{{ route('book.index') }}" class="btn13">Go to Books</a>
        </div>

        
    </form>
    
    
    
    <form method="GET" action="{{ route('logs.index') }}" class="row g-3 align-items-end mb-4">
    <div class="col-md-4">
        <label for="patron_name" class="form-label">Filter by Patron Name</label>
        <select class="form-select" name="patron_name">
            <option value="">All</option>
            @foreach ($patronNames as $name)
                <option value="{{ $name }}" {{ request('patron_name') == $name ? 'selected' : '' }}>{{ $name }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label for="book_title" class="form-label">Filter by Book Title</label>
        <select class="form-select" name="book_title">
            <option value="">All</option>
            @foreach ($bookTitles as $title)
                <option value="{{ $title }}" {{ request('book_title') == $title ? 'selected' : '' }}>{{ $title }}</option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2">
        <label for="start_date" class="form-label">Start Date</label>
        <input type="date" class="form-control" name="start_date" value="{{ request('start_date') }}">
    </div>

    <div class="col-md-2">
        <label for="end_date" class="form-label">End Date</label>
        <input type="date" class="form-control" name="end_date" value="{{ request('end_date') }}">
    </div>

    <div class="col-md-12 d-flex justify-content-end mt-2">
        <button type="submit" class="btn btn-primary me-2">Apply Filters</button>
        <a href="{{ route('logs.index') }}" class="btn btn-secondary">Reset</a>
    </div>
</form>

    
    
    
    
    <h3 class="transact mt-5">Transaction Logs</h3>
    <div class="table-responsive">
    <table class="table table-striped align-midle">
        <thead class="tabel-dark">
        <tr>
            <th>Book Title</th>
            <th>Barcode</th>
            <th>Patron Name</th> 
            <th>Status</th>
            <th>Timestamp</th>
        </tr>
        </thead>
        <tbody>
        @forelse($logs as $log)
            <tr>
                 <td>{{ $log->book->title_statement ?? 'N/A' }}</td>
                <td>{{ $log->book->barcode ?? 'N/A' }}</td>
                <td>{{ $log->patron_name }}</td> 
                <td>{{ ucfirst($log->status) }}</td>
              <td>{{ $log->timestamp_manila ?? '—' }}</td>


            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center text-muted">No transactions found.</td>
            </tr>
        @endforelse
        </tbody>
    </table>
    <div class="d-flex justify-content-center mt-3">
        {{ $logs->links('pagination::bootstrap-5') }}
    </div>
    </div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        const form = document.querySelector("form"); // Select the form

        form.addEventListener("keydown", function(event) {
            if (event.key === "Enter") {
                event.preventDefault(); // Stop form submission
            }
        });
    });
</script>
    </div>




<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</footer>
</body>
</html>
