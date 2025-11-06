@extends('layouts.master')
@section('title','Admin Dashboard')

@section('content')

<div class="container-fluid">
    <div class="row">

        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 p-0">
            <div class="d-flex flex-column flex-shrink-0 sidebar-theme vh-100 p-3 text-light shadow-lg">
                <div class="text-center mb-4">
                    <img id="sidebarPhoto" src="{{ asset('images/admin-avatar.png') }}" 
                         alt="Admin Photo" 
                         class="img-fluid rounded-circle border border-light mb-2" 
                         style="width:100px;height:100px;">
                    <h5 class="mb-0">{{ auth()->user()->name }}</h5>
                    <small class="text-muted">Administrator</small>
                </div>

                <hr class="text-secondary">

                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item mb-2">
                        <a href="#" class="nav-link text-light active" data-section="dashboard">
                            <i class="bi bi-speedometer2 me-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="#" class="nav-link text-light" data-section="employees">
                            <i class="bi bi-people me-2"></i> List of Employees
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="#" class="nav-link text-light" data-section="hrList">
                            <i class="bi bi-person-badge me-2"></i> List of HR
                        </a>
                    </li>
                    <li class="nav-item mb-2">
                        <a href="#" class="nav-link text-light" data-section="addHR">
                            <i class="bi bi-person-plus me-2"></i> Add HR
                        </a>
                    </li>
                    <!-- HR Leaves Section -->
                    <li class="nav-item mb-2">
                        <a href="#" class="nav-link text-light" data-section="hrLeaves">
                            <i class="bi bi-calendar-check me-2"></i> HR Leaves
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

            <!-- Dashboard Section -->
            <div id="dashboardSection" class="section-content">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card bg-primary text-white shadow border-0">
                            <div class="card-body text-center">
                                <i class="bi bi-people display-4 mb-3"></i>
                                <h5 class="card-title">Total Employees</h5>
                                <h3 class="card-text">{{ $totalEmployees }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-success text-white shadow border-0">
                            <div class="card-body text-center">
                                <i class="bi bi-person-badge display-4 mb-3"></i>
                                <h5 class="card-title">Total HR</h5>
                                <h3 class="card-text">{{ $totalHR }}</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-info text-white shadow border-0">
                            <div class="card-body text-center">
                                <i class="bi bi-building display-4 mb-3"></i>
                                <h5 class="card-title">Total Departments</h5>
                                <h3 class="card-text">{{ $totalDepartments }}</h3>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="card shadow border-0">
                    <div class="card-header text-white" style="background: linear-gradient(90deg, #1e3c72, #2a5298);">
                        <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <div class="row text-center">
                            <div class="col-md-3 mb-3">
                                <a href="#" class="btn btn-primary btn-lg p-3" data-section="addHR">
                                    <i class="bi bi-person-plus display-6 mb-2"></i><br>
                                    Add New HR
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="#" class="btn btn-success btn-lg p-3" data-section="employees">
                                    <i class="bi bi-people display-6 mb-2"></i><br>
                                    View Employees
                                </a>
                            </div>
                            <div class="col-md-3 mb-3">
                                <a href="#" class="btn btn-info btn-lg p-3" data-section="hrList">
                                    <i class="bi bi-person-badge display-6 mb-2"></i><br>
                                    Manage HR
                                </a>
                            </div>
                            {{-- <div class="col-md-3 mb-3">
                                <a href="#" class="btn btn-warning btn-lg p-3">
                                    <i class="bi bi-graph-up display-6 mb-2"></i><br>
                                    View Reports
                                </a>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Employees Section -->
            <div id="employeesSection" class="section-content d-none">
                <div class="container mt-4">
                    <h2 class="text-center mb-4">List of Employees</h2>

                    <!-- Success Message -->
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif

                    <!-- Employees Table -->
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
                                </tr>
                            </thead>
                            <tbody class="text-center">
                                @foreach($employees as $employee)
                                    <tr>
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
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        
                        <!-- Results Count -->
                        <div class="mt-3 text-muted">
                            Total: {{ $employees->count() }} employees
                        </div>
                    </div>
                </div>
            </div>

            <!-- HR List Section -->
            <div id="hrListSection" class="section-content d-none">
                <div class="card shadow border-0">
                    <div class="card-header text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(90deg, #1e3c72, #2a5298);">
                        <h5 class="mb-0">
                            <i class="bi bi-person-badge me-2"></i>
                            @if(isset($editHR))
                                Edit HR - {{ $editHR->name }}
                            @else
                                List of HR
                            @endif
                        </h5>
                        @if(isset($editHR))
                        <a href="{{ route('admin.dashboard', ['tab' => 'hrList']) }}" class="btn btn-light btn-sm">
                            <i class="bi bi-arrow-left me-1"></i> Back to List
                        </a>
                        @endif
                    </div>
                    <div class="card-body">
                        
                        @if(isset($editHR))
                            <!-- Edit HR Form -->
                            <div class="row justify-content-center">
                                <div class="col-lg-10">
                                    <div class="card shadow border-0">
                                        <div class="card-header text-white" style="background: linear-gradient(90deg, #1e3c72, #2a5298);">
                                            <h5 class="mb-0"><i class="bi bi-person-gear me-2"></i>Edit HR Details</h5>
                                        </div>
                                        <div class="card-body">
                                            <form action="{{ route('admin.hr.update', $editHR->id) }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                @method('PUT')

                                                <div class="row g-3">
                                                    <!-- First Name -->
                                                    <div class="col-md-6">
                                                        <label for="first_name" class="form-label">First Name</label>
                                                        <input type="text" class="form-control" id="first_name" name="first_name" 
                                                            value="{{ old('first_name', explode(' ', $editHR->name)[0] ?? '') }}" required>
                                                        @error('first_name') <small class="text-danger">{{ $message }}</small> @enderror
                                                    </div>

                                                    <!-- Last Name -->
                                                    <div class="col-md-6">
                                                        <label for="last_name" class="form-label">Last Name</label>
                                                        <input type="text" class="form-control" id="last_name" name="last_name" 
                                                            value="{{ old('last_name', explode(' ', $editHR->name)[1] ?? '') }}" required>
                                                        @error('last_name') <small class="text-danger">{{ $message }}</small> @enderror
                                                    </div>

                                                    <!-- Email -->
                                                    <div class="col-md-6">
                                                        <label for="email" class="form-label">Email</label>
                                                        <input type="email" class="form-control" id="email" name="email" 
                                                            value="{{ old('email', $editHR->email) }}" required>
                                                        @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                                                    </div>

                                                    <!-- Phone -->
                                                    <div class="col-md-6">
                                                        <label for="phone" class="form-label">Phone</label>
                                                        <input type="text" class="form-control" id="phone" name="phone" 
                                                            value="{{ old('phone', $editHR->phone) }}" required>
                                                        @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                                                    </div>

                                                    <!-- HR Department -->
                                                    <div class="col-md-6">
                                                        <label for="hr_department_id" class="form-label">HR Department</label>
                                                        <select name="hr_department_id" id="hr_department_id" class="form-select" required>
                                                            <option value="">Select Department</option>
                                                            @foreach($hrdepartments as $department)
                                                                <option value="{{ $department->id }}" 
                                                                    {{ $editHR->hr_department_id == $department->id ? 'selected' : '' }}>
                                                                    {{ $department->department_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                        @error('hr_department_id') <small class="text-danger">{{ $message }}</small> @enderror
                                                    </div>

                                                    <!-- Gender -->
                                                    <div class="col-md-6">
                                                        <label for="gender" class="form-label">Gender</label>
                                                        <select name="gender" id="gender" class="form-select" required>
                                                            <option value="">Select Gender</option>
                                                            <option value="Male" {{ $editHR->gender == 'Male' ? 'selected' : '' }}>Male</option>
                                                            <option value="Female" {{ $editHR->gender == 'Female' ? 'selected' : '' }}>Female</option>
                                                            <option value="Other" {{ $editHR->gender == 'Other' ? 'selected' : '' }}>Other</option>
                                                        </select>
                                                        @error('gender') <small class="text-danger">{{ $message }}</small> @enderror
                                                    </div>

                                                    <!-- Date of Birth -->
                                                    <div class="col-md-6">
                                                        <label for="date_of_birth" class="form-label">Date of Birth</label>
                                                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                                                            value="{{ old('date_of_birth', $editHR->date_of_birth ? \Carbon\Carbon::parse($editHR->date_of_birth)->format('Y-m-d') : '') }}">
                                                        @error('date_of_birth') <small class="text-danger">{{ $message }}</small> @enderror
                                                    </div>

                                                    <!-- Date of Joining -->
                                                    <div class="col-md-6">
                                                        <label for="date_of_joining" class="form-label">Date of Joining</label>
                                                        <input type="date" class="form-control" id="date_of_joining" name="date_of_joining" 
                                                            value="{{ old('date_of_joining', $editHR->date_of_joining ? \Carbon\Carbon::parse($editHR->date_of_joining)->format('Y-m-d') : '') }}">
                                                        @error('date_of_joining') <small class="text-danger">{{ $message }}</small> @enderror
                                                    </div>

                                                    <!-- Status -->
                                                    <div class="col-md-6">
                                                        <label for="status" class="form-label">Status</label>
                                                        <select name="status" id="status" class="form-select" required>
                                                            <option value="Active" {{ $editHR->status == 'Active' ? 'selected' : '' }}>Active</option>
                                                            <option value="Inactive" {{ $editHR->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                                        </select>
                                                        @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                                                    </div>

                                                    <!-- Photo -->
                                                    <div class="col-md-6">
                                                        <label for="photo" class="form-label">Profile Photo</label>
                                                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                                                        @if($editHR->photo)
                                                            <div class="mt-2">
                                                                <small class="text-muted">Current Photo:</small>
                                                                <img src="{{ asset('storage/'.$editHR->photo) }}" alt="Current Photo" class="img-thumbnail ms-2" style="width: 50px; height: 50px;">
                                                            </div>
                                                        @endif
                                                        @error('photo') <small class="text-danger">{{ $message }}</small> @enderror
                                                    </div>

                                                    <!-- Password (Optional) -->
                                                    <div class="col-12">
                                                        <label for="password" class="form-label">New Password (Leave blank to keep current)</label>
                                                        <input type="password" class="form-control" id="password" name="password" 
                                                            placeholder="Enter new password">
                                                        @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                                                    </div>

                                                    <!-- Buttons -->
                                                    <div class="col-12 text-center mt-4">
                                                        <button type="submit" class="btn btn-success px-4">
                                                            <i class="bi bi-check-circle me-2"></i>Update HR
                                                        </button>
                                                        <a href="{{ route('admin.dashboard', ['tab' => 'hrList']) }}" 
                                                        class="btn btn-secondary px-4">
                                                            <i class="bi bi-arrow-left me-2"></i>Cancel
                                                        </a>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- HR List Table (shown when not editing) -->
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead class="table-primary text-center">
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Department</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="text-center">
                                        @foreach($hrList as $hr)
                                        <tr>
                                            <td>{{ $hr->id }}</td>
                                            <td>{{ $hr->name }}</td>
                                            <td>{{ $hr->email }}</td>
                                            <td>{{ $hr->phone }}</td>
                                            <td>{{ $hr->hrDepartment->department_name ?? 'N/A' }}</td>
                                            <td>
                                                <span class="badge {{ $hr->status == 'Active' ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $hr->status }}
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.dashboard', ['tab' => 'hrList', 'hr_id' => $hr->id]) }}" 
                                                class="btn btn-primary btn-sm">
                                                    <i class="bi bi-pencil"></i>
                                                </a>

                                                <form action="{{ route('admin.hr.destroy', $hr->id) }}" 
                                                    method="POST" 
                                                    class="d-inline-block" 
                                                    onsubmit="return confirm('Are you sure you want to delete this HR?');">
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
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Add HR Section -->
            <div id="addHRSection" class="section-content d-none">
                <div class="card shadow border-0">
                    <div class="card-header text-white" style="background: linear-gradient(90deg, #1e3c72, #2a5298);">
                        <h5 class="mb-0"><i class="bi bi-person-plus me-2"></i>Add New HR</h5>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('admin.storeHR') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-3">
                                <!-- First Name -->
                                <div class="col-md-6">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" value="{{ old('first_name') }}" required>
                                    @error('first_name') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <!-- Last Name -->
                                <div class="col-md-6">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name') }}" required>
                                    @error('last_name') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" required>
                                    @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <!-- Phone -->
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Phone</label>
                                    <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" maxlength="10" required>
                                    @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <!-- Password -->
                                <div class="col-md-6">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <!-- Gender -->
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

                                <!-- Date of Birth -->
                                <div class="col-md-6">
                                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}" required>
                                    @error('date_of_birth') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <!-- HR Department -->
                                <div class="col-md-6">
                                    <label for="hr_department_id" class="form-label">HR Department</label>
                                    <select name="hr_department_id" id="hr_department_id" class="form-select" required>
                                        <option value="">-- Select HR Department --</option>
                                        @foreach($hrdepartments as $department)
                                            <option value="{{ $department->id }}" {{ old('hr_department_id') == $department->id ? 'selected' : '' }}>
                                                {{ $department->department_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('hr_department_id') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <!-- Status -->
                                <div class="col-md-6">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status" required>
                                        <option value="">-- Select Status --</option>
                                        <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <!-- Date of Joining -->
                                <div class="col-md-6">
                                    <label for="date_of_joining" class="form-label">Date of Joining</label>
                                    <input type="date" class="form-control" id="date_of_joining" name="date_of_joining" value="{{ old('date_of_joining') }}" required>
                                    @error('date_of_joining') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <!-- Photo -->
                                <div class="col-md-12">
                                    <label for="photo" class="form-label">HR Photo</label>
                                    <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                                    @error('photo') <small class="text-danger">{{ $message }}</small> @enderror
                                </div>

                                <!-- Submit -->
                                <div class="col-12 text-center mt-3">
                                    <button type="submit" class="btn btn-success btn-lg">
                                        <i class="bi bi-person-plus me-2"></i>Add HR
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- HR Leaves Section -->
            <div id="hrLeavesSection" class="section-content d-none">
                <div class="card shadow border-0">
                    <div class="card-header text-white d-flex justify-content-between align-items-center" style="background: linear-gradient(90deg, #1e3c72, #2a5298);">
                        <h5 class="mb-0">
                            <i class="bi bi-calendar-check me-2"></i>
                            HR Leave Requests
                        </h5>
                        <span class="badge bg-warning">
                            {{ $pendingHrLeavesCount ?? 0 }} Pending
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
                                        <th>HR Name</th>
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
                                    @forelse($hrLeaves as $leave)
                                    <tr id="leaveRow-{{ $leave->id }}">
                                        <td class="text-center">
                                            {{ $leave->employee->first_name }} {{ $leave->employee->last_name }}
                                            <br>
                                            <span class="badge bg-info">HR</span>
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
                                            <span class="text-muted">No HR leave requests found</span>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Results Count -->
                        @if($hrLeaves && $hrLeaves->count() > 0)
                        <div class="mt-3 text-muted">
                            Total: {{ $hrLeaves->count() }} HR leave request(s)
                        </div>
                        @endif
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

<!-- Include Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        // Section navigation functionality
        function switchToSection(section) {
            console.log('Switching to section:', section);
            
            // Remove active class from all nav links
            $('.sidebar-theme .nav-link').removeClass('active bg-primary');
            
            // Add active class to clicked nav link
            $(`.sidebar-theme .nav-link[data-section="${section}"]`).addClass('active bg-primary');

            // Hide all sections
            const sections = [
                'dashboardSection',
                'employeesSection', 
                'hrListSection',
                'addHRSection',
                'hrLeavesSection'
            ];

            // Approve HR leave
            $(document).on('click', '.approveBtn', function() {
                const leaveId = $(this).data('id');
                updateHrLeaveStatus(leaveId, 'Approved');
            });

            // Reject HR leave
            $(document).on('click', '.rejectBtn', function() {
                const leaveId = $(this).data('id');
                updateHrLeaveStatus(leaveId, 'Rejected');
            });

            function updateHrLeaveStatus(leaveId, status) {
                $.ajax({
                    url: "{{ route('admin.hrLeaves.updateStatus') }}",
                    type: "POST",
                    data: {
                        leave_id: leaveId,
                        status: status,
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.success) {
                            const row = $("#leaveRow-" + response.leave_id);
                            
                            // Update status badge
                            const badge = row.find('.status-cell span');
                            badge.text(response.new_status);
                            
                            badge.removeClass('bg-warning bg-success bg-danger');
                            if (response.new_status === 'Approved') {
                                badge.addClass('bg-success');
                            } else if (response.new_status === 'Rejected') {
                                badge.addClass('bg-danger');
                            } else {
                                badge.addClass('bg-warning');
                            }

                            // Update action buttons
                            row.find('td:last').html(
                                response.new_status === 'Approved' ? 
                                '<span class="text-muted"><i class="bi bi-check-circle text-success me-1"></i>Approved</span>' :
                                '<span class="text-muted"><i class="bi bi-x-circle text-danger me-1"></i>Rejected</span>'
                            );
                            
                            // Show success message
                            showNotification('Leave ' + response.new_status.toLowerCase() + ' successfully!', 'success');
                            
                            // Update pending count in dashboard if needed
                            updatePendingCount();
                        }
                    },
                    error: function(xhr) {
                        if (xhr.status === 403) {
                            showNotification(xhr.responseJSON.message || 'You are not authorized to perform this action!', 'error');
                        } else {
                            showNotification("Something went wrong while updating status!", 'error');
                        }
                    }
                });
            }

            function showNotification(message, type) {
                // Create a simple notification
                const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
                const alert = $(`<div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>`);
                
                $('.container-fluid').prepend(alert);
                
                // Auto remove after 3 seconds
                setTimeout(() => {
                    alert.alert('close');
                }, 3000);
            }
            
            sections.forEach(sectionId => {
                $(`#${sectionId}`).addClass('d-none');
            });

            // Show selected section
            $(`#${section}Section`).removeClass('d-none');
        }

        // Add click event listeners to sidebar links
        $('.sidebar-theme .nav-link').on('click', function(e) {
            e.preventDefault();
            const section = $(this).data('section');
            switchToSection(section);
            
            // Update URL without page reload (optional)
            const url = new URL(window.location);
            url.searchParams.set('tab', section);
            window.history.pushState({}, '', url);
        });

        // Add click event listeners to quick action buttons
        $('.btn[data-section]').on('click', function(e) {
            e.preventDefault();
            const section = $(this).data('section');
            switchToSection(section);
            
            // Update URL without page reload (optional)
            const url = new URL(window.location);
            url.searchParams.set('tab', section);
            window.history.pushState({}, '', url);
        });

        // Restore active section on page load
        function restoreActiveSection() {
            const urlParams = new URLSearchParams(window.location.search);
            const tabParam = urlParams.get('tab');
            
            // If there's a tab parameter, switch to that section
            if (tabParam) {
                switchToSection(tabParam);
            } else {
                // Default to dashboard section
                switchToSection('dashboard');
            }
        }

        // Handle browser back/forward buttons
        $(window).on('popstate', function() {
            restoreActiveSection();
        });

        // Initialize on page load
        restoreActiveSection();
    });
</script>

<style>
    .card-header {
        background: linear-gradient(90deg, #1e3c72, #2a5298) !important;
    }

    .btn-primary {
        background: linear-gradient(90deg, #1e3c72, #2a5298);
        border: none;
    }

    .btn-primary:hover {
        background: linear-gradient(90deg, #2a5298, #1e3c72);
        transform: translateY(-1px);
    }

    /* Sidebar Theme (matches employee dashboard) */
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

    /* Button styling */
    .btn-success {
        background: linear-gradient(90deg, #28a745, #20c997);
        border: none;
    }

    .btn-success:hover {
        background: linear-gradient(90deg, #20c997, #28a745);
        transform: translateY(-2px);
    }

    .btn-info {
        background: linear-gradient(90deg, #17a2b8, #6f42c1);
        border: none;
    }

    .btn-info:hover {
        background: linear-gradient(90deg, #6f42c1, #17a2b8);
        transform: translateY(-2px);
    }

    /* Card shadows and transitions */
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    /* Table styling */
    .table th {
        background: linear-gradient(90deg, #1e3c72, #2a5298);
        color: white;
        border: none;
    }

    .badge {
        font-size: 0.8em;
        padding: 0.5em 0.75em;
    }
</style>

{{-- form validation for add HR --}}
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
            const phoneRegex = /^[6-9][0-9]{9}$/;
            
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

        // Date of Birth validation - must be in the past
        $('#date_of_birth').on('change', function() {
            const dob = new Date($(this).val());
            const today = new Date();
            
            // Remove existing error message
            $('#dobError').remove();
            
            if (dob >= today) {
                // Show error message below the field
                $(this).after('<small class="text-danger field-error" id="dobError">Date of birth must be in the past</small>');
                $(this).addClass('is-invalid');
            } else {
                // Remove error message and styling
                $(this).removeClass('is-invalid');
            }
        });

        // Date of Joining validation - must be reasonable (not in distant future)
        $('#date_of_joining').on('change', function() {
            const doj = new Date($(this).val());
            const today = new Date();
            const maxFutureDate = new Date();
            maxFutureDate.setFullYear(today.getFullYear() + 1); // Max 1 year in future
            
            // Remove existing error message
            $('#dojError').remove();
            
            if (doj > maxFutureDate) {
                // Show error message below the field
                $(this).after('<small class="text-danger field-error" id="dojError">Date of joining cannot be more than 1 year in future</small>');
                $(this).addClass('is-invalid');
            } else {
                // Remove error message and styling
                $(this).removeClass('is-invalid');
            }
        });

        // Required field validation for text inputs
        $('#first_name, #last_name').on('input', function() {
            const value = $(this).val();
            const fieldId = $(this).attr('id') + 'Error';
            
            // Remove existing error message
            $('#' + fieldId).remove();
            
            if (!value.trim()) {
                // Show error message below the field
                $(this).after('<small class="text-danger field-error" id="' + fieldId + '">This field is required</small>');
                $(this).addClass('is-invalid');
            } else {
                // Remove error message and styling
                $(this).removeClass('is-invalid');
            }
        });

        // Required field validation for dropdowns
        $('#gender, #hr_department_id, #status').on('change', function() {
            const value = $(this).val();
            const fieldId = $(this).attr('id') + 'Error';
            
            // Remove existing error message
            $('#' + fieldId).remove();
            
            if (!value) {
                // Show error message below the field
                $(this).after('<small class="text-danger field-error" id="' + fieldId + '">This field is required</small>');
                $(this).addClass('is-invalid');
            } else {
                // Remove error message and styling
                $(this).removeClass('is-invalid');
            }
        });

        // Clear errors when user starts typing in a field
        $('input, select').on('focus', function() {
            $(this).removeClass('is-invalid');
            $('#' + $(this).attr('id') + 'Error').remove();
        });

        // Form submission validation
        $('form').on('submit', function(e) {
            let hasErrors = false;

            // Email validation
            const email = $('#email').val();
            const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
            if (!email || !emailRegex.test(email)) {
                hasErrors = true;
                $('#email').addClass('is-invalid');
                if (!$('#emailError').length) {
                    $('#email').after('<small class="text-danger field-error" id="emailError">Please enter a valid email address</small>');
                }
            }

            // Password validation
            const password = $('#password').val();
            if (!password || password.length < 6 || password.length > 12) {
                hasErrors = true;
                $('#password').addClass('is-invalid');
                if (!$('#passwordError').length) {
                    $('#password').after('<small class="text-danger field-error" id="passwordError">Password must be between 6 and 12 characters</small>');
                }
            }

            // Phone validation
            const phone = $('#phone').val();
            const phoneRegex = /^[0-9]{10}$/;
            if (!phone || !phoneRegex.test(phone)) {
                hasErrors = true;
                $('#phone').addClass('is-invalid');
                if (!$('#phoneError').length) {
                    $('#phone').after('<small class="text-danger field-error" id="phoneError">Phone number must be exactly 10 digits</small>');
                }
            }

            // Required fields validation
            const requiredFields = ['first_name', 'last_name', 'gender', 'hr_department_id', 'status'];
            requiredFields.forEach(field => {
                const value = $('#' + field).val();
                if (!value || !value.trim()) {
                    hasErrors = true;
                    $('#' + field).addClass('is-invalid');
                    if (!$('#' + field + 'Error').length) {
                        $('#' + field).after('<small class="text-danger field-error" id="' + field + 'Error">This field is required</small>');
                    }
                }
            });

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
    }
    
    .field-error {
        display: block;
        margin-top: 5px;
        font-size: 0.875em;
        color: #dc3545;
    }
</style>