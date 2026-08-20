<!DOCTYPE html>
<html lang="en">
<head>
  <title>{{ config('app.name') }} — Attendance</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="{{ asset(config('branding.css_path', 'branding/branding.css')) }}">
  <link rel="stylesheet" href="{{ asset('css/attendance/scan.css') }}?v={{ @filemtime(public_path('css/attendance/scan.css')) }}">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    .marquee-container {
      width: 100%;
      overflow: hidden;
      background-color: #222;
      color: #fff;
      border-top: 2px solid #444;
      padding: 15px 0;
      box-sizing: border-box;
    }
    .marquee {
      display: inline-block;
      white-space: nowrap;
      padding-left: 100%;
      animation: scroll-text 15s linear infinite;
      font-family: 'Poppins', sans-serif;
      font-weight: 700;
      font-size: 24px;
    }
    @keyframes scroll-text {
      0% { transform: translateX(0%); }
      100% { transform: translateX(-100%); }
    }
  </style>
</head>
<body>

<header class="header">
  <div class="header-left">
    <img src="{{ asset('images/pantasLogo.png') }}" alt="Logo">
  </div>
  <div class="header-center">
    <div class="system-title">POWERED BY PANTAS</div>
  </div>
  <div class="header-right" aria-hidden="true"></div>
</header>

<div class="main">
  <div class="sidebar">
    <div class="date" id="currentDate">Date</div>
    <div class="time" id="currentTime">--:--:--</div>
    <div class="profile-pic">
      <img src="{{ asset('images/2x2_undifined_gender.jpg') }}" alt="Default Profile">
    </div>
  </div>

  <div class="right-content">
    <form id="scanForm">
      @csrf
      <textarea name="qrcode" id="qrcode" style="opacity:0; position:absolute;" autofocus autocomplete="off"></textarea>
    </form>
    <video muted autoplay loop controls class="ads-vid">
      <source src="{{ asset('videos/area51_product_slideshow.mp4') }}" type="video/mp4">
    </video>
  </div>
</div>

<footer>
  <div class="footer1">
    <div class="footer-logo">
      <div class="marquee-container">
        <div class="marquee">Welcome to {{ config('app.name') }}</div>
      </div>
    </div>
  </div>
</footer>

<div id="sectionModal" class="section-modal" aria-hidden="true">
  <div class="modal-content section-picker-modal">
    <h2>Select library section</h2>
    <div class="section-buttons" id="sectionButtons" data-count="{{ count($attendanceSections ?? []) }}">
      @forelse($attendanceSections ?? [] as $section)
        <button type="button" data-section="{{ $section }}">{{ $section }}</button>
      @empty
        <p class="section-empty-msg">No sections configured. Add sections under Attendance → Section Picker.</p>
      @endforelse
    </div>
  </div>
</div>

<div id="feedbackModal" class="section-modal" aria-hidden="true">
  <div class="modal-content feedback-card">
    <h2>How was your library experience?</h2>
    <div class="feedback-options">
      <button type="button" data-rating="excellent">😊<span>Excellent</span></button>
      <button type="button" data-rating="good">🙂<span>Good</span></button>
      <button type="button" data-rating="medium">😐<span>Medium</span></button>
      <button type="button" data-rating="poor">🙁<span>Poor</span></button>
      <button type="button" data-rating="very_bad">😠<span>Very Bad</span></button>
    </div>
    <button type="button" id="declineFeedback" class="decline-btn">Skip</button>
  </div>
</div>

