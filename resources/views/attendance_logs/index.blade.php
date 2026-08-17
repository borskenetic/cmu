@extends('layouts.sec')

@section('title', 'Attendance Logs')

@section('content')
<div class="attendance-logs-page">
    <div class="al-toolbar">
        <a href="{{ route('attendance_logs.reports.hub') }}" class="export-btn">Patron reports</a>
        <a href="{{ route('attendance_logs.export.pdf', request()->query()) }}" class="export-btn">Export PDF</a>
        <a href="{{ route('attendance_logs.export.excel', request()->query()) }}" class="export-btn">Export Excel</a>
    </div>

    <div class="al-filters no-bg">
        <form method="GET" class="al-filters-form">
            <div class="al-field">
                <label>Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search name, course, section...">
            </div>
            <div class="al-field" style="flex:0 1 auto;">
                <label>From</label>
                <input type="date" name="from" value="{{ request('from') }}">
            </div>
            <div class="al-field" style="flex:0 1 auto;">
                <label>To</label>
                <input type="date" name="to" value="{{ request('to') }}">
            </div>
            <div class="al-field">
                <label>Section</label>
                <select name="section">
                    <option value="">All Sections</option>
                    @foreach($sections as $section)
                        <option value="{{ $section }}" {{ request('section') == $section ? 'selected' : '' }}>{{ $section }}</option>
                    @endforeach
                </select>
            </div>
            <div class="al-field" style="flex:0 1 120px;">
                <label>Year Level</label>
                <select name="year_level">
                    <option value="">All Levels</option>
                    @foreach(['1','2','3','4','5','6'] as $year)
                        <option value="{{ $year }}" {{ request('year_level') == $year ? 'selected' : '' }}>Year {{ $year }}</option>
                    @endforeach
                </select>
            </div>
            <div class="al-field">
                <label>College of...</label>
                <select name="college_id">
                    <option value="">All Colleges</option>
                    @foreach($colleges as $college)
                        <option value="{{ $college->id }}" {{ (string) request('college_id') === (string) $college->id ? 'selected' : '' }}>{{ $college->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="al-field" style="flex:0 1 auto;">
                <label>Status</label>
                <div class="al-status-btns">
                    <button type="submit" name="status" value="IN" class="al-btn-in">IN Only</button>
                    <button type="submit" name="status" value="OUT" class="al-btn-out">OUT Only</button>
                </div>
            </div>
            <div class="al-field" style="flex:0 1 auto;">
                <label>&nbsp;</label>
                <button type="submit" class="btn-search">Search</button>
            </div>
        </form>
    </div>

    <div class="al-table-wrap">
        <table class="al-table">
            <thead>
                <tr>
                    <th>Last Name</th>
                    <th>First Name</th>
                    <th>Course</th>
                    <th>Section</th>
                    <th>Status</th>
                    <th>Scanned At</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->student ? $log->student->lastname : 'Unknown' }}</td>
                        <td>{{ $log->student ? $log->student->firstname : 'Unknown' }}</td>
                        <td>{{ $log->student ? $log->student->course : 'Unknown' }}</td>
                        <td>{{ $log->section ?? '—' }}</td>
                        <td>
                            @php $status = strtolower($log->status); @endphp
                            @if($status === 'in')
                                <span class="in">IN</span>
                            @elseif($status === 'out')
                                <span class="out">OUT</span>
                            @else
                                <span class="out" style="background:#6b7280;">Unknown</span>
                            @endif
                        </td>
                        <td>
                            {{ $log->scanned_at ? $log->scanned_at->format('Y-m-d h:i A') : '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="al-empty">No attendance records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="d-flex justify-content-center mt-4">
        {{ $logs->withQueryString()->links('pagination::bootstrap-5') }}
    </div>
</div>
@endsection
