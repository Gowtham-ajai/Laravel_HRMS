@extends('layouts.master')
@section('title', 'HR Dashboard')

@section('content')
<div class="container-fluid">
    <div class="row">

        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 p-0">
            <div class="d-flex flex-column flex-shrink-0 vh-100 p-3 text-light bg-dark shadow-lg sidebar-theme">
                <div class="text-center mb-4">
                    <img id="sidebarPhoto" src="{{ $hrUser->photo ? asset('storage/'.$hrUser->photo) : asset('images/default-avatar.png') }}" 
                        alt="HR Photo" 
                        class="img-fluid rounded-circle border border-light mb-2" 
                        style="width:100px;height:100px;">
                    <h5 class="mb-0">{{ $hrUser->name }}</h5>
                    <small class="text-muted">{{ $hrUser->role }}</small>
                </div>

                <hr class="text-secondary">

                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item mb-2">
                        <a href="#" class="nav-link text-light active bg-primary" data-section="dashboardSection">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="#" class="nav-link text-light" data-section="employeesSection">
                            <i class="bi bi-people me-2"></i> List of Employees
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="#" class="nav-link text-light" data-section="addEmployeeSection">
                            <i class="bi bi-person-plus me-2"></i> Add Employee
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="#" class="nav-link text-light" data-section="attendanceSection">
                            <i class="bi bi-clock-history me-2"></i> Emp Attendance
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="#" class="nav-link text-light" data-section="hrattendanceSection">
                            <i class="bi bi-clock-history me-2"></i> HR Attendance
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="#" class="nav-link text-light" data-section="leavesSection">
                            <i class="bi bi-calendar-check me-2"></i> Leaves
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="#" class="nav-link text-light" data-section="applyLeavesSection">
                            <i class="bi bi-calendar-check me-2"></i> Apply Leave
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="#" class="nav-link text-light" data-section="settingsSection">
                            <i class="bi bi-gear-fill me-2"></i> Settings
                        </a>
                    </li>
                </ul>

                <!-- Logout positioned at the bottom of sidebar -->
                <div class="mt-auto pt-3 border-top border-secondary">
                    <a href="{{ route('logout') }}" class="btn-gradient nav-link text-light d-flex align-items-center justify-content-center text-decoration-none"
                    onclick="event.preventDefault(); document.getElementById('logoutForm').submit();">
                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                    </a>
                    <form id="logoutForm" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 p-4 bg-light" id="mainContent">

            <!-- Dashboard Section -->
            <div id="dashboardSection" class="content-section">
                    <h2 class="mb-4 text-center fw-bold text-dark">HR Dashboard Overview</h2>
                    
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="stat-card stat-card-primary">
                                <i class="bi bi-people"></i>
                                <h3>{{ $totalEmployees }}</h3>
                                <p>Total Employees</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stat-card stat-card-success">
                                <i class="bi bi-person-check"></i>
                                <h3>{{ $activeEmployees }}</h3>
                                <p>Active Employees</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stat-card stat-card-warning">
                                <i class="bi bi-person-x"></i>
                                <h3>{{ $inactiveEmployees }}</h3>
                                <p>Inactive Employees</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stat-card stat-card-info">
                                <i class="bi bi-building"></i>
                                <h3>{{ $departmentsCount }}</h3>
                                <p>Departments</p>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Stats Row -->
                    <div class="row mb-4">
                        <div class="col-md-3 mb-3">
                            <div class="stat-card stat-card-danger">
                                <i class="bi bi-calendar-check"></i>
                                <h3>{{ $pendingLeaves ?? 0 }}</h3>
                                <p>Pending Leaves</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stat-card" style="background: linear-gradient(135deg, #6f42c1, #e83e8c);">
                                <i class="bi bi-clock-history"></i>
                                <h3>{{ $presentToday ?? 42 }}</h3>
                                <p>Present Today</p>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="stat-card" style="background: linear-gradient(135deg, #fd7e14, #e74c3c);">
                                <i class="bi bi-person-dash"></i>
                                <h3>{{ $absentToday ?? 8 }}</h3>
                                <p>Absent Today</p>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="card shadow border-0 mb-4">
                        <div class="card-header text-white" style="background: linear-gradient(90deg, #1e3c72, #2a5298);">
                            <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Quick Actions</h5>
                        </div>
                        <div class="card-body">
                            <div class="row text-center">
                                <div class="col-md-3 mb-3">
                                    <a href="#" class="quick-action-btn bg-primary" data-section="addEmployeeSection">
                                        <i class="bi bi-person-plus"></i>
                                        <span>Add Employee</span>
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="#" class="quick-action-btn bg-success" data-section="employeesSection">
                                        <i class="bi bi-people"></i>
                                        <span>View Employees</span>
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="#" class="quick-action-btn bg-info" data-section="attendanceSection">
                                        <i class="bi bi-clock-history"></i>
                                        <span>Attendance</span>
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="#" class="quick-action-btn bg-warning" data-section="leavesSection">
                                        <i class="bi bi-calendar-check"></i>
                                        <span>Manage Leaves</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
            </div>

            <!-- Employees List -->
            <div id="employeesSection" class="content-section {{ (request()->get('tab') !== 'editEmployeeSection' || !request()->get('employee_id')) ? '' : 'd-none' }}">
                <div class="container mt-5">
                    <h2 class="text-center mb-4"><i class="bi bi-people"></i>List of Employees</h2>

                    <!-- Success Message -->
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <!-- Search Box -->
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Search Employees</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="search" class="form-label">Search</label>
                                    <input type="text" class="form-control" id="search" name="search" 
                                        placeholder="Search by name, email, designation...">
                                </div>
                                <div class="col-md-2">
                                    <label for="department" class="form-label">Department</label>
                                    <select class="form-select" id="department" name="department">
                                        <option value="">All Departments</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}">
                                                {{ $dept->department_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="designation" class="form-label">Designation</label>
                                    <input type="text" class="form-control" id="designation" name="designation" 
                                        placeholder="e.g., Java Developer">
                                </div>
                                <div class="col-md-2">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="">All Status</option>
                                        <option value="Active">Active</option>
                                        <option value="Inactive">Inactive</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label for="employee_id" class="form-label">Employee ID</label>
                                    <input type="text" class="form-control" id="employee_id" name="employee_id" 
                                        placeholder="Search by ID">
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" id="searchBtn" class="btn btn-primary w-80">Search</button>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" id="resetBtn" class="btn btn-secondary w-80">Reset</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Employees Table -->
                    <div class="card shadow border-0">
                        <div class="card-header text-white" style="background: linear-gradient(90deg, #1e3c72, #2a5298);">
                            <h5 class="mb-0"><i class="bi bi-people me-2"></i>List of Employees</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-dark text-center">
                                        <tr>
                                            <th>ID</th>
                                            <th>Full Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Department</th>
                                            <th>Designation</th>
                                            <th>Status</th>
                                            <th>Date of Joining</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center" id="employeesTableBody">
                                        @foreach($employees as $employee)
                                            <tr class="employee-row" 
                                                data-id="{{ $employee->id }}"
                                                data-name="{{ strtolower($employee->first_name . ' ' . $employee->last_name) }}"
                                                data-email="{{ strtolower($employee->email) }}"
                                                data-phone="{{ $employee->phone }}"
                                                data-department="{{ $employee->department_id }}"
                                                data-department-name="{{ strtolower($employee->department->department_name ?? '') }}"
                                                data-designation="{{ strtolower($employee->designation->designation ?? '') }}"
                                                data-status="{{ $employee->status }}">
                                                <td>{{ $employee->id }}</td>
                                                <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
                                                <td>{{ $employee->email }}</td>
                                                <td>{{ $employee->phone }}</td>
                                                <td>{{ $employee->department->department_name ?? 'N/A' }}</td>
                                                <td>{{ $employee->designation->designation ?? 'N/A' }}</td>
                                                <td>
                                                    @if($employee->status === 'Active')
                                                        <span class="badge bg-success">Active</span>
                                                    @else
                                                        <span class="badge bg-danger">Inactive</span>
                                                    @endif
                                                </td>
                                                <td>{{ \Carbon\Carbon::parse($employee->date_of_joining)->format('d-M-Y') }}</td>
                                                <td>
                                                    <a href="{{ route('hr.dashboard', ['tab' => 'editEmployeeSection', 'employee_id' => $employee->id]) }}" class="btn btn-primary btn-sm">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>

                                                    <form action="{{ route('employees.destroy', $employee->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this employee?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="btn btn-danger btn-sm">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                
                                <!-- Results Count -->
                                <div class="mt-3 text-muted text-center" id="resultsCount">
                                    Total: {{ $employees->count() }} employees
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Employee -->
            <div id="addEmployeeSection" class="content-section d-none">
                <div class="container mt-5">
                    <h2 class="text-center mb-4"> <i class="bi bi-person-plus"></i>Add New Employee</h2>

                    <!-- Success / Error Messages -->
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="card p-4 shadow-sm">
                        <form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3">

                                <div class="col-md-6">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" value="{{ old('first_name') }}">
                                    @error('first_name') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name') }}">
                                    @error('last_name') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}">
                                    @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" 
                                        placeholder="Enter 10-digit number starting with 6,7,8,9" maxlength="10">
                                    @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <!-- Password (HR sets it) -->
                                <div class="col-md-6">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password">
                                    @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select class="form-select" id="gender" name="gender" required>
                                        <option value="">-- Select Gender --</option>
                                        <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                        <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                        <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                    @error('gender') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}">
                                    @error('date_of_birth') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="photo" class="form-label">Employee Photo</label>
                                    <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                                    @error('photo') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label>Department</label>
                                    <select name="department_id" id="department_id" class="form-select">
                                        <option value="">-- Select Department --</option>
                                        @foreach($departments as $department)
                                            <option value="{{ $department->id }}">{{ $department->department_name }}</option>
                                        @endforeach
                                    </select>
                                    @error('department_id') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label>Designation</label>
                                    <select name="designation_id" id="designation_id" class="form-select">
                                        <option value="">-- Select Designation --</option>
                                    </select>
                                    @error('designation_id') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="status" class="form-label">Status</label>
                                    <select name="status" id="status" class="form-select">
                                        <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="col-md-6">
                                    <label for="date_of_joining" class="form-label">Date of Joining</label>
                                    <input type="date" class="form-control" id="date_of_joining" name="date_of_joining" value="{{ old('date_of_joining') }}">
                                    @error('date_of_joining') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <div class="col-12 text-center mt-3">
                                    <button type="submit" class="btn btn-success">Add Employee</button>
                                    <a href="{{ route('employees.index') }}" class="btn btn-secondary">Back</a>
                                </div>

                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Edit Employee -->
            <div id="editEmployeeSection" class="content-section {{ (request()->get('tab') === 'editEmployeeSection' && request()->get('employee_id')) ? '' : 'd-none' }}">
                @if($editEmployee)
                    @include('hr_partials.edit_emp', ['employee' => $editEmployee])
                @else
                    <div class="container mt-5">
                        <div class="alert alert-warning text-center">
                            <h4><i class="bi bi-exclamation-triangle"></i> No Employee Selected</h4>
                            <p class="mb-3">Please select an employee to edit from the list.</p>
                            <a href="{{ route('hr.dashboard', ['tab' => 'employeesSection']) }}" class="btn btn-primary">
                                <i class="bi bi-arrow-left"></i> Back to Employees List
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            {{-- HR Attendance --}}
            <div id="hrattendanceSection" class="d-none">
                <div class="attendance-container">
                    <div class="attendance-card">
                        <div class="attendance-header">
                            <h1><i class="bi bi-clock-history"></i>HR Attendance System</h1>
                            <p class="attendance-subtitle">Manage your daily attendance and breaks</p>
                        </div>
                        
                        <div class="status-container" id="hrStatusContainer">
                            <div class="status-label">Current Status</div>
                            <div class="status-value" id="hrStatusDisplay">Status: Checked Out</div>
                        </div>
                        
                        <div class="timer-container">
                            <div class="timer-card work">
                                <div class="timer-label">Total Working Time</div>
                                <div class="timer-value" id="hrTotalWorkTime">00:00:00</div>
                            </div>
                            
                            <div class="timer-card break">
                                <div class="timer-label">Break Duration</div>
                                <div class="timer-value break-timer-value" id="hrBreakTime">00:00:00</div>
                            </div>
                        </div>
                        
                        <div class="buttons-container">
                            <button id="hrCheckinBtn" class="attendance-btn btn-checkin pulse" onclick="hrCheckIn()">
                                <i class="bi bi-box-arrow-in-right"></i> Check In
                            </button>
                            <button id="hrCheckoutBtn" class="attendance-btn btn-checkout" onclick="hrCheckOut()" disabled>
                                <i class="bi bi-box-arrow-right"></i> Check Out
                            </button>
                            <button id="hrBreakinBtn" class="attendance-btn btn-breakin" onclick="hrBreakIn()" disabled>
                                <i class="bi bi-cup-straw"></i> Break In
                            </button>
                            <button id="hrBreakoutBtn" class="attendance-btn btn-breakout" onclick="hrBreakOut()" disabled>
                                <i class="bi bi-cup"></i> Break Out
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Employee Attendance -->
            <div id="attendanceSection" class="d-none">
                @include('attendances.index')
            </div>


            <!-- Employee Leaves Section -->
            <div id="leavesSection" class="section-content d-none">
                <div class="card shadow border-0">
                    <div class="card-header text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(90deg, #1e3c72, #2a5298);">
                        <h5 class="mb-0">
                            <i class="bi bi-calendar-check me-2"></i>
                            Employee Leave Requests
                        </h5>
                        <span class="badge bg-warning">
                            {{ $pendingLeavesCount ?? 0 }} Pending
                        </span>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-primary text-center">
                                    <tr>
                                        <th>Employee Name</th>
                                        <th>Leave Type</th>
                                        <th>Start Date</th>
                                        <th>End Date</th>
                                        <th>Remarks</th>
                                        <th>Status</th>
                                        <th>Applied On</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($leaves as $leave)
                                    <tr id="leaveRow-{{ $leave->id }}">
                                        <td class="text-center">
                                            {{ $leave->employee->first_name }} {{ $leave->employee->last_name }}
                                            <br>
                                            <span class="badge bg-secondary">Employee</span>
                                        </td>
                                        <td class="text-center">{{ $leave->leave_type }}</td>
                                        <td class="text-center">{{ \Carbon\Carbon::parse($leave->start_date)->format('M d, Y') }}</td>
                                        <td class="text-center">{{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}</td>
                                        <td>{{ $leave->remarks ?? '-' }}</td>
                                        <td class="text-center status-cell">
                                            <span class="badge 
                                                @if($leave->status == 'Pending') bg-warning
                                                @elseif($leave->status == 'Approved') bg-success
                                                @else bg-danger @endif">
                                                {{ $leave->status }}
                                            </span>
                                        </td>
                                        <td class="text-center">{{ \Carbon\Carbon::parse($leave->created_at)->format('M d, Y') }}</td>
                                        <td class="text-center">
                                            @if($leave->status == 'Pending')
                                                <button class="btn btn-success btn-sm approveBtn" data-id="{{ $leave->id }}">
                                                    <i class="bi bi-check-circle me-1"></i>Approve
                                                </button>
                                                <button class="btn btn-danger btn-sm rejectBtn" data-id="{{ $leave->id }}">
                                                    <i class="bi bi-x-circle me-1"></i>Reject
                                                </button>
                                            @else
                                                <span class="text-muted">
                                                    @if($leave->status == 'Approved')
                                                        <i class="bi bi-check-circle text-success me-1"></i>Approved
                                                    @else
                                                        <i class="bi bi-x-circle text-danger me-1"></i>Rejected
                                                    @endif
                                                </span>
                                            @endif
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <i class="bi bi-inbox display-4 text-muted d-block mb-2"></i>
                                            <span class="text-muted">No employee leave requests found</span>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Results Count -->
                        @if($leaves && $leaves->count() > 0)
                        <div class="mt-3 text-muted text-center">
                            Total: {{ $leaves->count() }} employee leave request(s)
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Apply leave --}}
            <div id="applyLeavesSection" class="d-none">
                <div class="container mt-4">
                    <div class="row">
                        <div class="col-md-8 mx-auto">
                            <!-- Leave Summary will appear here automatically -->
                            
                            <!-- Apply Leave Form Card -->
                            <div class="card shadow-sm border-0 mb-4">
                                <div class="card-header text-white" 
                                    style="background: linear-gradient(90deg, #1e3c72, #2a5298);">
                                    <h5 class="mb-0 fw-bold"><i class="bi bi-calendar-plus me-2"></i>Apply Leave</h5>
                                </div>

                                <div class="card-body">
                                    <!-- Leave Summary will be inserted here by JavaScript -->
                                    
                                    <form id="applyHrLeaveForm">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="leave_type" class="form-label">Leave Type</label>
                                            <select name="leave_type" id="leave_type" class="form-control" required>
                                                <option value="">Select Leave Type</option>
                                                <option value="Sick">Sick</option>
                                                <option value="Casual">Casual</option>
                                                <option value="Annual">Annual</option>
                                                <option value="Unpaid">Unpaid</option>
                                            </select>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label for="start_date" class="form-label">Start Date</label>
                                                <input type="date" name="start_date" id="start_date" class="form-control" required>
                                            </div>

                                            <div class="col-md-6 mb-3">
                                                <label for="end_date" class="form-label">End Date</label>
                                                <input type="date" name="end_date" id="end_date" class="form-control" required>
                                            </div>
                                        </div>

                                        <div class="mb-3">
                                            <label for="remarks" class="form-label">Remarks</label>
                                            <textarea name="remarks" id="remarks" class="form-control" rows="3" placeholder="Optional remarks..."></textarea>
                                        </div>

                                        <button type="submit" 
                                            class="btn border-0 text-white fw-semibold px-4 py-2 rounded-pill w-100"
                                            style="background: linear-gradient(90deg, #1e3c72, #2a5298); transition: 0.3s;">
                                            <i class="bi bi-send-check me-2"></i>Apply Leave
                                        </button>
                                    </form>

                                    <!-- Validation messages will appear here -->
                                    
                                    <div id="hrLeaveSuccessMessage" class="alert alert-success mt-3 d-none">
                                        <i class="bi bi-check-circle me-2"></i>Leave applied successfully! Admin will review your request.
                                    </div>
                                    
                                    <div id="hrLeaveErrorMessage" class="alert alert-danger mt-3 d-none">
                                        <i class="bi bi-exclamation-triangle me-2"></i>Error applying leave. Please try again.
                                    </div>
                                </div>
                            </div>

                            <!-- HR Leave History -->
                            <div class="card shadow-sm border-0">
                                <div class="card-header text-white"
                                    style="background: linear-gradient(90deg, #1e3c72, #2a5298);">
                                    <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2"></i>My Leave History</h5>
                                </div>

                                <div class="card-body">
                                    @if($hrLeaves && $hrLeaves->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped" id="hrLeavesTable">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th>Type</th>
                                                    <th>Start Date</th>
                                                    <th>End Date</th>
                                                    <th>Status</th>
                                                    <th>Remarks</th>
                                                    <th>Applied On</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($hrLeaves as $leave)
                                                    <tr>
                                                        <td>{{ $leave->leave_type }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($leave->start_date)->format('M d, Y') }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($leave->end_date)->format('M d, Y') }}</td>
                                                        <td>
                                                            <span class="badge 
                                                                @if($leave->status == 'Pending') bg-warning
                                                                @elseif($leave->status == 'Approved') bg-success
                                                                @else bg-danger
                                                                @endif">
                                                                {{ $leave->status }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $leave->remarks ?? '-' }}</td>
                                                        <td>{{ \Carbon\Carbon::parse($leave->created_at)->format('M d, Y') }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @else
                                    <div class="text-center py-4">
                                        <i class="bi bi-inbox display-4 text-muted mb-3"></i>
                                        <p class="text-muted">No leave requests found</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings -->
            <div id="settingsSection" class="d-none">
                <div class="card shadow border-0">
                    <div class="card-header text-white" style="background: linear-gradient(90deg, #1e3c72, #2a5298);">
                        <h5 class="mb-0">HR Settings</h5>
                    </div>
                    <div class="card-body">
                        <!-- HR Details -->
                        <ul class="list-group list-group-flush" id="hrDetails">
                            <li class="list-group-item"><strong>ID:</strong> {{ $hrUser->id }}</li>
                            <li class="list-group-item"><strong>Name:</strong> {{ $hrUser->name }}</li>
                            <li class="list-group-item"><strong>Department:</strong> {{ $hrUser->hrDepartment->department_name ?? 'N/A' }}</li>
                            <li class="list-group-item"><strong>Gender:</strong> {{ $hrUser->gender ?? 'N/A' }}</li>
                            <li class="list-group-item"><strong>Date of Birth:</strong> {{ $hrUser->date_of_birth ? \Carbon\Carbon::parse($hrUser->date_of_birth)->format('d-M-Y') : 'N/A' }}</li>
                            <li class="list-group-item"><strong>Age:</strong> 
                                @if($hrUser->date_of_birth)
                                    {{ \Carbon\Carbon::parse($hrUser->date_of_birth)->age }} years
                                @else
                                    N/A
                                @endif
                            </li>
                            <li class="list-group-item"><strong>Email:</strong> {{ $hrUser->email }}</li>
                            <li class="list-group-item"><strong>Phone:</strong> <span id="phoneDisplay">{{ $hrUser->phone }}</span></li>
                            <li class="list-group-item"><strong>Joined:</strong> {{ $hrUser->date_of_joining ? \Carbon\Carbon::parse($hrUser->date_of_joining)->format('d-M-Y') : 'N/A' }}</li>
                            <li class="list-group-item"><strong>Profile Picture:</strong>
                                <br>
                                <img src="{{ $hrUser->photo ? asset('storage/'.$hrUser->photo) : asset('images/default-avatar.png') }}" 
                                    alt="HR Photo" 
                                    class="img-fluid rounded" 
                                    style="width:100px;" 
                                    id="photoDisplay">
                            </li>
                        </ul>

                        <button class="btn btn-gradient mt-3" id="editBtn">Edit</button>

                        <!-- Edit Form (hidden initially) -->
                        <form id="settingsForm" class="mt-3 d-none" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label for="phoneInput" class="form-label">Phone</label>
                                <input type="text" id="phoneInput" name="phone" class="form-control" value="{{ $hrUser->phone }}" required>
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

                            <button type="submit" class="btn btn-gradient">Save Changes</button>
                            <button type="button" class="btn btn-secondary" id="cancelEdit">Cancel</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection


{{-- script for HR attendance --}}
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script> 
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>                   
<script>
    // HR ATTENDANCE WITH AJAX - FIXED PERSISTENCE
    let hrWorkTimer = null;
    let hrBreakTimer = null;
    let hrTotalWorkSeconds = 0;
    let hrTotalBreakSeconds = 0;
    let hrIsOnBreak = false;
    let hrCurrentStatus = 'checked_out';
    let hrTotalBreaksTaken = 0;
    let hrIsInitialized = false;
    let hrLastCheckInTime = null;

    // HR Local Storage Keys - SAME AS EMPLOYEE SYSTEM
    const HR_STORAGE_KEYS = {
        WORK_SECONDS: 'hr_attendance_work_seconds',
        BREAK_SECONDS: 'hr_attendance_break_seconds',
        IS_ON_BREAK: 'hr_attendance_is_on_break',
        CURRENT_STATUS: 'hr_attendance_current_status',
        BREAKS_TAKEN: 'hr_attendance_breaks_taken',
        CHECKIN_TIME: 'hr_attendance_checkin_time',
        LAST_SYNC: 'hr_attendance_last_sync'
    };

    // ========== PERSISTENCE FUNCTIONS ==========

    function hrSaveTimerState() {
        try {
            localStorage.setItem(HR_STORAGE_KEYS.WORK_SECONDS, hrTotalWorkSeconds.toString());
            localStorage.setItem(HR_STORAGE_KEYS.BREAK_SECONDS, hrTotalBreakSeconds.toString());
            localStorage.setItem(HR_STORAGE_KEYS.IS_ON_BREAK, hrIsOnBreak.toString());
            localStorage.setItem(HR_STORAGE_KEYS.CURRENT_STATUS, hrCurrentStatus);
            localStorage.setItem(HR_STORAGE_KEYS.BREAKS_TAKEN, hrTotalBreaksTaken.toString());
            if (hrLastCheckInTime) {
                localStorage.setItem(HR_STORAGE_KEYS.CHECKIN_TIME, hrLastCheckInTime);
            }
            localStorage.setItem(HR_STORAGE_KEYS.LAST_SYNC, Date.now().toString());
        } catch (error) {
            console.error('HR Error saving timer state:', error);
        }
    }

    function hrLoadTimerState() {
        try {
            const savedWorkSeconds = localStorage.getItem(HR_STORAGE_KEYS.WORK_SECONDS);
            const savedBreakSeconds = localStorage.getItem(HR_STORAGE_KEYS.BREAK_SECONDS);
            const savedIsOnBreak = localStorage.getItem(HR_STORAGE_KEYS.IS_ON_BREAK);
            const savedStatus = localStorage.getItem(HR_STORAGE_KEYS.CURRENT_STATUS);
            const savedBreaksTaken = localStorage.getItem(HR_STORAGE_KEYS.BREAKS_TAKEN);
            const savedCheckinTime = localStorage.getItem(HR_STORAGE_KEYS.CHECKIN_TIME);

            hrTotalWorkSeconds = Math.max(0, parseInt(savedWorkSeconds) || 0);
            hrTotalBreakSeconds = Math.max(0, parseInt(savedBreakSeconds) || 0);
            hrIsOnBreak = savedIsOnBreak === 'true';
            hrCurrentStatus = savedStatus || 'checked_out';
            hrTotalBreaksTaken = Math.max(0, parseInt(savedBreaksTaken) || 0);
            hrLastCheckInTime = savedCheckinTime;

            if (!['checked_out', 'checked_in', 'on_break'].includes(hrCurrentStatus)) {
                hrCurrentStatus = 'checked_out';
            }

            return true;
        } catch (error) {
            console.error('HR Error loading timer state:', error);
            hrTotalWorkSeconds = 0;
            hrTotalBreakSeconds = 0;
            hrIsOnBreak = false;
            hrCurrentStatus = 'checked_out';
            hrTotalBreaksTaken = 0;
            hrLastCheckInTime = null;
            return false;
        }
    }

    function hrClearTimerState() {
        try {
            Object.values(HR_STORAGE_KEYS).forEach(key => {
                localStorage.removeItem(key);
            });
        } catch (error) {
            console.error('HR Error clearing timer state:', error);
        }
    }

    // ========== ELAPSED TIME CALCULATION ==========

    function hrCalculateElapsedTime() {
        try {
            const lastSync = localStorage.getItem(HR_STORAGE_KEYS.LAST_SYNC);
            if (!lastSync) return 0;
            
            const lastSyncTime = parseInt(lastSync);
            const now = Date.now();
            
            const elapsed = Math.max(0, now - lastSyncTime);
            return Math.floor(elapsed / 1000);
        } catch (error) {
            console.error('HR Error calculating elapsed time:', error);
            return 0;
        }
    }

    // ========== TIMER FUNCTIONS ==========

    function hrStartWorkTimer() {
        if (hrWorkTimer) {
            clearInterval(hrWorkTimer);
        }
        
        hrWorkTimer = setInterval(function() {
            hrUpdateWorkTimer();
        }, 1000);
        
        console.log('HR Work timer started');
    }

    function hrStartBreakTimer() {
        if (hrBreakTimer) {
            clearInterval(hrBreakTimer);
        }
        
        hrBreakTimer = setInterval(function() {
            hrUpdateBreakTimer();
        }, 1000);
        
        console.log('HR Break timer started');
    }

    function hrSecondsToTime(totalSeconds) {
        const safeSeconds = Math.max(0, totalSeconds);
        const hours = Math.floor(safeSeconds / 3600);
        const minutes = Math.floor((safeSeconds % 3600) / 60);
        const seconds = safeSeconds % 60;
        return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
    }

    function hrUpdateWorkTimer() {
        if (!hrIsOnBreak && hrCurrentStatus === 'checked_in') {
            hrTotalWorkSeconds++;
            hrTotalWorkSeconds = Math.max(0, hrTotalWorkSeconds);
            
            if (hrTotalWorkSeconds % 10 === 0) {
                hrSaveTimerState();
            }
            
            if (document.getElementById('hrTotalWorkTime')) {
                document.getElementById('hrTotalWorkTime').textContent = hrSecondsToTime(hrTotalWorkSeconds);
            }
        }
    }

    function hrUpdateBreakTimer() {
        if (hrIsOnBreak && hrCurrentStatus === 'on_break') {
            hrTotalBreakSeconds++;
            hrTotalBreakSeconds = Math.max(0, hrTotalBreakSeconds);
            
            if (hrTotalBreakSeconds % 10 === 0) {
                hrSaveTimerState();
            }
            
            if (document.getElementById('hrBreakTime')) {
                document.getElementById('hrBreakTime').textContent = hrSecondsToTime(hrTotalBreakSeconds);
            }
        }
    }

    function hrStopAllTimers() {
        if (hrWorkTimer) {
            clearInterval(hrWorkTimer);
            hrWorkTimer = null;
        }
        if (hrBreakTimer) {
            clearInterval(hrBreakTimer);
            hrBreakTimer = null;
        }
        hrSaveTimerState();
    }

    // ========== RESTORE TIMERS WITH ELAPSED TIME ==========

    function hrRestoreTimers() {
        console.log('HR Restoring timers from saved state...');
        
        const elapsedSeconds = hrCalculateElapsedTime();
        console.log('Time elapsed since last sync:', elapsedSeconds, 'seconds');
        
        // Add elapsed time to appropriate counter
        if (hrCurrentStatus === 'checked_in') {
            if (hrIsOnBreak) {
                hrTotalBreakSeconds += elapsedSeconds;
                console.log('Added elapsed time to break seconds:', elapsedSeconds);
                hrStartBreakTimer();
            } else {
                hrTotalWorkSeconds += elapsedSeconds;
                console.log('Added elapsed time to work seconds:', elapsedSeconds);
                hrStartWorkTimer();
            }
        } else if (hrCurrentStatus === 'on_break') {
            hrTotalBreakSeconds += elapsedSeconds;
            console.log('Added elapsed time to break seconds:', elapsedSeconds);
            hrStartBreakTimer();
        }
        
        // Final validation
        hrTotalWorkSeconds = Math.max(0, hrTotalWorkSeconds);
        hrTotalBreakSeconds = Math.max(0, hrTotalBreakSeconds);
        
        // Update UI immediately
        hrUpdateAttendanceUI();
        
        console.log('HR Timers restored successfully. Current state:', {
            status: hrCurrentStatus,
            isOnBreak: hrIsOnBreak,
            workSeconds: hrTotalWorkSeconds,
            breakSeconds: hrTotalBreakSeconds
        });
    }

    // ========== AJAX FUNCTIONS ==========

    function hrAjaxRequest(url, method, data, successCallback, errorCallback) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        $.ajax({
            url: url,
            type: method,
            data: data,
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                if (typeof successCallback === 'function') {
                    successCallback(response);
                }
            },
            error: function(xhr, status, error) {
                console.error('HR AJAX Error:', error);
                if (typeof errorCallback === 'function') {
                    errorCallback(xhr, status, error);
                } else {
                    let errorMessage = 'Request failed. Please try again.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    Swal.fire({
                        title: 'Error',
                        text: errorMessage,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            }
        });
    }

    // ========== ATTENDANCE FUNCTIONS ==========

    function hrUpdateAttendanceUI() {
        const statusDisplay = document.getElementById('hrStatusDisplay');
        const checkinBtn = document.getElementById('hrCheckinBtn');
        const checkoutBtn = document.getElementById('hrCheckoutBtn');
        const breakinBtn = document.getElementById('hrBreakinBtn');
        const breakoutBtn = document.getElementById('hrBreakoutBtn');
        const totalWorkTime = document.getElementById('hrTotalWorkTime');
        const breakTime = document.getElementById('hrBreakTime');

        if (!totalWorkTime || !breakTime) return;

        totalWorkTime.textContent = hrSecondsToTime(hrTotalWorkSeconds);
        breakTime.textContent = hrSecondsToTime(hrTotalBreakSeconds);

        switch(hrCurrentStatus) {
            case 'checked_out':
                statusDisplay.textContent = 'Status: Checked Out';
                statusDisplay.style.color = '#dc3545';
                checkinBtn.disabled = false;
                checkoutBtn.disabled = true;
                breakinBtn.disabled = true;
                breakoutBtn.disabled = true;
                break;
            case 'checked_in':
                statusDisplay.textContent = 'Status: Checked In - Working';
                statusDisplay.style.color = '#28a745';
                checkinBtn.disabled = true;
                checkoutBtn.disabled = false;
                breakinBtn.disabled = false;
                breakoutBtn.disabled = true;
                break;
            case 'on_break':
                statusDisplay.textContent = 'Status: On Break';
                statusDisplay.style.color = '#ffc107';
                checkinBtn.disabled = true;
                checkoutBtn.disabled = true;
                breakinBtn.disabled = true;
                breakoutBtn.disabled = false;
                break;
        }
        
        hrSaveTimerState();
    }

    function hrCheckIn() {
        console.log('HR CheckIn called');
        
        Swal.fire({
            title: 'Checking In...',
            text: 'Please wait',
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false
        });

        hrAjaxRequest(
            '/hr/attendance/checkin',
            'POST',
            {},
            function(response) {
                if (response.success) {
                    hrCurrentStatus = 'checked_in';
                    hrTotalWorkSeconds = 0;
                    hrTotalBreakSeconds = 0;
                    hrTotalBreaksTaken = 0;
                    hrIsOnBreak = false;
                    hrLastCheckInTime = new Date().toISOString();
                    
                    hrUpdateAttendanceUI();
                    hrStartWorkTimer();
                    
                    Swal.fire({
                        title: 'Success!',
                        text: response.message,
                        icon: 'success',
                        timer: 3000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        title: 'Check-in Failed',
                        text: response.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            function(xhr, status, error) {
                Swal.fire({
                    title: 'Check-in Failed',
                    text: 'Network error. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        );
    }

    function hrCheckOut() {
        console.log('HR CheckOut called');
        
        hrStopAllTimers();
        
        Swal.fire({
            title: 'Checking Out...',
            text: 'Please wait',
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false
        });

        hrAjaxRequest(
            '/hr/attendance/checkout',
            'POST',
            {},
            function(response) {
                if (response.success) {
                    hrCurrentStatus = 'checked_out';
                    hrIsOnBreak = false;
                    
                    hrUpdateAttendanceUI();
                    hrClearTimerState();
                    
                    Swal.fire({
                        title: 'Checked Out Successfully!',
                        text: response.message,
                        icon: 'success',
                        timer: 3000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        title: 'Check-out Failed',
                        text: response.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            function(xhr, status, error) {
                hrStopAllTimers();
                hrCurrentStatus = 'checked_out';
                hrUpdateAttendanceUI();
                
                Swal.fire({
                    title: 'Check-out Failed',
                    text: 'Network error. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        );
    }

    function hrBreakIn() {
        Swal.fire({
            title: 'Starting Break...',
            text: 'Please wait',
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false
        });

        hrAjaxRequest(
            '/hr/attendance/breakin',
            'POST',
            {},
            function(response) {
                if (response.success) {
                    hrCurrentStatus = 'on_break';
                    hrIsOnBreak = true;
                    
                    if (hrWorkTimer) clearInterval(hrWorkTimer);
                    hrStartBreakTimer();
                    
                    hrUpdateAttendanceUI();
                    
                    Swal.fire({
                        title: 'Break Started!',
                        text: response.message,
                        icon: 'success',
                        timer: 3000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        title: 'Break Start Failed',
                        text: response.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            function(xhr, status, error) {
                Swal.fire({
                    title: 'Break Start Failed',
                    text: 'Network error. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        );
    }

    function hrBreakOut() {
        Swal.fire({
            title: 'Ending Break...',
            text: 'Please wait',
            icon: 'info',
            showConfirmButton: false,
            allowOutsideClick: false
        });

        hrAjaxRequest(
            '/hr/attendance/breakout',
            'POST',
            {},
            function(response) {
                if (response.success) {
                    hrCurrentStatus = 'checked_in';
                    hrIsOnBreak = false;
                    hrTotalBreaksTaken++;
                    
                    if (hrBreakTimer) clearInterval(hrBreakTimer);
                    hrStartWorkTimer();
                    
                    hrUpdateAttendanceUI();
                    
                    Swal.fire({
                        title: 'Break Ended!',
                        text: response.message,
                        icon: 'success',
                        timer: 3000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        title: 'Break End Failed',
                        text: response.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            function(xhr, status, error) {
                Swal.fire({
                    title: 'Break End Failed',
                    text: 'Network error. Please try again.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        );
    }

    // ========== LOAD ATTENDANCE DATA ==========
    function hrLoadAttendanceData() {
        // First load from localStorage
        const hasLocalState = hrLoadTimerState();
        console.log('HR Local state loaded:', {
            hasLocalState: hasLocalState,
            currentStatus: hrCurrentStatus,
            workSeconds: hrTotalWorkSeconds,
            breakSeconds: hrTotalBreakSeconds
        });

        // Then check server status
        hrAjaxRequest(
            '/hr/attendance/status',
            'GET',
            {},
            function(response) {
                console.log('HR Server attendance response:', response);
                
                if (response.attendance) {
                    const attendance = response.attendance;
                    
                    if (attendance.status === 'checked_in' || attendance.status === 'on_break') {
                        // If we have local state, trust it for timing but sync status
                        if (hasLocalState) {
                            console.log('HR Syncing local state with server status');
                            hrCurrentStatus = attendance.status;
                            hrTotalBreaksTaken = attendance.breaks ? attendance.breaks.length : 0;
                            
                            // RESTORE TIMERS WITH ELAPSED TIME - THIS IS THE KEY FIX
                            hrRestoreTimers();
                        } else {
                            // No local state, initialize from server
                            console.log('HR Initializing from server data');
                            hrCurrentStatus = attendance.status;
                            
                            if (attendance.check_in) {
                                const checkInTime = new Date(attendance.check_in);
                                const now = new Date();
                                const workSeconds = Math.floor((now - checkInTime) / 1000);
                                
                                hrTotalWorkSeconds = Math.max(0, workSeconds);
                                hrTotalBreaksTaken = attendance.breaks ? attendance.breaks.length : 0;
                                
                                // Calculate break seconds from server breaks
                                if (attendance.breaks && attendance.breaks.length > 0) {
                                    hrTotalBreakSeconds = attendance.breaks.reduce((total, breakItem) => {
                                        return total + (breakItem.duration || 0);
                                    }, 0);
                                }
                                
                                // If currently on break, add current break duration
                                if (attendance.status === 'on_break' && response.break_start_time) {
                                    hrIsOnBreak = true;
                                    const breakStart = new Date(response.break_start_time * 1000);
                                    const currentBreakSeconds = Math.floor((now - breakStart) / 1000);
                                    hrTotalBreakSeconds += currentBreakSeconds;
                                }
                                
                                // Save this calculated state to localStorage
                                hrSaveTimerState();
                            }
                            
                            // Start appropriate timers
                            if (hrCurrentStatus === 'checked_in') {
                                hrStartWorkTimer();
                            } else if (hrCurrentStatus === 'on_break') {
                                hrStartBreakTimer();
                            }
                            
                            hrUpdateAttendanceUI();
                        }
                    } else if (attendance.status === 'checked_out') {
                        // Server shows checked out, clear local state
                        console.log('HR Server shows checked out, clearing local state');
                        hrCurrentStatus = 'checked_out';
                        hrClearTimerState();
                        hrUpdateAttendanceUI();
                    }
                } else {
                    // No server attendance record
                    console.log('HR No server attendance found');
                    
                    if (hasLocalState && (hrCurrentStatus === 'checked_in' || hrCurrentStatus === 'on_break')) {
                        // We have active local session but no server record - trust local storage
                        console.log('HR Active local session found (no server data), restoring timers...');
                        hrRestoreTimers();
                    } else if (!hasLocalState) {
                        // No data anywhere - reset to safe state
                        console.log('HR No data found anywhere, resetting to checked_out');
                        hrCurrentStatus = 'checked_out';
                        hrTotalWorkSeconds = 0;
                        hrTotalBreakSeconds = 0;
                        hrTotalBreaksTaken = 0;
                        hrIsOnBreak = false;
                        hrUpdateAttendanceUI();
                    }
                }
            },
            function(xhr, status, error) {
                console.error('HR Failed to load attendance data from server:', error);
                
                // Try to restore from localStorage as fallback
                if (hasLocalState && (hrCurrentStatus === 'checked_in' || hrCurrentStatus === 'on_break')) {
                    console.log('HR Server failed, restoring from localStorage...');
                    hrRestoreTimers();
                } else if (!hasLocalState) {
                    // Reset to safe state
                    hrCurrentStatus = 'checked_out';
                    hrTotalWorkSeconds = 0;
                    hrTotalBreakSeconds = 0;
                    hrTotalBreaksTaken = 0;
                    hrIsOnBreak = false;
                    hrUpdateAttendanceUI();
                }
            }
        );
    }

    // ========== INITIALIZATION ==========

    function initializeHRAttendanceSystem() {
        if (hrIsInitialized) {
            hrUpdateAttendanceUI();
            return;
        }
        
        hrIsInitialized = true;
        hrLoadAttendanceData();
        
        // Set up periodic sync
        setInterval(() => {
            if (hrCurrentStatus === 'checked_in' || hrCurrentStatus === 'on_break') {
                hrSaveTimerState();
            }
        }, 30000);
        
        // Save state before page unload
        $(window).on('beforeunload', function() {
            hrSaveTimerState();
        });

        // Notify that HR attendance system is ready
        $(document).trigger('hrAttendanceSystemReady');
    }

    // Initialize when document is ready
    $(document).ready(function() {
        initializeHRAttendanceSystem();
    });

    // Make functions globally available
    window.hrCheckIn = hrCheckIn;
    window.hrCheckOut = hrCheckOut;
    window.hrBreakIn = hrBreakIn;
    window.hrBreakOut = hrBreakOut;
    window.hrUpdateAttendanceUI = hrUpdateAttendanceUI;
    window.hrStartWorkTimer = hrStartWorkTimer;
    window.hrStartBreakTimer = hrStartBreakTimer;
</script>

{{-- script for sidebar and edit emp --}}
<script>
    $(document).ready(function(){
        console.log('=== HR DASHBOARD INITIALIZED ===');

        // Add content-section class to all main sections
        $('#dashboardSection, #employeesSection, #addEmployeeSection, #editEmployeeSection, #hrattendanceSection, #attendanceSection, #leavesSection, #applyLeavesSection, #settingsSection').addClass('content-section');

        // =============================================
        // MAIN SECTION SWITCHING FUNCTION
        // =============================================
        function switchToSection(section, updateUrl = true) {
            console.log('Switching to section:', section, 'Update URL:', updateUrl);
            
            // Remove active class from all links
            $('.sidebar-theme .nav-link').removeClass('active bg-primary');
            
            // Add active class to clicked link
            const navSection = section.replace('Section', '');
            const targetNavSection = (navSection === 'editEmployee') ? 'employees' : navSection;
            $(`.sidebar-theme .nav-link[data-section="${targetNavSection}"]`).addClass('active bg-primary');

            // Hide all sections
            $('.content-section').addClass('d-none');

            // Show selected section
            const targetSection = $('#' + section);
            if (targetSection.length) {
                targetSection.removeClass('d-none');
                console.log('Successfully showed section:', section);
                
                // Save active section to localStorage
                localStorage.setItem('hr_active_section', section);
                
                // Update HR attendance UI if switching to HR attendance section
                if (section === 'hrattendanceSection' && typeof window.hrUpdateAttendanceUI === 'function') {
                    setTimeout(() => {
                        console.log('Calling hrUpdateAttendanceUI after section switch');
                        window.hrUpdateAttendanceUI();
                    }, 100);
                }
            } else {
                console.error('Section not found:', section);
            }

            // Update URL if requested
            if (updateUrl && section !== 'editEmployeeSection') {
                const newUrl = `${window.location.pathname}?tab=${section}`;
                window.history.pushState({}, '', newUrl);
            }
        }

        // =============================================
        // URL PARAMETER HANDLING
        // =============================================
        function handleUrlParameters() {
            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('tab');
            const employeeId = urlParams.get('employee_id');
            
            console.log('URL Parameters - Tab:', tab, 'Employee ID:', employeeId);
            console.log('Edit Employee Data:', @json($editEmployee));

            // Priority 1: Edit Employee Section (from URL)
            if (tab === 'editEmployeeSection' && employeeId) {
                console.log('URL indicates EDIT EMPLOYEE section should be shown');
                
                // Hide all sections
                $('.content-section').addClass('d-none');
                
                // Show edit section
                $('#editEmployeeSection').removeClass('d-none');
                
                // Update navigation to highlight employees
                $('.sidebar-theme .nav-link').removeClass('active bg-primary');
                $('.sidebar-theme .nav-link[data-section="employees"]').addClass('active bg-primary');
                
                console.log('Edit employee section should now be visible');
            } 
            // Priority 2: Other sections from URL
            else if (tab) {
                console.log('URL indicates section:', tab);
                switchToSection(tab, false);
            } 
            // Priority 3: Load from localStorage
            else {
                loadSavedSection();
            }
        }

        // =============================================
        // LOAD SAVED SECTION
        // =============================================
        function loadActiveSection() {
            try {
                return localStorage.getItem('hr_active_section') || 'dashboardSection';
            } catch (error) {
                console.error('Error loading active section:', error);
                return 'dashboardSection';
            }
        }

        function loadSavedSection() {
            const savedSection = loadActiveSection();
            console.log('Restoring active section from localStorage:', savedSection);
            switchToSection(savedSection, false);
            
            // If it's HR attendance section, wait for the system to be ready
            if (savedSection === 'hrattendanceSection') {
                let attempts = 0;
                const maxAttempts = 10;
                
                const checkAttendanceSystem = setInterval(() => {
                    attempts++;
                    if (typeof window.hrUpdateAttendanceUI === 'function') {
                        console.log('HR attendance system ready, updating UI');
                        window.hrUpdateAttendanceUI();
                        clearInterval(checkAttendanceSystem);
                    } else if (attempts >= maxAttempts) {
                        console.log('HR attendance system not available after max attempts');
                        clearInterval(checkAttendanceSystem);
                    }
                }, 200);
            }
        }

        // =============================================
        // EVENT HANDLERS
        // =============================================
        
        // Click event for sidebar links
        $('.sidebar-theme .nav-link').click(function(e){
            e.preventDefault();
            const section = $(this).data('section') + 'Section';
            console.log('Clicked sidebar link:', section);
            switchToSection(section);
        });

        // Click event for quick action buttons
        $('.quick-action-btn').click(function(e){
            e.preventDefault();
            const section = $(this).data('section');
            console.log('Clicked quick action:', section);
            switchToSection(section);
        });

        // Handle edit links - allow normal navigation for edit functionality
        $(document).on('click', 'a[href*="editEmployeeSection"]', function(e) {
            console.log('Edit link clicked, allowing normal navigation');
            // Let the normal link behavior happen (page reload) for edit links
            // This ensures the server loads the correct employee data
        });

        // Handle browser back/forward buttons
        window.addEventListener('popstate', function() {
            console.log('Browser navigation detected');
            handleUrlParameters();
        });

        // Listen for HR attendance system ready event
        $(document).on('hrAttendanceSystemReady', function() {
            console.log('HR Attendance system ready event received');
            const currentSection = localStorage.getItem('hr_active_section');
            if (currentSection === 'hrattendanceSection' && typeof window.hrUpdateAttendanceUI === 'function') {
                setTimeout(() => {
                    window.hrUpdateAttendanceUI();
                }, 300);
            }
        });

        // =============================================
        // EMERGENCY OVERRIDE FOR EDIT SECTION
        // =============================================
        function emergencyEditSectionOverride() {
            const urlParams = new URLSearchParams(window.location.search);
            const tab = urlParams.get('tab');
            const employeeId = urlParams.get('employee_id');
            
            if (tab === 'editEmployeeSection' && employeeId) {
                console.log('EMERGENCY: Forcing edit section visibility');
                
                // Nuclear option - completely override everything
                $('.content-section').addClass('d-none');
                $('#editEmployeeSection').removeClass('d-none');
                
                // Force CSS to ensure visibility
                $('#editEmployeeSection').css({
                    'display': 'block',
                    'visibility': 'visible',
                    'opacity': '1'
                });
                
                // Update nav manually
                $('.sidebar-theme .nav-link').removeClass('active bg-primary');
                $('.sidebar-theme .nav-link[data-section="employees"]').addClass('active bg-primary');
                
                console.log('EMERGENCY: Edit section should now be visible');
            }
        }

        // =============================================
        // INITIALIZATION
        // =============================================
        
        // Handle URL parameters first
        handleUrlParameters();
        
        // Load saved section after short delay (fallback)
        setTimeout(loadSavedSection, 50);
        
        // Emergency override with longer delay
        setTimeout(emergencyEditSectionOverride, 500);
        
        // Debug: Check final section states
        setTimeout(function() {
            console.log('=== FINAL SECTION STATES ===');
            $('.content-section').each(function() {
                console.log(`Section: ${this.id}, Visible: ${!$(this).hasClass('d-none')}`);
            });
        }, 1000);
    });
</script>

{{-- script for HR leave application --}}
<script>
    $(document).ready(function() {
        let currentMonthRemainingDays = 2; // Default value

        // Initialize HR leave summary when page loads
        updateHrLeaveSummary();

        // Real-time validation when dates change
        $('#start_date, #end_date').on('change', function() {
            validateHrLeaveDates();
        });

        // Function to validate HR leave dates
        function validateHrLeaveDates() {
            const startDate = $('#start_date').val();
            const endDate = $('#end_date').val();

            if (startDate && endDate) {
                // Show loading state
                const submitButton = $('#applyHrLeaveForm button[type="submit"]');
                submitButton.prop('disabled', true).html('<i class="bi bi-hourglass-split me-2"></i>Checking...');

                $.ajax({
                    url: "{{ route('hr.checkAvailableLeaves') }}",
                    method: "POST",
                    data: {
                        start_date: startDate,
                        end_date: endDate,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        console.log('Leave availability response:', response);
                        if (response.success) {
                            currentMonthRemainingDays = response.remaining_days;
                            
                            if (response.can_apply) {
                                // Enable submit button
                                submitButton.prop('disabled', false).html('Apply Leave');
                                showHrDateValidationMessage(response.message, 'success');
                            } else {
                                // Keep button disabled
                                submitButton.prop('disabled', true).html('Apply Leave');
                                
                                if (response.is_duplicate) {
                                    showHrDateValidationMessage(response.message, 'error');
                                } else {
                                    showHrDateValidationMessage(response.message, 'warning');
                                }
                            }
                        }
                    },
                    error: function(xhr) {
                        console.error('Error checking leave availability:', xhr);
                        console.log('XHR response:', xhr.responseText);
                        submitButton.prop('disabled', false).html('Apply Leave');
                        showHrDateValidationMessage('Error checking leave availability. Please try again.', 'error');
                    }
                });
            } else {
                // If dates are not complete, enable button but show info
                const submitButton = $('#applyHrLeaveForm button[type="submit"]');
                submitButton.prop('disabled', false).html('Apply Leave');
                $('#hrDateValidationMessage').remove();
            }
        }

        // Function to show HR validation messages
        function showHrDateValidationMessage(message, type) {
            // Remove existing messages
            $('#hrDateValidationMessage').remove();
            
            let messageClass = 'alert-info';
            if (type === 'success') messageClass = 'alert-success';
            if (type === 'error') messageClass = 'alert-danger';
            if (type === 'warning') messageClass = 'alert-warning';
            
            const messageHtml = `<div id="hrDateValidationMessage" class="alert ${messageClass} mt-3">${message}</div>`;
            
            // Insert after the form but before success/error messages
            $('#applyHrLeaveForm').append(messageHtml);
        }

        // HR Leave Form Submission
        $('#applyHrLeaveForm').on('submit', function(e) {
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

            // Get form data
            const formData = $(this).serialize();
            
            // Show loading state
            const submitBtn = $(this).find('button[type="submit"]');
            const originalText = submitBtn.html();
            submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-2"></i>Applying...');

            // Hide previous messages
            $('#hrLeaveSuccessMessage').addClass('d-none');
            $('#hrLeaveErrorMessage').addClass('d-none');
            $('#hrDateValidationMessage').remove();

            $.ajax({
                url: "{{ route('hr.applyLeave') }}",
                method: "POST",
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                success: function(response) {
                    console.log('Leave application response:', response);
                    if (response.success) {
                        // Show success message
                        $('#hrLeaveSuccessMessage').removeClass('d-none').text(response.message);
                        
                        // Reset form and clear validation messages
                        $('#applyHrLeaveForm')[0].reset();
                        $('#hrDateValidationMessage').remove();
                        
                        // Update HR leave summary
                        updateHrLeaveSummary();
                        
                        // Reload the page after 2 seconds to show updated leave list
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        $('#hrLeaveErrorMessage').removeClass('d-none').text(response.message || 'Error applying leave');
                    }
                },
                error: function(xhr) {
                    console.error('Leave application error:', xhr);
                    console.log('XHR response:', xhr.responseText);
                    
                    let errorMessage = 'Error submitting leave request! Please try again.';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.status === 422) {
                        // Validation errors - this will catch "end date before start date" from server
                        const errors = xhr.responseJSON.errors;
                        errorMessage = Object.values(errors)[0][0];
                    }
                    
                    $('#hrLeaveErrorMessage').removeClass('d-none').text(errorMessage);
                },
                complete: function() {
                    // Reset button state
                    submitBtn.prop('disabled', false).html(originalText);
                }
            });
        });

        // Add HR leave summary display
        function updateHrLeaveSummary() {
            console.log('Fetching HR leave summary...');
            
            $.ajax({
                url: "{{ route('hr.getLeaveSummary') }}",
                method: "GET",
                success: function(response) {
                    console.log('HR Leave Summary Response:', response);
                    if (response.success) {
                        // Create or update HR leave summary
                        if ($('#hrLeaveSummary').length === 0) {
                            $('#applyHrLeaveForm').before(`
                                <div id="hrLeaveSummary" class="alert alert-info mb-4">
                                    <h6 class="alert-heading"><i class="bi bi-info-circle me-2"></i>Your Leave Summary This Month</h6>
                                    <hr>
                                    <div class="row">
                                        <div class="col-6">
                                            <strong>Total Leaves Taken:</strong><br>
                                            <span class="h5">${response.approved_days} days</span>
                                        </div>
                                        <div class="col-6">
                                            <strong>Remaining Leaves:</strong><br>
                                            <span class="h5 text-success">${response.remaining_days} days</span>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <small class="text-muted">Total Limit: 2 days per month</small>
                                    </div>
                                </div>
                            `);
                        } else {
                            $('#hrLeaveSummary').html(`
                                <h6 class="alert-heading"><i class="bi bi-info-circle me-2"></i>Your Leave Summary This Month</h6>
                                <hr>
                                <div class="row">
                                    <div class="col-6">
                                        <strong>Total Leaves Taken:</strong><br>
                                        <span class="h5">${response.approved_days} days</span>
                                    </div>
                                    <div class="col-6">
                                        <strong>Remaining Leaves:</strong><br>
                                        <span class="h5 text-success">${response.remaining_days} days</span>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted">Total Limit: 2 days per month</small>
                                </div>
                            `);
                        }
                    } else {
                        console.warn('HR leave summary returned success: false', response);
                        // Don't show error summary - just proceed without it
                        $('#hrLeaveSummary').remove();
                    }
                },
                error: function(xhr, status, error) {
                    console.warn('Could not load HR leave summary (non-critical)', {
                        status: status,
                        error: error
                    });
                    // Remove any existing summary and proceed without it
                    $('#hrLeaveSummary').remove();
                }
            });
        }

        // Hover glow effect for submit button
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

{{-- employee leaves --}}
<script>
    $(document).ready(function() {

        // Approve leave
        $(document).on('click', '.approveBtn', function() {
            const leaveId = $(this).data('id');
            updateLeaveStatus(leaveId, 'Approved');
        });

        // Reject leave
        $(document).on('click', '.rejectBtn', function() {
            const leaveId = $(this).data('id');
            updateLeaveStatus(leaveId, 'Rejected');
        });

        function updateLeaveStatus(leaveId, status) {
            $.ajax({
                url: "{{ route('hr.updateLeaveStatus') }}",
                type: "POST",
                data: {
                    leave_id: leaveId,
                    status: status,
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    if (response.success) {
                        const row = $("#leaveRow-" + response.leave_id);
                        
                        // Update status text + badge color dynamically
                        const badge = row.find('.status-cell span');
                        badge.text(response.new_status);
                        
                        badge.removeClass('bg-warning bg-success bg-danger');
                        if (response.new_status === 'Approved') badge.addClass('bg-success');
                        else if (response.new_status === 'Rejected') badge.addClass('bg-danger');
                        else badge.addClass('bg-warning');

                        // Update action buttons after approval/rejection
                        if (response.new_status === 'Approved') {
                            row.find('td:last').html('<span class="text-muted"><i class="bi bi-check-circle text-success me-1"></i>Approved</span>');
                        } else if (response.new_status === 'Rejected') {
                            row.find('td:last').html('<span class="text-muted"><i class="bi bi-x-circle text-danger me-1"></i>Rejected</span>');
                        }
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 403) {
                        alert(xhr.responseJSON.message || 'You are not authorized to perform this action!');
                    } else {
                        alert("Something went wrong while updating status!");
                    }
                }
            });
        }
    });
</script>

<!-- JavaScript for Client-Side Filtering -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchBtn = document.getElementById('searchBtn');
        const resetBtn = document.getElementById('resetBtn');
        const searchInput = document.getElementById('search');
        const departmentSelect = document.getElementById('department');
        const designationInput = document.getElementById('designation');
        const statusSelect = document.getElementById('status');
        const employeeIdInput = document.getElementById('employee_id');
        const employeeRows = document.querySelectorAll('tbody tr.employee-row');
        const resultsCount = document.getElementById('resultsCount');

        console.log('Found employee rows:', employeeRows.length);

        // Function to filter employees
        function filterEmployees() {
            const searchValue = searchInput.value.toLowerCase().trim();
            const departmentValue = departmentSelect.value;
            const designationValue = designationInput.value.toLowerCase().trim();
            const statusValue = statusSelect.value;
            const employeeIdValue = employeeIdInput.value.trim();

            let visibleCount = 0;

            employeeRows.forEach(row => {
                const id = row.getAttribute('data-id');
                const name = row.getAttribute('data-name');
                const email = row.getAttribute('data-email');
                const phone = row.getAttribute('data-phone');
                const department = row.getAttribute('data-department');
                const departmentName = row.getAttribute('data-department-name');
                const designation = row.getAttribute('data-designation');
                const status = row.getAttribute('data-status');

                console.log('Checking row:', { id, name, department, designation, status });

                // Check all filter conditions
                const matchesSearch = !searchValue || 
                    name.includes(searchValue) || 
                    email.includes(searchValue) || 
                    phone.includes(searchValue) || 
                    designation.includes(searchValue) ||
                    departmentName.includes(searchValue);

                const matchesDepartment = !departmentValue || department === departmentValue;
                const matchesDesignation = !designationValue || designation.includes(designationValue);
                const matchesStatus = !statusValue || status === statusValue;
                const matchesEmployeeId = !employeeIdValue || id.toString().includes(employeeIdValue);

                // Show or hide row based on all conditions
                const shouldShow = matchesSearch && matchesDepartment && matchesDesignation && matchesStatus && matchesEmployeeId;
                
                if (shouldShow) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            // Update results count
            updateResultsCount(visibleCount);
        }

        // Function to update results count
        function updateResultsCount(visibleCount) {
            if (visibleCount === 0) {
                resultsCount.innerHTML = `
                    <div class="alert alert-warning text-center">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        No employees found matching your criteria.
                        <button type="button" id="resetBtn2" class="btn btn-sm btn-outline-primary ms-2">Show All Employees</button>
                    </div>
                `;
                
                // Add event listener to the new reset button
                document.getElementById('resetBtn2')?.addEventListener('click', resetFilters);
            } else {
                resultsCount.innerHTML = `
                    <div class="text-muted text-center">
                        <i class="bi bi-info-circle me-2"></i>
                        Showing: <strong>${visibleCount}</strong> of <strong>${employeeRows.length}</strong> employees
                    </div>
                `;
            }
        }

        // Function to reset all filters
        function resetFilters() {
            searchInput.value = '';
            departmentSelect.value = '';
            designationInput.value = '';
            statusSelect.value = '';
            employeeIdInput.value = '';
            
            // Show all rows
            employeeRows.forEach(row => {
                row.style.display = '';
            });
            
            // Reset results count
            resultsCount.innerHTML = `
                <div class="text-muted text-center">
                    <i class="bi bi-people me-2"></i>
                    Total: <strong>${employeeRows.length}</strong> employees
                </div>
            `;
        }

        // Event listeners
        searchBtn.addEventListener('click', filterEmployees);
        resetBtn.addEventListener('click', resetFilters);

        // Real-time filtering as user types
        searchInput.addEventListener('input', filterEmployees);
        designationInput.addEventListener('input', filterEmployees);
        employeeIdInput.addEventListener('input', filterEmployees);
        
        // Real-time filtering for dropdowns
        departmentSelect.addEventListener('change', filterEmployees);
        statusSelect.addEventListener('change', filterEmployees);

        // Add keyboard support (Enter key to search)
        [searchInput, designationInput, employeeIdInput].forEach(input => {
            input.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    filterEmployees();
                }
            });
        });

        // Initialize with proper count display
        updateResultsCount(employeeRows.length);
    });
</script>
<style>
    .card-header {
        background: linear-gradient(90deg, #1e3c72, #2a5298) !important;
    }

    .form-label {
        font-weight: 500;
        color: #495057;
    }

    .btn-primary {
        background: linear-gradient(90deg, #1e3c72, #2a5298);
        border: none;
    }

    .btn-primary:hover {
        background: linear-gradient(90deg, #2a5298, #1e3c72);
        transform: translateY(-1px);
    }

    .employee-row {
        transition: all 0.3s ease;
    }
</style>

{{-- validation script for add emp form --}}
<script>
    $(document).ready(function() {
        // Remove any existing error messages on page load
        $('.field-error').remove();

        // Real-time email validation while typing
        $('#email').on('input', function() {
            const email = $(this).val();
            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            
            // Remove existing error message
            $('#emailError').remove();
            
            if (email && !emailRegex.test(email)) {
                // Show error message below the field
                $(this).after('<small class="text-danger field-error" id="emailError">Please enter a valid email address (e.g., example@domain.com)</small>');
                $(this).addClass('is-invalid');
            } else {
                // Remove error message and styling
                $(this).removeClass('is-invalid');
            }
        });

        // Real-time password validation while typing
        $('#password').on('input', function() {
            const password = $(this).val();
            
            // Remove existing error message
            $('#passwordError').remove();
            
            if (password && (password.length < 6 || password.length > 12)) {
                // Show error message below the field
                $(this).after('<small class="text-danger field-error" id="passwordError">Password must be between 6 and 12 characters</small>');
                $(this).addClass('is-invalid');
            } else {
                // Remove error message and styling
                $(this).removeClass('is-invalid');
            }
        });

        // Real-time phone validation while typing
        $('#phone').on('input', function() {
            const phone = $(this).val();
            const phoneRegex = /^[6-9][0-9]{9}$/; // Updated regex to start with 6,7,8,9
            
            // Remove existing error message
            $('#phoneError').remove();
            
            if (phone) {
                if (!phoneRegex.test(phone)) {
                    // Show error message below the field
                    if (phone.length === 10) {
                        $(this).after('<small class="text-danger field-error" id="phoneError">Phone number must start with 6, 7, 8, or 9</small>');
                    } else {
                        $(this).after('<small class="text-danger field-error" id="phoneError">Phone number must be exactly 10 digits and start with 6, 7, 8, or 9</small>');
                    }
                    $(this).addClass('is-invalid');
                } else {
                    // Remove error message and styling
                    $(this).removeClass('is-invalid');
                }
            } else {
                // Remove error message and styling if field is empty
                $(this).removeClass('is-invalid');
            }
        });

        // Clear errors when user starts typing in a field
        $('input').on('focus', function() {
            $(this).removeClass('is-invalid');
        });

        // Form submission validation
        $('form').on('submit', function(e) {
            let hasErrors = false;

            // Email validation
            const email = $('#email').val();
            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (email && !emailRegex.test(email)) {
                hasErrors = true;
                $('#email').addClass('is-invalid');
                if (!$('#emailError').length) {
                    $('#email').after('<small class="text-danger field-error" id="emailError">Please enter a valid email address</small>');
                }
            }

            // Password validation
            const password = $('#password').val();
            if (password && (password.length < 6 || password.length > 12)) {
                hasErrors = true;
                $('#password').addClass('is-invalid');
                if (!$('#passwordError').length) {
                    $('#password').after('<small class="text-danger field-error" id="passwordError">Password must be between 6 and 12 characters</small>');
                }
            }

            // Phone validation
            const phone = $('#phone').val();
            const phoneRegex = /^[6-9][0-9]{9}$/; // Updated regex to start with 6,7,8,9
            if (phone && !phoneRegex.test(phone)) {
                hasErrors = true;
                $('#phone').addClass('is-invalid');
                if (!$('#phoneError').length) {
                    if (phone.length === 10) {
                        $('#phone').after('<small class="text-danger field-error" id="phoneError">Phone number must start with 6, 7, 8, or 9</small>');
                    } else {
                        $('#phone').after('<small class="text-danger field-error" id="phoneError">Phone number must be exactly 10 digits and start with 6, 7, 8, or 9</small>');
                    }
                }
            }

            if (hasErrors) {
                e.preventDefault();
                // Scroll to first error
                $('html, body').animate({
                    scrollTop: $('.is-invalid').first().offset().top - 100
                }, 500);
                return false;
            }
        });
    });
</script>
<style>
    .is-invalid {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }
    
    .field-error {
        display: block;
        margin-top: 5px;
        font-size: 0.875em;
    }
</style>

{{-- styling for settings --}}
<style>
        /* Sidebar Theme — matches Employee Dashboard gradient */
        .sidebar-theme {
            background: linear-gradient(180deg, #1e3c72, #2a5298);
            color: #f1f1f1;
            backdrop-filter: blur(8px);
            height: 100vh;
            border-top-right-radius: 10px;
            border-bottom-right-radius: 10px;
        }

        /* Sidebar links styling */
        .sidebar-theme .nav-link {
            color: #e0e0e0;
            font-weight: 500;
            border-radius: 8px;
            padding: 10px 15px;
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

        .sidebar-theme h4 {
            font-weight: 600;
            color: #ffffff;
        }

        /* Logout link styling */
        .sidebar-theme .nav-item.mt-3 a {
            color: #ffb3b3;
            font-weight: 500;
            transition: color 0.3s;
        }
        .sidebar-theme .nav-item.mt-3 a:hover {
            color: #ff6666;
        }

        /* Cards consistency */
        .card {
            border: none;
            border-radius: 10px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
        }
        .card-header {
            border-radius: 10px 10px 0 0 !important;
        }

        /* Edit Button with Sidebar Color Tone */
        .btn-gradient {
            background: linear-gradient(90deg, #1e3c72, #2a5298);
            border: none;
            color: white;
            font-weight: 500;
            padding: 10px 20px;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .btn-gradient:hover {
            background: linear-gradient(90deg, #2a5298, #1e3c72);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(30, 60, 114, 0.3);
        }

        /* Dashboard Stats Cards */
        .stat-card {
            height: 100%;
            border-radius: 15px;
            color: white;
            text-align: center;
            padding: 25px 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .stat-card i {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.9;
        }
        
        .stat-card h3 {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 10px 0;
        }
        
        .stat-card p {
            font-size: 1.1rem;
            margin: 0;
            opacity: 0.9;
        }
        
        .stat-card-primary {
            background: linear-gradient(135deg, #1e3c72, #2a5298);
        }
        
        .stat-card-success {
            background: linear-gradient(135deg, #28a745, #20c997);
        }
        
        .stat-card-warning {
            background: linear-gradient(135deg, #ffc107, #fd7e14);
        }
        
        .stat-card-info {
            background: linear-gradient(135deg, #17a2b8, #6f42c1);
        }
        
        .stat-card-danger {
            background: linear-gradient(135deg, #dc3545, #e83e8c);
        }

        /* Quick Action Buttons */
        .quick-action-btn {
            height: 100%;
            padding: 25px 15px;
            border-radius: 12px;
            text-align: center;
            color: white;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
        }
        
        .quick-action-btn:hover {
            transform: translateY(-5px);
            color: white;
            text-decoration: none;
            box-shadow: 0 10px 20px rgba(0,0,0,0.2);
        }
        
        .quick-action-btn i {
            font-size: 2.5rem;
            margin-bottom: 15px;
        }
        
        .quick-action-btn span {
            font-size: 1.1rem;
            font-weight: 500;
        }

        /* Recent Activity */
        .activity-item {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }
        
        .activity-item:hover {
            background-color: #f8f9fa;
            transform: translateX(5px);
        }
        
        .activity-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
        }
        
        .activity-icon-success {
            background-color: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }
        
        .activity-icon-primary {
            background-color: rgba(30, 60, 114, 0.1);
            color: #1e3c72;
        }
        
        .activity-icon-warning {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }
        
        .activity-icon-info {
            background-color: rgba(23, 162, 184, 0.1);
            color: #17a2b8;
        }

        /* Attendance Card Styling */
        .attendance-card {
            max-width: 600px;
            margin: 0 auto;
            padding: 30px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
        }

        .status {
            font-size: 1.2rem;
            margin: 20px 0;
            padding: 10px;
            border-radius: 8px;
            background: #f8f9fa;
        }

        .timer {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
        }

        .timer div {
            font-weight: bold;
            font-size: 1.1rem;
        }

        .buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-checkin {
            background: #28a745;
            color: white;
        }

        .btn-checkout {
            background: #dc3545;
            color: white;
        }

        .btn-breakin {
            background: #ffc107;
            color: black;
        }

        .btn-breakout {
            background: #17a2b8;
            color: white;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        }

        .btn:disabled {
            opacity: 0.6;
            transform: none;
            box-shadow: none;
        }

        /* Logout section styling to match sidebar theme */
       
</style>

{{-- script for add employee --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
    $(document).ready(function(){
        $('#department_id').change(function(){
            let departmentId = $(this).val();
            if(departmentId){
                $.ajax({
                    url: '/hr/get-designations/' + departmentId, // ✅ fixed
                    type: 'GET',
                    success: function(data){
                        let options = '<option value="">-- Select Designation --</option>';
                        $.each(data, function(key, value){
                            options += `<option value="${value.id}">${value.designation}</option>`;
                        });
                        $('#designation_id').html(options);
                    },
                    error: function(xhr){
                        console.log(xhr.responseText);
                    }
                });
            } else {
                $('#designation_id').html('<option value="">-- Select Designation --</option>');
            }
        });
    });
</script>

{{-- script for edit settings --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
        $(document).ready(function(){
            // Section navigation functionality
            function switchToSection(section) {
                console.log('Switching to section:', section);
                
                // Remove active class from all links
                $('.sidebar-theme .nav-link').removeClass('active bg-primary');
                
                // Add active class to clicked link
                $(`.sidebar-theme .nav-link[data-section="${section}"]`).addClass('active bg-primary');

                // Hide all sections
                $('#dashboardSection, #employeesSection, #addEmployeeSection, #editEmployeeSection, #hrattendanceSection, #attendanceSection, #leavesSection, #settingsSection').addClass('d-none');

                // Show selected section
                const targetSection = $('#' + section);
                if (targetSection.length) {
                    targetSection.removeClass('d-none');
                    console.log('Successfully showed section:', section);
                } else {
                    console.error('Section not found:', section);
                }
            }

            // Click event for sidebar links
            $('.sidebar-theme .nav-link').click(function(e){
                e.preventDefault();
                const section = $(this).data('section');
                console.log('Clicked sidebar link:', section);
                switchToSection(section);
            });

            // Click event for quick action buttons
            $('.quick-action-btn').click(function(e){
                e.preventDefault();
                const section = $(this).data('section');
                console.log('Clicked quick action:', section);
                switchToSection(section);
            });

            // Automatically open the correct section based on URL
            const params = new URLSearchParams(window.location.search);
            const tab = params.get('tab');
            if (tab) {
                console.log('URL tab parameter found:', tab);
                switchToSection(tab);
            } else {
                // Default to dashboard
                switchToSection('dashboardSection');
            }

            // Settings form functionality
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
                $('#hrDetails').addClass('d-none');
                $(this).hide();
                // Reset password fields and errors
                newPassword.val('');
                confirmPassword.val('');
                passwordMatchError.addClass('d-none');
            });

            // Cancel edit
            $('#cancelEdit').click(function(){
                $('#settingsForm').addClass('d-none');
                $('#hrDetails').removeClass('d-none');
                $('#editBtn').show();
                // Reset password fields and errors
                newPassword.val('');
                confirmPassword.val('');
                passwordMatchError.addClass('d-none');
            });

            // AJAX submit with proper validation
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
                    url: "{{ route('hr.updateSettings', $hrUser->id) }}",
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
                        $('#hrDetails').removeClass('d-none');
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


{{-- style for attendance system --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
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