<script>
  const LOGOUT_FEEDBACK_ENABLED = @json($logoutFeedbackEnabled ?? true);
  const SECTION_PICKER_ENABLED = @json($sectionPickerEnabled ?? true);
  const HAS_ATTENDANCE_SECTIONS = @json(count($attendanceSections ?? []) > 0);
  const feedbackModal = document.getElementById('feedbackModal');
  const sectionModal = document.getElementById('sectionModal');
  let selectedStudent = null;
  let currentStudentId = null;
  let clearDisplayTimer = null;

  document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('qrcode');
    const profileImg = document.querySelector('.profile-pic img');
    const sidebar = document.querySelector('.sidebar');
    let isCooldown = false;

    setInterval(() => input.focus(), 100);
    input.focus();

    function clearDisplay() {
      if (feedbackModal && feedbackModal.style.display === 'flex') return;
      profileImg.src = "{{ asset('images/2x2_undifined_gender.jpg') }}";
      document.querySelectorAll('.name-box').forEach(box => box.remove());
      selectedStudent = null;
      currentStudentId = null;
    }

    function scheduleClear(delayMs) {
      if (clearDisplayTimer) clearTimeout(clearDisplayTimer);
      clearDisplayTimer = setTimeout(clearDisplay, delayMs);
    }

    function showLogoutFeedback() {
      const enabled = LOGOUT_FEEDBACK_ENABLED;
      if (!enabled || !feedbackModal || !currentStudentId) {
        scheduleClear(2000);
        return;
      }
      if (clearDisplayTimer) {
        clearTimeout(clearDisplayTimer);
        clearDisplayTimer = null;
      }
      setTimeout(() => {
        feedbackModal.style.display = 'flex';
        feedbackModal.setAttribute('aria-hidden', 'false');
      }, 500);
    }

    function profileUrl(path) {
      return path ? "{{ asset('') }}" + path.replace(/^\//, '') : "{{ asset('images/2x2_undifined_gender.jpg') }}";
    }

    input.addEventListener('keypress', function (e) {
      if (e.key !== 'Enter') return;
      e.preventDefault();
      if (isCooldown) return;
      isCooldown = true;
      setTimeout(() => { isCooldown = false; }, 300);

      const formData = new FormData();
      formData.append('qrcode', input.value.trim().replace(/\r/g, ''));
      formData.append('_token', '{{ csrf_token() }}');

      fetch("{{ route('attendance.process') }}", { method: 'POST', body: formData })
        .then(res => res.json())
        .then(data => {
          if (feedbackModal && feedbackModal.style.display === 'flex') {
            closeFeedbackModal();
          }
          clearDisplay();

          if (data.type === 'student') {
            selectedStudent = data.student;
            currentStudentId = data.student_id;
            profileImg.src = profileUrl(data.student.profile_picture);

            if (data.next_status === 'OUT') {
              fetch("{{ route('attendance.section') }}", {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': '{{ csrf_token() }}',
                  'Accept': 'application/json',
                },
                body: JSON.stringify({ student_id: currentStudentId, section: null })
              })
              .then(res => res.json())
              .then(response => {
                const div = document.createElement('div');
                div.classList.add('name-box');
                div.innerHTML = `
                  <div class="student-name">${selectedStudent.firstname} ${selectedStudent.lastname}</div>
                  <div class="label">Name</div>
                  <div class="status-button status-out">OUT</div>
                  <div class="timestamp">${response.scanned_at}</div>
                `;
                sidebar.appendChild(div);

                const feedbackOn = response.logout_feedback_enabled ?? data.logout_feedback_enabled ?? LOGOUT_FEEDBACK_ENABLED;
                if (feedbackOn) {
                  showLogoutFeedback();
                } else {
                  scheduleClear(2000);
                }
              });
            } else {
              const sectionPickerOn = (data.section_picker_enabled ?? SECTION_PICKER_ENABLED) && HAS_ATTENDANCE_SECTIONS;
              if (sectionPickerOn) {
                sectionModal.style.display = 'flex';
                sectionModal.setAttribute('aria-hidden', 'false');
              } else {
                fetch("{{ route('attendance.section') }}", {
                  method: 'POST',
                  headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                  },
                  body: JSON.stringify({ student_id: currentStudentId, section: null })
                })
                .then(res => res.json())
                .then(response => {
                  const div = document.createElement('div');
                  div.classList.add('name-box');
                  div.innerHTML = `
                    <div class="student-name">${selectedStudent.firstname} ${selectedStudent.lastname}</div>
                    <div class="label">Name</div>
                    <div class="status-button">${response.status}</div>
                    <div class="timestamp">${response.scanned_at}</div>
                  `;
                  sidebar.appendChild(div);
                  scheduleClear(3000);
                });
              }
            }
          } else if (data.type === 'error') {
            const div = document.createElement('div');
            div.classList.add('name-box');
            div.innerHTML = `
              <div class="student-name">${data.message}</div>
              <div class="label">Error</div>
            `;
            sidebar.appendChild(div);
            scheduleClear(2000);
          }

          input.value = '';
        })
        .catch(err => console.error(err));
    });

    document.querySelectorAll('.section-buttons button').forEach(btn => {
      btn.addEventListener('click', function () {
        if (!currentStudentId) return;

        fetch("{{ route('attendance.section') }}", {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
          },
          body: JSON.stringify({
            student_id: currentStudentId,
            section: this.dataset.section
          })
        })
        .then(res => res.json())
        .then(response => {
          sectionModal.style.display = 'none';
          sectionModal.setAttribute('aria-hidden', 'true');

          const div = document.createElement('div');
          div.classList.add('name-box');
          div.innerHTML = `
            <div class="student-name">${selectedStudent.firstname} ${selectedStudent.lastname}</div>
            <div class="label">${this.dataset.section}</div>
            <div class="status-button">${response.status}</div>
            <div class="timestamp">${response.scanned_at}</div>
          `;
          sidebar.appendChild(div);
          scheduleClear(3000);
        });
      });
    });

    function closeFeedbackModal() {
      if (!feedbackModal) return;
      feedbackModal.style.display = 'none';
      feedbackModal.setAttribute('aria-hidden', 'true');
    }

    function sendFeedback(rating = null, declined = 0) {
      if (!currentStudentId) {
        closeFeedbackModal();
        clearDisplay();
        input.focus();
        return;
      }

      fetch("{{ route('attendance.feedback.store') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': '{{ csrf_token() }}',
          'Accept': 'application/json',
        },
        body: JSON.stringify({
          student_id: currentStudentId,
          rating: rating,
          declined: declined ? 1 : 0,
        }),
      }).catch(err => console.error(err)).finally(() => {
        closeFeedbackModal();
        clearDisplay();
        input.focus();
      });
    }

    document.querySelectorAll('.feedback-options button').forEach(btn => {
      btn.addEventListener('click', function () {
        sendFeedback(this.dataset.rating, 0);
      });
    });

    document.getElementById('declineFeedback')?.addEventListener('click', function () {
      sendFeedback(null, 1);
    });

    function updateDateTime() {
      const now = new Date();
      const dateEl = document.getElementById('currentDate');
      const timeEl = document.getElementById('currentTime');
      if (dateEl && timeEl) {
        dateEl.textContent = now.toLocaleDateString('en-GB', {
          weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
        });
        timeEl.textContent = now.toLocaleTimeString('en-US');
      }
    }
    updateDateTime();
    setInterval(updateDateTime, 1000);
  });
</script>
</body>
</html>
