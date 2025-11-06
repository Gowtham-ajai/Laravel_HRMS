@extends('layouts.master')
@section('title','Employee Dashboard')

@section('content')

<div class="container-fluid">
    <div class="row">

        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 p-0">
            <div class="d-flex flex-column flex-shrink-0 sidebar-theme vh-100 p-3 text-light shadow-lg">
                <div class="text-center mb-4">
                    <img id="sidebarPhoto" src="{{ asset('storage/'.$employee->photo) }}" 
                         alt="Photo" 
                         class="img-fluid rounded-circle border border-light mb-2" 
                         style="width:100px;height:100px;">
                    <h5 class="mb-0">{{ $employee->first_name }} {{ $employee->last_name }}</h5>
                    <small class="text-muted">{{ $employee->role }}</small>
                </div>

                <hr class="text-secondary">

                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item mb-2">
                        <a href="#" class="nav-link text-light active" data-section="details">
                            <i class="bi bi-person-circle me-2"></i> Details
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="#" class="nav-link text-light" data-section="attendance">
                            <i class="bi bi-clock-history me-2"></i> Attendance
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="#" class="nav-link text-light" data-section="leaves">
                            <i class="bi bi-calendar-check me-2"></i> Leaves
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#" class="nav-link text-light" data-section="settings">
                            <i class="bi bi-gear-fill me-2"></i> Settings
                        </a>
                    </li>
                    <!-- Logout Section -->
                    <li class="nav-item mt-3">
                        <a href="{{ route('logout') }}" class="nav-link text-light d-flex align-items-center"
                        onclick="event.preventDefault(); document.getElementById('logoutForm').submit();">
                            <i class="bi bi-box-arrow-right me-2"></i> Logout
                        </a>
                        <form id="logoutForm" action="{{ route('logout') }}" method="POST" class="d-none">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Main Content Area -->
        <div class="col-md-9 col-lg-10 p-4" id="dashboardContent">

            <!-- Default section: Employee Details -->
            <div id="detailsSection">
                <div class="card shadow border-0">
                    <div class="card-header text-white" style="background: linear-gradient(90deg, #1e3c72, #2a5298);">
                        <h5 class="mb-0">Employee Details</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                             <li class="list-group-item">
                                <strong>Employee ID:</strong> {{ $employee->employee_id ?? $employee->id }}
                            </li>
                            
                             <li class="list-group-item">
                                <strong>Employee Name:</strong> {{ $employee->first_name }} {{ $employee->last_name }}
                            </li>

                            <li class="list-group-item">
                                <strong>Department:</strong> {{ $employee->department->department_name ?? 'N/A' }}
                            </li>

                            <li class="list-group-item">
                                <strong>Designation:</strong> {{ $employee->designation->designation ?? 'N/A' }}
                            </li>

                            <li class="list-group-item">
                                <strong>Birthday:</strong> {{ \Carbon\Carbon::parse($employee->date_of_birth)->format('d-M-Y') }}
                            </li>

                            <li class="list-group-item">
                                <strong>Age:</strong> {{ $employee->age() }}
                            </li>

                            <li class="list-group-item">
                                <strong>Gender</strong> {{ $employee->gender }}
                            </li>
                            <li class="list-group-item">
                                <strong>Email:</strong>{{ $employee->email }}
                            </li>

                            <li class="list-group-item">
                                <strong>Phone:</strong> {{ $employee->phone }}
                            </li>

                            <li class="list-group-item">
                                <strong>Joined:</strong> {{ \Carbon\Carbon::parse($employee->date_of_joining)->format('d-M-Y') }}
                            </li>

                            <li class="list-group-item">
                                <strong>Contract:</strong> {{ $employee->contractDuration() }}
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Attendance Section -->
            <div id="attendanceSection" class="d-none">
                <div class="attendance-card">
                    <div class="attendance-header">
                        <h1><i class="bi bi-clock-history"></i>Employee Attendance System</h1>
                        <p class="attendance-subtitle">Manage your daily attendance and breaks</p>
                    </div>
                    
                    <div class="status-container" id="statusContainer">
                        <div class="status-label">Current Status</div>
                        <div class="status-value" id="statusDisplay">Not Checked In</div>
                    </div>
                    
                    <div class="timer-container">
                        <div class="timer-card work">
                            <div class="timer-label">Total Working Time</div>
                            <div class="timer-value" id="totalWorkTime">00:00:00</div>
                        </div>
                        
                        <div class="timer-card break">
                            <div class="timer-label">Break Duration</div>
                            <div class="timer-value break-timer-value" id="breakTime">00:00:00</div>
                        </div>
                    </div>
                    
                    <div class="buttons-container">
                        <button id="checkinBtn" class="attendance-btn btn-checkin pulse" onclick="checkIn()">
                            <i class="bi bi-box-arrow-in-right"></i> Check In
                        </button>
                        <button id="checkoutBtn" class="attendance-btn btn-checkout" onclick="checkOut()" disabled>
                            <i class="bi bi-box-arrow-right"></i> Check Out
                        </button>
                        <button id="breakinBtn" class="attendance-btn btn-breakin" onclick="breakIn()" disabled>
                            <i class="bi bi-cup-straw"></i> Break In
                        </button>
                        <button id="breakoutBtn" class="attendance-btn btn-breakout" onclick="breakOut()" disabled>
                            <i class="bi bi-cup"></i> Break Out
                        </button>
                    </div>
                </div>
            </div>

            <!-- Leaves Section -->
            <div id="leavesSection" class="d-none">
                <div class="card shadow-sm border-0 mb-3">
                    <div class="card-header text-white" 
                        style="background: linear-gradient(90deg, #1e3c72, #2a5298);">
                        <h5 class="mb-0 fw-bold">Apply Leave</h5>
                    </div>

                    <div class="card-body">
                        <form id="applyLeaveForm">
                            @csrf
                            <div class="mb-2">
                                <label for="leave_type">Leave Type</label>
                                <select name="leave_type" id="leave_type" class="form-control" required>
                                    <option value="Sick">Sick</option>
                                    <option value="Casual">Casual</option>
                                    <option value="Annual">Annual</option>
                                    <option value="Unpaid">Unpaid</option>
                                </select>
                            </div>

                            <div class="mb-2">
                                <label for="start_date">Start Date</label>
                                <input type="date" name="start_date" id="start_date" class="form-control" required>
                            </div>

                            <div class="mb-2">
                                <label for="end_date">End Date</label>
                                <input type="date" name="end_date" id="end_date" class="form-control" required>
                            </div>

                            <div class="mb-2">
                                <label for="remarks">Remarks</label>
                                <textarea name="remarks" id="remarks" class="form-control" rows="3"></textarea>
                            </div>

                            <button type="submit" 
                                class="btn border-0 text-white fw-semibold px-4 py-2 rounded-pill"
                                style="background: linear-gradient(90deg, #1e3c72, #2a5298); transition: 0.3s;">
                                Apply Leave
                            </button>

                        </form>

                        <div id="successMessage" class="alert alert-success mt-3 d-none">
                            Leave applied successfully!
                        </div>
                    </div>
                </div>


                <!-- Leave History -->
                <div class="card shadow-sm border-0">
                    <div class="card-header text-white"
                        style="background: linear-gradient(90deg, #1e3c72, #2a5298);">
                        <h5 class="mb-0 fw-bold">My Leaves</h5>
                    </div>

                    <div class="card-body">
                        <table class="table table-bordered" id="leavesTable">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Start</th>
                                    <th>End</th>
                                    <th>Status</th>
                                    <th>Remarks</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($leaves as $leave)
                                    <tr id="leave-{{ $leave->id }}">
                                        <td>{{ $leave->leave_type }}</td>
                                        <td>{{ $leave->start_date }}</td>
                                        <td>{{ $leave->end_date }}</td>
                                        <td>
                                            <span class="badge 
                                                @if($leave->status == 'Pending') bg-warning
                                                @elseif($leave->status == 'Approved') bg-success
                                                @else bg-danger
                                                @endif">{{ $leave->status }}</span>
                                        </td>
                                        <td>{{ $leave->remarks }}</td>
                                        <td>—</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Settings Section -->
            <div id="settingsSection" class="d-none">
                <div class="card shadow border-0">
                    <div class="card-header text-white" style="background: linear-gradient(90deg, #1e3c72, #2a5298);">
                        <h5 class="mb-0">Employee Settings</h5>
                    </div>
                    <div class="card-body">
                        <!-- Employee Details -->
                        <ul class="list-group list-group-flush" id="employeeDetails">
                            <li class="list-group-item"><strong>ID:</strong> {{ $employee->employee_id ?? $employee->id }}</li>
                            <li class="list-group-item"><strong>Name:</strong> {{ $employee->first_name }} {{ $employee->last_name }}</li>
                            <li class="list-group-item"><strong>Department:</strong> {{ $employee->department->department_name ?? 'N/A' }}</li>
                            <li class="list-group-item"><strong>Designation:</strong> {{ $employee->designation->designation ?? 'N/A' }}</li>
                            <li class="list-group-item"><strong>Age:</strong> {{ $employee->age() }}</li>
                            <li class="list-group-item"><strong>Gender:</strong> {{ $employee->gender }}</li>
                            <li class="list-group-item"><strong>Email:</strong> {{ $employee->email }}</li>
                            <li class="list-group-item"><strong>Phone:</strong> <span id="phoneDisplay">{{ $employee->phone }}</span></li>
                            <li class="list-group-item"><strong>Joined:</strong> {{ \Carbon\Carbon::parse($employee->date_of_joining)->format('d-M-Y') }}</li>
                            <li class="list-group-item"><strong>Profile Picture:</strong>
                                <br>
                                <img src="{{ asset('storage/'.$employee->photo) }}" alt="Photo" class="img-fluid rounded" style="width:100px;" id="photoDisplay">
                            </li>
                        </ul>

                        <button class="btn btn-gradient mt-3" id="editBtn">Edit</button>

                        <!-- Edit Form (hidden initially) -->
                        <form id="settingsForm" class="mt-3 d-none" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="phoneInput" class="form-label">Phone</label>
                                <input type="text" id="phoneInput" name="phone" class="form-control" value="{{ $employee->phone }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" id="new_password" name="new_password" class="form-control" placeholder="Enter new password">
                                <div class="form-text">Leave blank if you don't want to change password</div>
                                <div id="passwordSameError" class="text-danger small d-none">New password cannot be the same as current password</div>
                            </div>

                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirm Password</label>
                                <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="Confirm new password">
                                <div id="passwordMatchError" class="text-danger small d-none">Passwords do not match</div>
                            </div>

                            <div class="mb-3">
                                <label for="photoInput" class="form-label">Profile Picture</label>
                                <input type="file" id="photoInput" name="photo" class="form-control" accept="image/*">
                            </div>

                            <button type="submit" class="btn btn-save">Save Changes</button>
                            <button type="button" class="btn btn-cancel" id="cancelEdit">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection



<!-- Add this to your head section -->
<link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet"> 
<!-- sweet alert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
<!-- Toastr CSS & JS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

{{-- Employee attendance --}}
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>  
    // Global variables - these persist across page refreshes using localStorage
    let workTimer = null;
    let breakTimer = null;
    let totalWorkSeconds = 0;
    let totalBreakSeconds = 0;
    let isOnBreak = false;
    let currentBreakStartTime = null;
    let currentStatus = 'checked_out';
    let totalBreaksTaken = 0;
    let isInitialized = false;
    let lastCheckInTime = null;

    // Local Storage Keys
    const STORAGE_KEYS = {
        WORK_SECONDS: 'attendance_work_seconds',
        BREAK_SECONDS: 'attendance_break_seconds',
        IS_ON_BREAK: 'attendance_is_on_break',
        CURRENT_STATUS: 'attendance_current_status',
        BREAKS_TAKEN: 'attendance_breaks_taken',
        CHECKIN_TIME: 'attendance_checkin_time',
        LAST_SYNC: 'attendance_last_sync',
        ACTIVE_SECTION: 'attendance_active_section',
        // SUMMARY PERSISTENCE
        SUMMARY_VISIBLE: 'attendance_summary_visible',
        SUMMARY_CHECKIN: 'attendance_summary_checkin',
        SUMMARY_CHECKOUT: 'attendance_summary_checkout',
        SUMMARY_WORK_HOURS: 'attendance_summary_work_hours',
        SUMMARY_BREAK_HOURS: 'attendance_summary_break_hours',
        SUMMARY_BREAKS_COUNT: 'attendance_summary_breaks_count'
    };

    // ========== SECTION NAVIGATION FUNCTIONS ==========

    // Save active section to localStorage
    function saveActiveSection(section) {
        try {
            localStorage.setItem(STORAGE_KEYS.ACTIVE_SECTION, section);
            console.log('Active section saved:', section);
        } catch (error) {
            console.error('Error saving active section:', error);
        }
    }

    // Load active section from localStorage
    function loadActiveSection() {
        try {
            return localStorage.getItem(STORAGE_KEYS.ACTIVE_SECTION) || 'details';
        } catch (error) {
            console.error('Error loading active section:', error);
            return 'details';
        }
    }

    // Function to switch sections programmatically
    function switchToSection(section) {
        console.log('Switching to section:', section);
        
        // Remove active class from all nav links
        $('.sidebar-theme .nav-link').removeClass('active bg-primary');
        
        // Add active class to clicked nav link
        $(`.sidebar-theme .nav-link[data-section="${section}"]`).addClass('active bg-primary');

        // Hide all sections
        $('#detailsSection, #attendanceSection, #leavesSection, #settingsSection').addClass('d-none');

        // Show selected section
        $(`#${section}Section`).removeClass('d-none');

        // Save active section
        saveActiveSection(section);

        // If switching to attendance section, update the UI
        if (section === 'attendance') {
            console.log('Switched to attendance section, updating UI...');
            setTimeout(() => {
                updateAttendanceUI();
                // Load summary if it exists
                loadAndShowSummary();
            }, 100);
        }
    }

    // ========== SUMMARY PERSISTENCE FUNCTIONS ==========

    // Save summary data to localStorage
    function saveSummaryData(attendance) {
        try {
            localStorage.setItem(STORAGE_KEYS.SUMMARY_VISIBLE, 'true');
            localStorage.setItem(STORAGE_KEYS.SUMMARY_CHECKIN, attendance.check_in || '');
            localStorage.setItem(STORAGE_KEYS.SUMMARY_CHECKOUT, attendance.check_out || '');
            localStorage.setItem(STORAGE_KEYS.SUMMARY_WORK_HOURS, attendance.total_work_hours?.toString() || '0');
            localStorage.setItem(STORAGE_KEYS.SUMMARY_BREAK_HOURS, attendance.total_break_hours?.toString() || '0');
            localStorage.setItem(STORAGE_KEYS.SUMMARY_BREAKS_COUNT, (attendance.breaks ? attendance.breaks.length : 0).toString());
            
            console.log('Summary data saved to localStorage');
        } catch (error) {
            console.error('Error saving summary data:', error);
        }
    }

    // Load and display saved summary data
    function loadAndShowSummary() {
        try {
            const summaryVisible = localStorage.getItem(STORAGE_KEYS.SUMMARY_VISIBLE);
            
            if (summaryVisible === 'true') {
                const checkin = localStorage.getItem(STORAGE_KEYS.SUMMARY_CHECKIN) || '-';
                const checkout = localStorage.getItem(STORAGE_KEYS.SUMMARY_CHECKOUT) || '-';
                const workHours = parseFloat(localStorage.getItem(STORAGE_KEYS.SUMMARY_WORK_HOURS) || '0');
                const breakHours = parseFloat(localStorage.getItem(STORAGE_KEYS.SUMMARY_BREAK_HOURS) || '0');
                const breaksCount = parseInt(localStorage.getItem(STORAGE_KEYS.SUMMARY_BREAKS_COUNT) || '0');
                
                console.log('Loading saved summary data:', {
                    checkin, checkout, workHours, breakHours, breaksCount
                });
                
                // Show the summary with saved data
                showSummaryFromData(checkin, checkout, workHours, breakHours, breaksCount);
                return true;
            }
            return false;
        } catch (error) {
            console.error('Error loading summary data:', error);
            return false;
        }
    }

    // Show summary from data (without needing attendance object)
    function showSummaryFromData(checkin, checkout, workHours, breakHours, breaksCount) {
        if (!isAttendanceSectionVisible()) return;
        
        const summaryElement = document.getElementById('summary');
        if (summaryElement) {
            summaryElement.style.display = 'block';
            document.getElementById('summaryCheckin').textContent = checkin;
            document.getElementById('summaryCheckout').textContent = checkout;
            
            // Convert decimal hours to hh:mm:ss format
            const workTimeFormatted = decimalHoursToTime(workHours);
            const breakTimeFormatted = decimalHoursToTime(breakHours);
            
            document.getElementById('summaryWorkHours').textContent = workTimeFormatted;
            document.getElementById('summaryBreakHours').textContent = breakTimeFormatted;
            document.getElementById('summaryBreaks').textContent = breaksCount;
            
            console.log('Summary displayed from saved data');
        }
    }

    // Clear summary data from localStorage
    function clearSummaryData() {
        try {
            localStorage.removeItem(STORAGE_KEYS.SUMMARY_VISIBLE);
            localStorage.removeItem(STORAGE_KEYS.SUMMARY_CHECKIN);
            localStorage.removeItem(STORAGE_KEYS.SUMMARY_CHECKOUT);
            localStorage.removeItem(STORAGE_KEYS.SUMMARY_WORK_HOURS);
            localStorage.removeItem(STORAGE_KEYS.SUMMARY_BREAK_HOURS);
            localStorage.removeItem(STORAGE_KEYS.SUMMARY_BREAKS_COUNT);
            
            console.log('Summary data cleared from localStorage');
        } catch (error) {
            console.error('Error clearing summary data:', error);
        }
    }

    // ========== TIMER AND ATTENDANCE FUNCTIONS ==========

    // Save current state to localStorage
    function saveTimerState() {
        try {
            localStorage.setItem(STORAGE_KEYS.WORK_SECONDS, totalWorkSeconds.toString());
            localStorage.setItem(STORAGE_KEYS.BREAK_SECONDS, totalBreakSeconds.toString());
            localStorage.setItem(STORAGE_KEYS.IS_ON_BREAK, isOnBreak.toString());
            localStorage.setItem(STORAGE_KEYS.CURRENT_STATUS, currentStatus);
            localStorage.setItem(STORAGE_KEYS.BREAKS_TAKEN, totalBreaksTaken.toString());
            if (lastCheckInTime) {
                localStorage.setItem(STORAGE_KEYS.CHECKIN_TIME, lastCheckInTime);
            }
            localStorage.setItem(STORAGE_KEYS.LAST_SYNC, Date.now().toString());
            
            console.log('Timer state saved to localStorage');
        } catch (error) {
            console.error('Error saving timer state:', error);
        }
    }

    // Enhanced loadTimerState with recovery
    function loadTimerState() {
        try {
            const savedWorkSeconds = localStorage.getItem(STORAGE_KEYS.WORK_SECONDS);
            const savedBreakSeconds = localStorage.getItem(STORAGE_KEYS.BREAK_SECONDS);
            const savedIsOnBreak = localStorage.getItem(STORAGE_KEYS.IS_ON_BREAK);
            const savedStatus = localStorage.getItem(STORAGE_KEYS.CURRENT_STATUS);
            const savedBreaksTaken = localStorage.getItem(STORAGE_KEYS.BREAKS_TAKEN);
            const savedCheckinTime = localStorage.getItem(STORAGE_KEYS.CHECKIN_TIME);

            // Validate and parse with defaults
            totalWorkSeconds = Math.max(0, parseInt(savedWorkSeconds) || 0);
            totalBreakSeconds = Math.max(0, parseInt(savedBreakSeconds) || 0);
            isOnBreak = savedIsOnBreak === 'true';
            currentStatus = savedStatus || 'checked_out';
            totalBreaksTaken = Math.max(0, parseInt(savedBreaksTaken) || 0);
            lastCheckInTime = savedCheckinTime;

            // Additional validation for status
            if (!['checked_out', 'checked_in', 'on_break'].includes(currentStatus)) {
                currentStatus = 'checked_out';
            }

            console.log('Timer state loaded from localStorage:', {
                workSeconds: totalWorkSeconds,
                breakSeconds: totalBreakSeconds,
                isOnBreak: isOnBreak,
                status: currentStatus,
                breaksTaken: totalBreaksTaken,
                checkinTime: lastCheckInTime
            });

            return true;
        } catch (error) {
            console.error('Error loading timer state:', error);
            // Reset to safe state
            totalWorkSeconds = 0;
            totalBreakSeconds = 0;
            isOnBreak = false;
            currentStatus = 'checked_out';
            totalBreaksTaken = 0;
            lastCheckInTime = null;
            return false;
        }
    }

    // Clear localStorage (on checkout)
    function clearTimerState() {
        try {
            // Save the active section first
            const activeSection = localStorage.getItem(STORAGE_KEYS.ACTIVE_SECTION);
            
            // Clear all timer-related keys BUT KEEP SUMMARY DATA
            Object.values(STORAGE_KEYS).forEach(key => {
                // Don't clear active section and summary-related keys
                if (key !== STORAGE_KEYS.ACTIVE_SECTION && 
                    !key.startsWith('attendance_summary_') &&
                    key !== STORAGE_KEYS.SUMMARY_VISIBLE) {
                    localStorage.removeItem(key);
                }
            });
            
            // Restore active section if it exists
            if (activeSection) {
                localStorage.setItem(STORAGE_KEYS.ACTIVE_SECTION, activeSection);
            }
            
            console.log('Timer state cleared from localStorage (summary and active section preserved)');
        } catch (error) {
            console.error('Error clearing timer state:', error);
        }
    }

    // Clear everything including summary (for logout or new day)
    function clearAllAttendanceData() {
        try {
            Object.values(STORAGE_KEYS).forEach(key => {
                localStorage.removeItem(key);
            });
            console.log('All attendance data cleared from localStorage');
        } catch (error) {
            console.error('Error clearing all attendance data:', error);
        }
    }

    //track emp checkin time how long(refresh page, close & open the browser)
    function calculateElapsedTime() {
        try {
            const lastSync = localStorage.getItem(STORAGE_KEYS.LAST_SYNC);
            if (!lastSync) return 0;
            
            const lastSyncTime = parseInt(lastSync);
            const now = Date.now();
            
            // Ensure we don't get negative elapsed time (clock changes, etc.)
            const elapsed = Math.max(0, now - lastSyncTime);
            return Math.floor(elapsed / 1000); // Convert to seconds
        } catch (error) {
            console.error('Error calculating elapsed time:', error);
            return 0;
        }
    }

    // Convert decimal hours to hh:mm:ss format - FIXED VERSION
    function decimalHoursToTime(decimalHours) {
        if (!decimalHours || decimalHours === 0 || isNaN(decimalHours)) return '00:00:00';
        
        // Convert decimal hours to total seconds
        const totalSeconds = Math.round(decimalHours * 3600);
        const hours = Math.floor(totalSeconds / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;
        
        return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }

    // Convert seconds to hh:mm:ss format
    function secondsToTime(totalSeconds) {
        // Ensure we never show negative time
        const safeSeconds = Math.max(0, totalSeconds);
        
        const hours = Math.floor(safeSeconds / 3600);
        const minutes = Math.floor((safeSeconds % 3600) / 60);
        const seconds = safeSeconds % 60;
        
        return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }

    // Update work timer - this runs continuously
    function updateWorkTimer() {
        if (!isOnBreak && currentStatus === 'checked_in') {
            totalWorkSeconds++;
            // Ensure work seconds never go negative
            totalWorkSeconds = Math.max(0, totalWorkSeconds);
            
            // Save state periodically (every 10 seconds)
            if (totalWorkSeconds % 10 === 0) {
                saveTimerState();
            }
            // Only update UI if we're on attendance section
            if (isAttendanceSectionVisible()) {
                document.getElementById('totalWorkTime').textContent = secondsToTime(totalWorkSeconds);
            }
        }
    }

    // Update break timer - this runs continuously
    function updateBreakTimer() {
        if (isOnBreak && currentStatus === 'on_break') {
            totalBreakSeconds++;
            // Ensure break seconds never go negative
            totalBreakSeconds = Math.max(0, totalBreakSeconds);
            
            // Save state periodically (every 10 seconds)
            if (totalBreakSeconds % 10 === 0) {
                saveTimerState();
            }
            // Only update UI if we're on attendance section
            if (isAttendanceSectionVisible()) {
                document.getElementById('breakTime').textContent = secondsToTime(totalBreakSeconds);
            }
        }
    }

    // Check if attendance section is currently visible
    function isAttendanceSectionVisible() {
        const attendanceSection = document.getElementById('attendanceSection');
        return attendanceSection && !attendanceSection.classList.contains('d-none');
    }

    // Update UI only when attendance section is visible
    function updateAttendanceUI() {
        if (!isAttendanceSectionVisible()) return;
        
        const statusDisplay = document.getElementById('statusDisplay');
        const checkinBtn = document.getElementById('checkinBtn');
        const checkoutBtn = document.getElementById('checkoutBtn');
        const breakinBtn = document.getElementById('breakinBtn');
        const breakoutBtn = document.getElementById('breakoutBtn');
        const totalWorkTime = document.getElementById('totalWorkTime');
        const breakTime = document.getElementById('breakTime');

        // Update timer displays
        if (totalWorkTime) totalWorkTime.textContent = secondsToTime(totalWorkSeconds);
        if (breakTime) breakTime.textContent = secondsToTime(totalBreakSeconds);

        switch(currentStatus) {
            case 'checked_out':
                if (statusDisplay) statusDisplay.textContent = 'Status: Checked Out';
                if (statusDisplay) statusDisplay.className = 'status status-checked-out';
                if (checkinBtn) checkinBtn.disabled = false;
                if (checkoutBtn) checkoutBtn.disabled = true;
                if (breakinBtn) breakinBtn.disabled = true;
                if (breakoutBtn) breakoutBtn.disabled = true;
                break;
                
            case 'checked_in':
                if (statusDisplay) statusDisplay.textContent = 'Status: Checked In - Ready for Break';
                if (statusDisplay) statusDisplay.className = 'status status-checked-in';
                if (checkinBtn) checkinBtn.disabled = true;
                if (checkoutBtn) checkoutBtn.disabled = false;
                if (breakinBtn) breakinBtn.disabled = false;
                if (breakoutBtn) breakoutBtn.disabled = true;
                break;
                
            case 'on_break':
                if (statusDisplay) statusDisplay.textContent = 'Status: On Break - Click Break Out to Resume';
                if (statusDisplay) statusDisplay.className = 'status status-on-break';
                if (checkinBtn) checkinBtn.disabled = true;
                if (checkoutBtn) checkoutBtn.disabled = true;
                if (breakinBtn) breakinBtn.disabled = true;
                if (breakoutBtn) breakoutBtn.disabled = false;
                break;
        }
        
        // Save state after UI update
        saveTimerState();
    }

    // Start work timer
    function startWorkTimer() {
        if (workTimer) clearInterval(workTimer);
        workTimer = setInterval(updateWorkTimer, 1000);
        console.log('Work timer started');
        saveTimerState();
    }

    // Start break timer
    function startBreakTimer() {
        if (breakTimer) clearInterval(breakTimer);
        breakTimer = setInterval(updateBreakTimer, 1000);
        console.log('Break timer started');
        saveTimerState();
    }

    // Stop all timers
    function stopAllTimers() {
        if (workTimer) {
            clearInterval(workTimer);
            workTimer = null;
        }
        if (breakTimer) {
            clearInterval(breakTimer);
            breakTimer = null;
        }
        console.log('All timers stopped');
        saveTimerState();
    }

    // Check In function with SweetAlert
    async function checkIn() {
        try {
            console.log('Attempting check in...');
            
            // Show loading SweetAlert
            Swal.fire({
                title: 'Checking In...',
                text: 'Please wait while we process your check-in',
                icon: 'info',
                showConfirmButton: false,
                allowOutsideClick: false,
                timer: 1500
            });

            const response = await axios.post('/attendance/checkin');
            console.log('CheckIn response:', response.data);
            
            if (response.data.success) {
                currentStatus = 'checked_in';
                totalWorkSeconds = 0;
                totalBreakSeconds = 0;
                totalBreaksTaken = 0;
                isOnBreak = false;
                lastCheckInTime = new Date().toISOString();
                
                // Hide any existing summary when checking in again
                const summaryElement = document.getElementById('summary');
                if (summaryElement) {
                    summaryElement.style.display = 'none';
                }
                // Clear previous summary data
                clearSummaryData();
                
                updateAttendanceUI();
                startWorkTimer();
                saveTimerState(); // Save immediately after checkin
                
                // SweetAlert success
                Swal.fire({
                    title: 'Checked In Successfully!',
                    text: response.data.message,
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    title: 'Check-in Failed',
                    text: response.data.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        } catch (error) {
            console.error('Check-in failed:', error);
            let errorMessage = 'Check-in failed: Network error';
            if (error.response && error.response.data) {
                errorMessage = error.response.data.message || 'Check-in failed';
            }
            
            Swal.fire({
                title: 'Check-in Failed',
                text: errorMessage,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    }
 
    // Check Out function with SweetAlert - FIXED VERSION
    async function checkOut() {  
        try {
            console.log('Attempting check out...');
            
            // Show loading SweetAlert
            Swal.fire({
                title: 'Checking Out...',
                text: 'Please wait while we process your check-out',
                icon: 'info',
                showConfirmButton: false,
                allowOutsideClick: false,
                timer: 2000
            });

            const response = await axios.post('/attendance/checkout');
            console.log('CheckOut response:', response.data);
            
            if (response.data.success) {
                currentStatus = 'checked_out';
                isOnBreak = false;
                stopAllTimers();
                updateAttendanceUI();
                
                // Show summary with the data from server response
                if (response.data.attendance) {
                    showSummary(response.data.attendance);
                }
                
                clearTimerState(); // Clear timer state but keep summary
                
                // SweetAlert success with summary
                Swal.fire({
                    title: 'Checked Out Successfully!',
                    html: `
                        <div class="text-start">
                            <p>${response.data.message}</p>
                            <div class="mt-3 p-2 bg-light rounded">
                                <small><strong>Today's Summary:</strong></small><br>
                                <small>Work Time: ${response.data.attendance?.total_work_hours ? decimalHoursToTime(response.data.attendance.total_work_hours) : '00:00:00'}</small><br>
                                <small>Break Time: ${response.data.attendance?.total_break_hours ? decimalHoursToTime(response.data.attendance.total_break_hours) : '00:00:00'}</small>
                            </div>
                        </div>
                    `,
                    icon: 'success',
                    confirmButtonText: 'Great!'
                });
            } else {
                Swal.fire({
                    title: 'Check-out Failed',
                    text: response.data.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        } catch (error) {
            console.error('Check-out failed:', error);
            let errorMessage = 'Check-out failed: Network error';
            if (error.response && error.response.data) {
                errorMessage = error.response.data.message || 'Check-out failed';
            }
            
            Swal.fire({
                title: 'Check-out Failed',
                text: errorMessage,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    }

    // Break In function with SweetAlert
    async function breakIn() {
        try {
            console.log('Attempting break in...');
            
            // Show loading SweetAlert
            Swal.fire({
                title: 'Starting Break...',
                text: 'Please wait while we start your break',
                icon: 'info',
                showConfirmButton: false,
                allowOutsideClick: false,
                timer: 1500
            });

            const response = await axios.post('/attendance/breakin');
            console.log('BreakIn response:', response.data);
            
            if (response.data.success) {
                currentStatus = 'on_break';
                isOnBreak = true;
                currentBreakStartTime = Date.now();
                
                // Stop work timer, start break timer
                if (workTimer) clearInterval(workTimer);
                startBreakTimer();
                
                updateAttendanceUI();
                
                // SweetAlert success
                Swal.fire({
                    title: 'Break Started!',
                    text: response.data.message,
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    title: 'Break Start Failed',
                    text: response.data.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        } catch (error) {
            console.error('Break in failed:', error);
            let errorMessage = 'Break in failed: Network error';
            if (error.response && error.response.data) {
                errorMessage = error.response.data.message || 'Break in failed';
            }
            
            Swal.fire({
                title: 'Break Start Failed',
                text: errorMessage,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    }

    // Break Out function with SweetAlert
    async function breakOut() {
        try {
            console.log('Attempting break out...');
            
            // Show loading SweetAlert
            Swal.fire({
                title: 'Ending Break...',
                text: 'Please wait while we end your break',
                icon: 'info',
                showConfirmButton: false,
                allowOutsideClick: false,
                timer: 1500
            });

            const response = await axios.post('/attendance/breakout');
            console.log('BreakOut response:', response.data);
            
            if (response.data.success) {
                currentStatus = 'checked_in';
                isOnBreak = false;
                totalBreaksTaken++;
                
                // Stop break timer, resume work timer
                if (breakTimer) clearInterval(breakTimer);
                startWorkTimer();
                
                updateAttendanceUI();
                
                // SweetAlert success
                Swal.fire({
                    title: 'Break Ended!',
                    text: response.data.message,
                    icon: 'success',
                    timer: 3000,
                    showConfirmButton: false
                });
            } else {
                Swal.fire({
                    title: 'Break End Failed',
                    text: response.data.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        } catch (error) {
            console.error('Break out failed:', error);
            let errorMessage = 'Break out failed: Network error';
            if (error.response && error.response.data) {
                errorMessage = error.response.data.message || 'Break out failed';
            }
            
            Swal.fire({
                title: 'Break End Failed',
                text: errorMessage,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    }

    // Show summary after checkout - FIXED VERSION
    function showSummary(attendance) {
        if (!isAttendanceSectionVisible()) return;
        
        const summaryElement = document.getElementById('summary');
        if (summaryElement) {
            summaryElement.style.display = 'block';
            document.getElementById('summaryCheckin').textContent = attendance.check_in || '-';
            document.getElementById('summaryCheckout').textContent = attendance.check_out || '-';
            
            console.log('Summary data received:', {
                work_hours: attendance.total_work_hours,
                break_hours: attendance.total_break_hours,
                breaks_count: attendance.breaks ? attendance.breaks.length : 0
            });
            
            // Convert decimal hours to hh:mm:ss format - FIXED CALCULATION
            const workTimeFormatted = decimalHoursToTime(attendance.total_work_hours);
            const breakTimeFormatted = decimalHoursToTime(attendance.total_break_hours);
            
            document.getElementById('summaryWorkHours').textContent = workTimeFormatted;
            document.getElementById('summaryBreakHours').textContent = breakTimeFormatted;
            document.getElementById('summaryBreaks').textContent = attendance.breaks ? attendance.breaks.length : totalBreaksTaken;
            
            // Save summary data to localStorage
            saveSummaryData(attendance);
            
            console.log('Summary shown and saved to localStorage');
        }
    }

    // Restore timers based on saved state
    function restoreTimers() {
        console.log('Restoring timers from saved state...');
        
        const elapsedSeconds = calculateElapsedTime();
        console.log('Time elapsed since last sync:', elapsedSeconds, 'seconds');
        
        // Validate the saved state to prevent negative times
        if (totalWorkSeconds < 0) totalWorkSeconds = 0;
        if (totalBreakSeconds < 0) totalBreakSeconds = 0;
        
        // Update counters based on elapsed time and current state
        if (currentStatus === 'checked_in') {
            if (isOnBreak) {
                // We were on break, add time to break seconds
                totalBreakSeconds += elapsedSeconds;
                console.log('Added elapsed time to break seconds:', elapsedSeconds);
                startBreakTimer();
            } else {
                // We were working, add time to work seconds
                totalWorkSeconds += elapsedSeconds;
                console.log('Added elapsed time to work seconds:', elapsedSeconds);
                startWorkTimer();
            }
        } else if (currentStatus === 'on_break') {
            // We were on break, add time to break seconds
            totalBreakSeconds += elapsedSeconds;
            console.log('Added elapsed time to break seconds:', elapsedSeconds);
            startBreakTimer();
        }
        
        // Final validation to ensure no negative times
        totalWorkSeconds = Math.max(0, totalWorkSeconds);
        totalBreakSeconds = Math.max(0, totalBreakSeconds);
        
        // Update UI immediately
        updateAttendanceUI();
        
        console.log('Timers restored successfully. Current state:', {
            status: currentStatus,
            isOnBreak: isOnBreak,
            workSeconds: totalWorkSeconds,
            breakSeconds: totalBreakSeconds,
            workTimeFormatted: secondsToTime(totalWorkSeconds),
            breakTimeFormatted: secondsToTime(totalBreakSeconds)
        });
    }

    // Load current attendance data from server and sync with localStorage - FIXED VERSION
    async function loadAttendanceData() {
        try {
            console.log('Loading attendance data from server...');
            
            // FIRST: Load from localStorage to get current state
            const hasLocalState = loadTimerState();
            console.log('Local state loaded:', {
                hasLocalState: hasLocalState,
                currentStatus: currentStatus,
                workSeconds: totalWorkSeconds,
                breakSeconds: totalBreakSeconds
            });

            // SECOND: Try to load saved summary data
            const hasSavedSummary = loadAndShowSummary();
            if (hasSavedSummary) {
                console.log('Saved summary loaded from localStorage');
                // If we have a saved summary, we should be in checked_out state
                if (currentStatus !== 'checked_out') {
                    console.log('Summary exists but status is not checked_out. Resetting status.');
                    currentStatus = 'checked_out';
                    updateAttendanceUI();
                }
                return; // Don't proceed further if we have a completed summary
            }

            // THIRD: Check server status for active sessions
            const response = await axios.get('/attendance/status');
            console.log('Server attendance response:', response.data);
            
            const attendance = response.data.attendance;
            
            if (attendance) {
                console.log('Server attendance found:', attendance.status);
                
                // If server shows checked_out but we have no local summary, show server summary
                if (attendance.status === 'checked_out' && attendance.check_out) {
                    console.log('Server shows completed attendance for today');
                    showSummary(attendance);
                    return;
                }
                
                // Handle active sessions
                if (attendance.status === 'checked_in' || attendance.status === 'on_break') {
                    // If we have local state, trust it for timing but sync status
                    if (hasLocalState) {
                        console.log('Syncing local state with server status');
                        currentStatus = attendance.status;
                        totalBreaksTaken = attendance.breaks ? attendance.breaks.length : 0;
                        
                        // Restore timers from local state
                        restoreTimers();
                    } else {
                        // No local state, initialize from server
                        console.log('Initializing from server data');
                        currentStatus = attendance.status;
                        
                        if (attendance.check_in) {
                            const checkInTime = new Date(attendance.check_in);
                            const now = new Date();
                            const workSeconds = Math.floor((now - checkInTime) / 1000);
                            
                            totalWorkSeconds = Math.max(0, workSeconds);
                            totalBreaksTaken = attendance.breaks ? attendance.breaks.length : 0;
                            
                            // Calculate break seconds from server breaks
                            if (attendance.breaks && attendance.breaks.length > 0) {
                                totalBreakSeconds = attendance.breaks.reduce((total, breakItem) => {
                                    return total + (breakItem.duration || 0);
                                }, 0);
                            }
                            
                            // If currently on break, add current break duration
                            if (attendance.status === 'on_break' && response.data.break_start_time) {
                                isOnBreak = true;
                                const breakStart = new Date(response.data.break_start_time * 1000);
                                const currentBreakSeconds = Math.floor((now - breakStart) / 1000);
                                totalBreakSeconds += currentBreakSeconds;
                            }
                            
                            // Save this calculated state to localStorage
                            saveTimerState();
                        }
                        
                        // Start appropriate timers
                        if (currentStatus === 'checked_in') {
                            startWorkTimer();
                        } else if (currentStatus === 'on_break') {
                            startBreakTimer();
                        }
                        
                        updateAttendanceUI();
                    }
                }
            } else {
                // No server attendance record
                console.log('No server attendance found');
                
                if (hasLocalState && (currentStatus === 'checked_in' || currentStatus === 'on_break')) {
                    // We have active local session but no server record - trust local storage
                    console.log('Active local session found (no server data), restoring timers...');
                    restoreTimers();
                } else if (!hasLocalState) {
                    // No data anywhere - reset to safe state
                    console.log('No data found anywhere, resetting to checked_out');
                    currentStatus = 'checked_out';
                    totalWorkSeconds = 0;
                    totalBreakSeconds = 0;
                    totalBreaksTaken = 0;
                    isOnBreak = false;
                    updateAttendanceUI();
                }
            }
            
        } catch (error) {
            console.error('Failed to load attendance data from server:', error);
            // Try to restore from localStorage as fallback
            const hasLocalData = loadTimerState();
            const hasSavedSummary = loadAndShowSummary();
            
            if (hasLocalData && (currentStatus === 'checked_in' || currentStatus === 'on_break')) {
                console.log('Server failed, restoring from localStorage...');
                restoreTimers();
            } else if (!hasSavedSummary) {
                // Reset to safe state
                currentStatus = 'checked_out';
                totalWorkSeconds = 0;
                totalBreakSeconds = 0;
                totalBreaksTaken = 0;
                isOnBreak = false;
                updateAttendanceUI();
            }
        }
    }

    // Check if we need to clear data for a new day
    function checkForNewDay() {
        try {
            const lastCheckin = localStorage.getItem(STORAGE_KEYS.CHECKIN_TIME);
            if (lastCheckin) {
                const lastDate = new Date(lastCheckin).toDateString();
                const today = new Date().toDateString();
                
                if (lastDate !== today) {
                    console.log('New day detected, clearing previous data');
                    clearAllAttendanceData();
                }
            }
        } catch (error) {
            console.error('Error checking for new day:', error);
        }
    }

    // Initialize attendance system
    function initializeAttendanceSystem() {
        if (isInitialized) {
            console.log('Attendance system already initialized, just updating UI');
            updateAttendanceUI();
            return;
        }
        
        console.log('Initializing attendance system...');
        isInitialized = true;
        
        // Check if we need to clear data for new day
        checkForNewDay();

        // Load data from server (which will sync with localStorage)
        loadAttendanceData();
        
        // Set up periodic sync with server (every 30 seconds) to prevent drift
        setInterval(async () => {
            if (currentStatus === 'checked_in' || currentStatus === 'on_break') {
                try {
                    saveTimerState(); // Save to localStorage
                } catch (error) {
                    console.error('Periodic sync failed:', error);
                }
            }
        }, 30000); // Sync every 30 seconds
        
        // Save state before page unload
        window.addEventListener('beforeunload', function() {
            saveTimerState();
        });
    }

    // Initialize when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded - Setting up attendance system with persistence');
        
        // Initialize attendance system immediately
        initializeAttendanceSystem();
    });

    // Export functions to global scope for HTML onclick attributes
    window.checkIn = checkIn;
    window.checkOut = checkOut;
    window.breakIn = breakIn;
    window.breakOut = breakOut;
</script>

{{-- side bar script --}}
<script>
    $(document).ready(function() {
        // Load the saved active section on page load
        const savedSection = loadActiveSection();
        console.log('Restoring active section:', savedSection);
        switchToSection(savedSection);

        $('.sidebar-theme .nav-link').on('click', function(e) {
            e.preventDefault();
            let section = $(this).data('section');
            switchToSection(section);
        });
    });
</script>


{{-- Settings script --}}
<script>
    $(document).ready(function(){
        // Password validation variables
        let newPassword = $('#new_password');
        let confirmPassword = $('#confirm_password');
        let passwordMatchError = $('#passwordMatchError');

        // Real-time password matching validation
        function validatePasswords() {
            const newPass = newPassword.val();
            const confirmPass = confirmPassword.val();
            
            // Only validate if both fields have values
            if (newPass && confirmPass) {
                if (newPass !== confirmPass) {
                    passwordMatchError.removeClass('d-none');
                    return false;
                } else {
                    passwordMatchError.addClass('d-none');
                    return true;
                }
            }
            // If one field is empty, don't show error
            passwordMatchError.addClass('d-none');
            return true;
        }

        // Real-time validation
        newPassword.on('input', validatePasswords);
        confirmPassword.on('input', validatePasswords);

        // Show edit form
        $('#editBtn').click(function(){
            $('#settingsForm').removeClass('d-none');
            $('#employeeDetails').addClass('d-none');
            $(this).hide();
            // Reset password fields and errors
            newPassword.val('');
            confirmPassword.val('');
            passwordMatchError.addClass('d-none');
        });

        // Cancel edit
        $('#cancelEdit').click(function(){
            $('#settingsForm').addClass('d-none');
            $('#employeeDetails').removeClass('d-none');
            $('#editBtn').show();
            // Reset password fields and errors
            newPassword.val('');
            confirmPassword.val('');
            passwordMatchError.addClass('d-none');
        });

        // AJAX submit with SweetAlert
        $('#settingsForm').submit(function(e){
            e.preventDefault();

            // Validate passwords before submission
            if (!validatePasswords()) {
                Swal.fire({
                    icon: 'error',
                    title: 'Password Mismatch',
                    text: 'Please make sure passwords match.'
                });
                return;
            }

            let formData = new FormData(this);

            // Show loading state
            const submitButton = $(this).find('button[type="submit"]');
            const originalText = submitButton.text();
            submitButton.prop('disabled', true).text('Saving...');

            $.ajax({
                url: "{{ route('employee.updateSettings', $employee->id) }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function(res){
                    // Show success message with SweetAlert
                    Swal.fire({
                        icon: 'success',
                        title: 'Settings Updated',
                        text: 'Your settings have been updated successfully!',
                        timer: 3000,
                        showConfirmButton: false
                    });

                    // Update phone and photo in details
                    $('#phoneDisplay').text($('#phoneInput').val());
                    if(res.photo){
                        $('#photoDisplay').attr('src', res.photo);
                        // Update profile picture in sidebar
                        $('#sidebarPhoto').attr('src', res.photo + '?t=' + new Date().getTime());
                    }

                    // Reset form and show details
                    $('#settingsForm').addClass('d-none');
                    $('#employeeDetails').removeClass('d-none');
                    $('#editBtn').show();
                    
                    // Clear password fields
                    newPassword.val('');
                    confirmPassword.val('');
                    passwordMatchError.addClass('d-none');
                },
                error: function(xhr, status, error){
                    console.log('XHR Response:', xhr.responseJSON);
                    console.log('Status:', status);
                    console.log('Error:', error);
                    
                    // Show error message with SweetAlert
                    let errorMessage = 'Something went wrong! Please try again.';
                    
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        // Laravel validation errors
                        let errors = xhr.responseJSON.errors;
                        errorMessage = '';
                        for (let field in errors) {
                            errorMessage += errors[field][0] + '\n';
                        }
                    } else if (xhr.responseJSON && xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                    } else if (xhr.status === 422) {
                        errorMessage = 'Validation failed. Please check your inputs.';
                    } else if (xhr.status === 500) {
                        errorMessage = 'Server error. Please try again later.';
                    }
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Update Failed',
                        text: errorMessage
                    });
                },
                complete: function() {
                    // Re-enable button
                    submitButton.prop('disabled', false).text(originalText);
                }
            });
        });
    });
</script>

{{-- style for settings --}}
<style>
    /* Edit Button with Sidebar Color Tone */
    .btn-gradient {
        background: linear-gradient(135deg, #1e3c72, #2a5298);
        border: none;
        color: white;
        font-weight: 500;
        padding: 10px 20px;
        border-radius: 8px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn-gradient:hover {
        background: linear-gradient(135deg, #2a5298, #1e3c72);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(30, 60, 114, 0.4);
    }

    .btn-gradient:active {
        transform: translateY(0);
        box-shadow: 0 2px 6px rgba(30, 60, 114, 0.3);
    }

    .btn-gradient:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(30, 60, 114, 0.3);
    }

    /* Secondary button styling (for Cancel button) */
    .btn-secondary {
        background: linear-gradient(135deg, #6c757d, #495057);
        border: none;
        color: white;
        font-weight: 500;
        padding: 10px 20px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .btn-secondary:hover {
        background: linear-gradient(135deg, #495057, #6c757d);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(108, 117, 125, 0.4);
    }

    /* Save Changes Button with Gradient */
    .btn-save {
        background: linear-gradient(135deg,#2a5298, #1e3c72);
        border: none;
        color: white;
        font-weight: 500;
        padding: 10px 25px;
        border-radius: 8px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .btn-save:hover {
        background: linear-gradient(135deg, #2a5298, #1e3c72);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(40, 167, 69, 0.4);
    }

    .btn-save:active {
        transform: translateY(0);
        box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
    }

    .btn-save:disabled {
        background: linear-gradient(135deg, #6c757d, #495057);
        transform: none;
        box-shadow: none;
        cursor: not-allowed;
    }

    .btn-save:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(40, 167, 69, 0.3);
    }

    /* With icon */
    .btn-save i {
        margin-right: 8px;
        transition: transform 0.3s ease;
    }

    .btn-save:hover i {
        transform: scale(1.1);
    }
</style>

{{-- Ajax for leaves section --}}
<script>
    $(document).ready(function() {
        let currentMonthRemainingDays = 2; // Default value

        // Real-time validation when dates change
        $('#start_date, #end_date').on('change', function() {
            validateLeaveDates();
        });

        // Function to validate leave dates
        function validateLeaveDates() {
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();

            if (startDate && endDate) {
                // Show loading state
                const submitButton = $('#applyLeaveForm button[type="submit"]');
                submitButton.prop('disabled', true).text('Checking availability...');

                $.ajax({
                    url: "{{ route('employee.checkAvailableLeaves') }}",
                    method: "POST",
                    data: {
                        start_date: startDate,
                        end_date: endDate,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if (response.success) {
                            currentMonthRemainingDays = response.remaining_days;
                            
                            if (response.can_apply) {
                                // Enable submit button
                                submitButton.prop('disabled', false).text('Apply Leave');
                                showDateValidationMessage(response.message, 'success');
                            } else {
                                // Keep button disabled
                                submitButton.prop('disabled', true).text('Apply Leave');
                                
                                if (response.is_duplicate) {
                                    showDateValidationMessage(response.message, 'error');
                                } else {
                                    showDateValidationMessage(response.message, 'warning');
                                }
                            }
                        }
                    },
                    error: function(xhr) {
                        console.error('Error checking leave availability:', xhr);
                        submitButton.prop('disabled', false).text('Apply Leave');
                        showDateValidationMessage('Error checking leave availability. Please try again.', 'error');
                    }
                });
            } else {
                // If dates are not complete, enable button but show info
                const submitButton = $('#applyLeaveForm button[type="submit"]');
                submitButton.prop('disabled', false).text('Apply Leave');
                $('#dateValidationMessage').remove();
            }
        }

        // Function to show validation messages
        function showDateValidationMessage(message, type) {
            // Remove existing messages
            $('#dateValidationMessage').remove();
            
            let messageClass = 'alert-info';
            if (type === 'success') messageClass = 'alert-success';
            if (type === 'error') messageClass = 'alert-danger';
            if (type === 'warning') messageClass = 'alert-warning';
            
            const messageHtml = `<div id="dateValidationMessage" class="alert ${messageClass} mt-2">${message}</div>`;
            
            $('#end_date').closest('.mb-2').after(messageHtml);
            
            // Auto-remove success messages after 5 seconds
            if (type === 'success') {
                setTimeout(() => {
                    $('#dateValidationMessage').fadeOut(300, function() {
                        $(this).remove();
                    });
                }, 5000);
            }
        }

        // Main form submission
        $('#applyLeaveForm').on('submit', function(e) {
            e.preventDefault();

            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();

            if (!startDate || !endDate) {
                Swal.fire({
                    title: 'Error!',
                    text: 'Please select both start and end dates.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
                return;
            }

            // Show loading SweetAlert
            Swal.fire({
                title: 'Submitting Leave Request...',
                text: 'Please wait while we process your request',
                icon: 'info',
                showConfirmButton: false,
                allowOutsideClick: false
            });

            $.ajax({
                url: "{{ route('employee.applyLeave') }}",
                method: "POST",
                data: $(this).serialize(),
                headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                success: function(response) {
                    if (response.success) {
                        // SweetAlert success message
                        Swal.fire({
                            title: 'Success!',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK',
                            timer: 3000,
                            showConfirmButton: true
                        });

                        // Add new leave instantly in table with proper styling
                        let statusClass = 'bg-warning';
                        let statusText = 'Pending';
                        
                        let newRow = `
                            <tr>
                                <td>${response.leave.leave_type}</td>
                                <td>${response.leave.start_date}</td>
                                <td>${response.leave.end_date}</td>
                                <td>
                                    <span class="badge ${statusClass}">${statusText}</span>
                                </td>
                                <td>${response.leave.remarks ?? ''}</td>
                                <td>—</td>
                            </tr>
                        `;
                        $('#leavesTable tbody').prepend(newRow);

                        // Reset the form and clear validation messages
                        $('#applyLeaveForm')[0].reset();
                        $('#dateValidationMessage').remove();
                        
                        // Update leave summary
                        updateLeaveSummary();
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Error submitting leave! Please try again.';
                    
                    // Check if there are validation errors
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        errorMessage = Object.values(errors)[0][0];
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    
                    // SweetAlert error message
                    Swal.fire({
                        title: 'Error!',
                        text: errorMessage,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });

        // Add a leave summary display
        function updateLeaveSummary() {
            $.ajax({
                url: "{{ route('employee.getLeaveSummary') }}",
                method: "GET",
                success: function(response) {
                    if (response.success) {
                        // Create or update leave summary
                        if ($('#leaveSummary').length === 0) {
                            $('#applyLeaveForm').before(`
                                <div id="leaveSummary" class="alert alert-info mb-3">
                                    <strong>Leave Summary This Month:</strong><br>
                                    Total Leaves Taken: ${response.approved_days} day(s)<br>
                                    Remaining Leaves: ${response.remaining_days} day(s)<br>
                                    Total Limit: 2 days per month
                                </div>
                            `);
                        } else {
                            $('#leaveSummary').html(`
                                <strong>Leave Summary This Month:</strong><br>
                                Total Leaves Taken: ${response.approved_days} day(s)<br>
                                Remaining Leaves: ${response.remaining_days} day(s)<br>
                                Total Limit: 2 days per month
                            `);
                        }
                    }
                }
            });
        }

        // Initialize leave summary when page loads
        updateLeaveSummary();
    });
</script>

{{-- apply leave button --}}
<script>
    $(document).ready(function() {
        // Hover glow effect
        $('button[type="submit"]').hover(
            function() {
                $(this).css('box-shadow', '0 0 12px rgba(30, 60, 114, 0.6)');
            },
            function() {
                $(this).css('box-shadow', 'none');
            }
        );
    });
</script>



<style>
    /* Sidebar Theme (matches navbar gradient tones) */
    .sidebar-theme {
    background: linear-gradient(180deg, #1e3c72, #2a5298);
    backdrop-filter: blur(8px);
    color: #f1f1f1;
    }

    /* Sidebar links */
    .sidebar-theme .nav-link {
    color: #e0e0e0;
    font-weight: 500;
    border-radius: 8px;
    transition: all 0.3s ease;
    }

    .sidebar-theme .nav-link:hover,
    .sidebar-theme .nav-link.active {
    background: rgba(255, 255, 255, 0.15);
    color: #61a0ff !important;
    transform: translateX(5px);
    }

    .sidebar-theme .nav-item i {
    color: #cfd8ff;
    }

    .sidebar-theme hr {
    border-color: rgba(255, 255, 255, 0.3);
    }

    .list-group-item strong {
        color: #1e3c72;
    }

</style>


<style>
    .text-danger.small {
        font-size: 0.875em;
        margin-top: 0.25rem;
    }
    
    .form-control.is-invalid {
        border-color: #dc3545;
        padding-right: calc(1.5em + 0.75rem);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 3.6.4.4.4-.4'/%3e%3cpath d='M6 7v1'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }
</style>

{{-- style for attendance section --}}
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #1e3c72, #2a5298);
        --success-gradient: linear-gradient(135deg, #28a745, #20c997);
        --danger-gradient: linear-gradient(135deg, #dc3545, #e83e8c);
        --warning-gradient: linear-gradient(135deg, #ffc107, #fd7e14);
        --info-gradient: linear-gradient(135deg, #17a2b8, #6f42c1);
    }
    
    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    .attendance-container {
        max-width: 700px;
        margin: 1.5rem auto;
        padding: 0 15px;
    }
    
    .attendance-card {
        background: white;
        border-radius: 15px;
        box-shadow: 0 10px 25px rgba(30, 60, 114, 0.15);
        padding: 2rem;
        text-align: center;
        transition: all 0.3s ease;
        border: none;
    }
    
    .attendance-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 30px rgba(30, 60, 114, 0.2);
    }
    
    .attendance-header {
        margin-bottom: 1.5rem;
    }
    
    .attendance-header h1 {
        color: #1e3c72;
        font-weight: 700;
        font-size: 2rem;
        margin-bottom: 0.5rem;
    }
    
    .attendance-header .bi {
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-right: 8px;
    }
    
    .attendance-subtitle {
        color: #6c757d;
        font-size: 1rem;
    }
    
    .status-container {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1.5rem;
        border-left: 4px solid #1e3c72;
    }
    
    .status-label {
        font-size: 0.8rem;
        color: #6c757d;
        text-transform: uppercase;
        letter-spacing: 1px;
        margin-bottom: 0.4rem;
    }
    
    .status-value {
        font-size: 1.3rem;
        font-weight: 600;
        color: #1e3c72;
    }
    
    .timer-container {
        display: flex;
        justify-content: space-around;
        margin-bottom: 2rem;
        flex-wrap: wrap;
    }
    
    .timer-card {
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        flex: 1;
        margin: 0 8px;
        min-width: 150px;
        border-top: 3px solid transparent;
        transition: all 0.3s ease;
    }
    
    .timer-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1);
    }
    
    .timer-card.work {
        border-top-color: #1e3c72;
    }
    
    .timer-card.break {
        border-top-color: #17a2b8;
    }
    
    .timer-label {
        font-size: 0.8rem;
        color: #6c757d;
        margin-bottom: 0.4rem;
    }
    
    .timer-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e3c72;
        font-family: 'Courier New', monospace;
    }
    
    .break-timer-value {
        color: #17a2b8;
    }
    
    .buttons-container {
        display: flex;
        justify-content: center;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 1.25rem;
    }
    
    .attendance-btn {
        padding: 10px 20px;
        border: none;
        border-radius: 40px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.3s ease;
        min-width: 130px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 3px 8px rgba(0, 0, 0, 0.1);
    }
    
    .attendance-btn i {
        margin-right: 6px;
        font-size: 1rem;
    }
    
    .btn-checkin {
        background: var(--success-gradient);
        color: white;
    }
    
    .btn-checkin:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 12px rgba(40, 167, 69, 0.4);
    }
    
    .btn-checkout {
        background: var(--danger-gradient);
        color: white;
    }
    
    .btn-checkout:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 12px rgba(220, 53, 69, 0.4);
    }
    
    .btn-breakin {
        background: var(--warning-gradient);
        color: white;
    }
    
    .btn-breakin:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 12px rgba(255, 193, 7, 0.4);
    }
    
    .btn-breakout {
        background: var(--info-gradient);
        color: white;
    }
    
    .btn-breakout:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 12px rgba(23, 162, 184, 0.4);
    }
    
    .attendance-btn:disabled {
        opacity: 0.6;
        transform: none;
        box-shadow: none;
        cursor: not-allowed;
    }
    
    .attendance-btn:disabled:hover {
        transform: none;
        box-shadow: none;
    }
    
    .current-time {
        margin-top: 1.25rem;
        color: #6c757d;
        font-size: 0.9rem;
    }
    
    .pulse {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% {
            box-shadow: 0 0 0 0 rgba(30, 60, 114, 0.4);
        }
        70% {
            box-shadow: 0 0 0 8px rgba(30, 60, 114, 0);
        }
        100% {
            box-shadow: 0 0 0 0 rgba(30, 60, 114, 0);
        }
    }
    
    /* Status-specific styles */
    .status-checked-in .status-value {
        color: #28a745;
    }
    
    .status-on-break .status-value {
        color: #ffc107;
    }
    
    .status-checked-out .status-value {
        color: #dc3545;
    }
    
    /* Responsive adjustments */
    @media (max-width: 768px) {
        .attendance-container {
            max-width: 95%;
            margin: 1rem auto;
        }
        
        .attendance-card {
            padding: 1.25rem;
        }
        
        .attendance-header h1 {
            font-size: 1.7rem;
        }
        
        .timer-container {
            flex-direction: column;
            gap: 12px;
        }
        
        .timer-card {
            margin: 0;
        }
        
        .buttons-container {
            flex-direction: column;
            align-items: center;
        }
        
        .attendance-btn {
            width: 100%;
            max-width: 220px;
        }
    }
</style>