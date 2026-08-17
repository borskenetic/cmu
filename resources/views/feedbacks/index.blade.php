<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>📋 Submitted Feedbacks</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('public/css/books/index.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <!-- Header with Left Logo and Right Logout Button (copied from your Book Kiosk header) -->
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

            <a class="btn0 btn-sm">Home</a>

            <div class="attendance_dropdown">
                <button class="attendance_dropdown-button">Attendance</button>
                <div class="attendance_dropdown-content">
                    <a href="{{ route('attendance.scan') }}">Attendance</a>
                    <a href="{{ route('attendance_logs.index') }}">Attendance-logs</a>
                </div>
            </div>

            <a href="{{ route('landing') }}"
                class="btn2 btn-sm {{ request()->routeIs('books.landing') ? 'active-btn' : '' }}"> OPAC</a>

            <div class="logs_dropdown">
                <button class="logs_dropdown-button">Create Account</button>
                <div class="logs_dropdown-content">
                    <a href="{{ route('users.create') }}">Create Account</a>
                    <a href="{{ route('users.index') }}">View Users</a>
                </div>
            </div>

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

            <a href="{{ route('files.index') }}" class="btn4 btn-sm">Repository</a>

            <div class="logs_dropdown">
                <button class="logs_dropdown-button">Room Reservations</button>
                <div class="logs_dropdown-content">
                    <a href="{{ route('rooms.index') }}">Manage Rooms</a>
                    <a href="{{ route('rooms.book') }}">Book a Room</a>
                    <a href="{{ route('rooms.schedule') }}">View Schedule</a>
                    <a href="{{ route('rooms.pending') }}">Pending Reservations</a>
                    <a href="{{ route('rooms.logs') }}">Reservation Logs</a>
                    <a href="{{ route('feedback.index') }}" class="feedback-link">Show Feedback</a>
                </div>
            </div>

            <form action="{{ route('logout') }}" method="POST" class="mb-0">
                @csrf
                <button type="submit" class="btn5">Logout</button>
            </form>
        </div>
    </div>

    <!-- JS for the mobile menu (copied) -->
    <script>
        const toggleBtn = document.getElementById('customMenuToggle');
        const closeBtn = document.getElementById('customMenuClose');
        const routeWrapper = document.getElementById('routeWrapper');

        if (toggleBtn) {
            toggleBtn.addEventListener('click', () => {
                routeWrapper.classList.add('open');
            });
        }
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                routeWrapper.classList.remove('open');
            });
        }
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) {
                routeWrapper.classList.remove('open');
            }
        });
    </script>

    <!-- Page Banner (kept from your header layout) -->
    <div class="head">
        <img src="{{ asset('images/Bannernew.jpg') }}" alt="Banner" class="banner-img" hidden>
    </div>

    <!-- Feedbacks list content (no footer) -->
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-bold">📋 Submitted Feedbacks</h2>
            <a href="{{ url()->previous() }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if ($feedbacks->isEmpty())
            <div class="text-center text-muted py-5">
                <i class="fas fa-inbox fa-3x mb-3"></i>
                <p>No feedbacks submitted yet.</p>
            </div>
        @else
            <div class="card shadow-sm rounded-4 border-0">
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width:48px">#</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Comments</th>
                                    <th class="text-center" style="width:170px">Submitted At</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($feedbacks as $index => $feedback)
                                    <tr>
                                        <td>{{ $feedbacks->firstItem() + $index }}</td>
                                        <td>{{ $feedback->name ?: 'Anonymous' }}</td>
                                        <td>{{ $feedback->email ?: '—' }}</td>
                                        <td style="max-width:520px; white-space:pre-line;">{{ $feedback->comments }}</td>
                                        <td class="text-center">{{ $feedback->created_at->format('M d, Y h:i A') }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 d-flex justify-content-center">
                        {{ $feedbacks->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Bootstrap JS only (no footer) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
