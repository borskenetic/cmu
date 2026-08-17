<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Create Book</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('public/css/books/create.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Martel+Sans:wght@900&display=swap" rel="stylesheet">


</head>

<body>
    <!-- Header with Left Logo and Right Logout Button -->
    <div class="d-flex align-items-center px-4 py-2 flex-wrap" style="background-color: white;">
        <img src="{{ asset('images/pantasLogo.png') }}" alt="New Logo" class="header-logo-img" />
        <h1 class="school-name mb-0 ms-2"></h1>

        <!-- IMPORTANT: add ms-auto to push right -->
        <div class="d-flex gap-2 flex-wrap ms-auto" style="margin-right: 9rem;">
            <a href="{{ route('book.index') }}" class="btn1 btn-sm">Home</a>




            <div class="attendance_dropdown">
                <button class="attendance_dropdown-button">Attendance</button>
                <div class="attendance_dropdown-content">
                    <a href="{{ route('attendance.scan') }}">Attendance</a>
                    <a href="{{ route('attendance_logs.index') }}">Attendance-logs</a>

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



            <a href="https://area51lmslibrary.com/user-account/?fbclid=IwY2xjawLvE-xleHRuA2FlbQIxMABicmlkETFHTzhpTjBrRURpVWFFdW9hAR7tC4LGq_N7YomZscUpiyZKJxd0BCy69WYZuj5CxaseF8G5ctGQnauMPJnheg_aem_ZvE4NOhe8ZwtNtoumemmyg"
                class="btn4 btn-sm" target="_blank" rel="noopener noreferrer" hidden>
                51 Learned
            </a>
            <a href="{{ route('files.index') }}" class="btn0 btn-sm">Repository</a>
            <form action="{{ route('logout') }}" method="POST" class="mb-0">
                @csrf
                <button type="submit" class="btn5">Logout</button>
            </form>
        </div>
    </div>

    <div class="edit-container">
        <div class="text-center">
            <div class="edit-header"> Add New Book</div>
        </div>

        <form id="addBookForm" method="POST" action="{{ route('book.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">

                <div class="col-md-6">
                    <label for="control_no">001</label>
                    <input type="text" name="control_no" class="form-control" placeholder="Control No.">
                </div>

                <div class="col-md-6">
                    <label for="marc_timestamp">005</label>
                    <input type="date" name="date_time_stamp" class="form-control"
                        placeholder="Enter Date and Time Stamp">
                </div>

                <div class="col-md-6">
                    <label for="fixed_length_data">008</label>
                    <input type="text" name="fixed_length_data" class="form-control"
                        placeholder="Enter Fixed-Length Data">
                </div>

                <div class="col-md-6">
                    <label for="isbn">020 ‡a</label>
                    <input type="text" name="isbn" class="form-control" placeholder="Enter ISBN">
                </div>

                <div class="col-md-6">
                    <label for="price">020 ‡c</label>
                    <input type="text" name="price" class="form-control" placeholder="Enter Price">
                </div>

                <div class="col-md-6">
                    <label for="cataloging_source">040 ‡a</label>
                    <input type="text" name="cataloging_source_a" class="form-control"
                        placeholder="Enter Cataloging Source">
                </div>

                <div class="col-md-6">
                    <label for="language">040 ‡b</label>
                    <input type="text" name="cataloging_source_b" class="form-control" placeholder="Enter Language">
                </div>

                <div class="col-md-6">
                    <label for="description_conventions">040 ‡e</label>
                    <input type="text" name="cataloging_source_e" value="rda" class="form-control"
                        placeholder="Enter Description Conventions">
                </div>


                <div class="col-md-6">
                    <label for="main_author">100 ‡a</label>
                    <input type="text" name="main_author" class="form-control" placeholder="Enter Main Author">
                </div>

                <div class="col-md-6">
                    <label for="title">245 ‡a</label>
                    <input type="text" name="title_statement" class="form-control" placeholder="Enter Title">
                </div>

                <div class="col-md-6">
                    <label for="title_responsibility">245 ‡c</label>
                    <input type="text" name="title_author" class="form-control"
                        placeholder="Enter Title Responsibility">
                </div>
                <div class="col-md-6">
                    <label for="edition">250</label>
                    <input type="text" name="edition" class="form-control" placeholder="Enter Edition">
                </div>
                <div class="col-md-6">
                    <label for="publication_place">264 ‡a</label>
                    <input type="text" name="pub_place" class="form-control" placeholder="Enter Publication Place">
                </div>
                <div class="col-md-6">
                    <label for="publisher">264 ‡b</label>
                    <input type="text" name="publisher" class="form-control" placeholder="Enter Publisher">
                </div>
                <div class="col-md-6">
                    <label for="publication_year">264 ‡c</label>
                    <input type="text" name="pub_year" class="form-control" placeholder="Enter Publication Year">
                </div>

                <div class="col-md-6">
                    <label for="pages">300 ‡a</label>
                    <input type="text" name="pages" class="form-control" placeholder="Enter Pages">
                </div>
                <div class="col-md-6">
                    <label for="illustrations">300 ‡b</label>
                    <input type="text" name="illustrations" class="form-control" placeholder="Enter Illustrations">
                </div>
                <div class="col-md-6">
                    <label for="size">300 ‡c</label>
                    <input type="text" name="size" class="form-control" placeholder="Enter Size">
                </div>
                <div class="col-md-6">
                    <label for="volume">300 ‡f</label>
                    <input type="text" name="volume" class="form-control" placeholder="Enter Volume">
                </div>
                <div class="col-md-6">
                    <label for="content_type">336 ‡a</label>
                    <input type="text" name="content_type" class="form-control" placeholder="Enter Content Type">
                </div>
                <div class="col-md-6">
                    <label for="media_type">337 ‡a</label>
                    <input type="text" name="media_type" class="form-control" placeholder="Enter Media Type">
                </div>
                <div class="col-md-6">
                    <label for="carrier_type">338 ‡a</label>
                    <input type="text" name="carrier_type" class="form-control" placeholder="Enter Carrier Type">
                </div>
                <div class="col-md-6">
                    <label for="series_title">490 ‡a</label>
                    <input type="text" name="series_title" class="form-control" placeholder="Enter Series Title">
                </div>
                <div class="col-md-6">
                    <label for="general_note">500 ‡a</label>
                    <textarea name="general_note" class="form-control" placeholder="Enter General Note"></textarea>
                </div>
                <div class="col-md-6">
                    <label for="bibliography_note">504</label>
                    <textarea name="bibliography_note" class="form-control"
                        placeholder="Enter Bibliography Note"></textarea>
                </div>
                <div class="col-md-6">
                    <label for="acquisition_source">541</label>
                    <input type="text" name="source_vendor" class="form-control" placeholder="Enter Acquisition Source">
                </div>
                <div class="col-md-6">
                    <label for="acquisition_date">541 ‡a</label>
                    <input type="date" name="source_date" class="form-control" placeholder="Enter Acquisition Date">
                </div>
                <div class="col-md-6">
                    <label for="subject">650 ‡a</label>
                    <input type="text" name="subject_topic" class="form-control" placeholder="Enter Subject">
                </div>
                <div class="col-md-6">
                    <label for="form">650 ‡v</label>
                    <input type="text" name="subject_form" class="form-control" placeholder="Enter Form">
                </div>
                <div class="col-md-6">
                    <label for="genre">655 ‡a</label>
                    <input type="text" name="genre" class="form-control" placeholder="Enter Genre">
                </div>
                <div class="col-md-6">
                    <label for="library_name">852 ‡b</label>
                    <input type="text" name="library_name" class="form-control" placeholder="Enter Library Name">
                </div>
                <div class="col-md-6">
                    <label for="section">852 ‡c</label>
                    <input type="text" name="section" class="form-control" placeholder="Enter Section">
                </div>
                <div class="col-md-6">
                    <label for="call_number">852 ‡h</label>
                    <input type="text" name="call_number" class="form-control" placeholder="Enter Call Number">
                </div>
                <div class="col-md-6">
                    <label for="accession_no">949</label>
                    <input type="text" name="accession_no" class="form-control" placeholder="Enter Accession No.">
                </div>
                <div class="col-md-6">
                    <label for="barcode">876 ‡p</label>
                    <input type="text" name="barcode" class="form-control" placeholder="Enter Barcode">
                </div>
                <div class="col-md-6">
                    <label for="rfid">RFID</label>
                    <input type="text" name="rfid" class="form-control" placeholder="Enter RFID">
                </div>
                <div class="col-md-6">
                    <label for="year">996 ‡e</label>
                    <input type="text" name="year" class="form-control" placeholder="Enter Year">
                </div>
                <div class="col-md-6">
                    <label for="course">650 ‡a</label>
                    <input type="text" name="course" class="form-control" placeholder="Enter Course">
                </div>
                <div class="col-md-6">
                    <label>Program</label>
                    <div id="program-container">
                        <div class="program-row">
                            <select name="program_ids[]" class="form-control mb-2">
                                <option value="">-- Select Program --</option>
                                @foreach($programs as $program)
                                <option value="{{ $program->id }}">{{ $program->program_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <button type="button" id="add-program-btn" class="btn btn-sm btn-secondary mt-1">Add More
                        Program</button>
                </div>


                <div class="col-md-12">
                    <label for="cover_image">856</label>
                    <input type="file" name="cover_image" class="form-control" placeholder="Enter Cover Image">
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('book.index') }}" class="btn btn-back"> Go Back</a>
                    <button type="submit" class="btn btn-save"> Save Book</button>
                </div>
            </div>


        </form>

        @if ($errors->any())
        <div class="alert alert-danger mt-4">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.getElementById('addBookForm');
            form.addEventListener('keydown', e => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    const inputs = [...form.querySelectorAll('input')];
                    const index = inputs.indexOf(document.activeElement);
                    if (inputs[index + 1]) inputs[index + 1].focus();
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const courseDropdown = document.getElementById('course');
            const yearDropdown = document.getElementById('year');
            const programDropdown = document.getElementById('program');

            courseDropdown.addEventListener('change', function () {
                const course = this.value;
                yearDropdown.innerHTML = '<option value="">-- Select Year --</option>';
                programDropdown.innerHTML = '<option value="">-- Select Program --</option>';
                programDropdown.disabled = true;

                if (course) {
                    fetch(`../prospectus/years?course=${course}`)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(year => {
                                yearDropdown.innerHTML += `<option value="${year}">${year}</option>`;
                            });
                            yearDropdown.disabled = false;
                        });
                } else {
                    yearDropdown.disabled = true;
                }
            });

            yearDropdown.addEventListener('change', function () {
                const course = courseDropdown.value;
                const year = this.value;

                programDropdown.innerHTML = '<option value="">-- Select Program --</option>';

                if (course && year) {
                    console.log("Course selected:", course);
                    fetch(`../prospectus/programs?course=${course}&year=${year}`)
                        .then(response => response.json())
                        .then(data => {
                            data.forEach(program => {
                                programDropdown.innerHTML += `<option value="${program}">${program}</option>`;
                            });
                            programDropdown.disabled = false;
                        });
                } else {
                    programDropdown.disabled = true;
                }
            });
        });
    </script>
    
    
    <script>
        const programs = @json($programs);
        const container = document.getElementById('program-container');
        const addBtn = document.getElementById('add-program-btn');
    
        function refreshOptions() {
            const selectedValues = Array.from(document.querySelectorAll('.program-select'))
                .map(sel => sel.value)
                .filter(v => v);
    
            document.querySelectorAll('.program-select').forEach(select => {
                const currentVal = select.value;
                Array.from(select.options).forEach(opt => {
                    if (opt.value && selectedValues.includes(opt.value) && opt.value !== currentVal) {
                        opt.hidden = true;
                    } else {
                        opt.hidden = false;
                    }
                });
            });
        }
    
        addBtn.addEventListener('click', () => {
            const row = document.createElement('div');
            row.classList.add('program-row', 'd-flex', 'align-items-center', 'mb-2');
    
            const select = document.createElement('select');
            select.name = "program_ids[]";
            select.classList.add('form-control', 'program-select', 'me-2');
    
            const defaultOption = document.createElement('option');
            defaultOption.value = "";
            defaultOption.textContent = "-- Select Program --";
            select.appendChild(defaultOption);
    
            programs.forEach(program => {
                const option = document.createElement('option');
                option.value = program.id;
                option.textContent = program.program_name;
                select.appendChild(option);
            });
    
            const removeBtn = document.createElement('button');
            removeBtn.type = "button";
            removeBtn.textContent = "Remove";
            removeBtn.classList.add('btn', 'btn-sm', 'btn-danger', 'remove-program');
    
            row.appendChild(select);
            row.appendChild(removeBtn);
            container.appendChild(row);
    
            refreshOptions();
        });
    
        container.addEventListener('click', (e) => {
            if (e.target.classList.contains('remove-program')) {
                e.target.closest('.program-row').remove();
                refreshOptions();
            }
        });
    
        container.addEventListener('change', (e) => {
            if (e.target.classList.contains('program-select')) {
                refreshOptions();
            }
        });
    
        refreshOptions();
    </script>

    
</body>

</html>