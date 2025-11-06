<div class="container mt-4">
    <h3 class="mb-4">Overall Employees Attendance</h3>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title"><i class="bi bi-people"></i>Total Employees</h5>
                    <h3 class="card-text">{{ $employees->count() }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title bi bi-clock-history">Present Today</h5>
                    <h3 class="card-text">{{ $presentToday }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5 class="card-title bi bi-person-dash">Absent Today</h5>
                    <h3 class="card-text">{{ $absentToday }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Search and Filter Section -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-search"></i></span>
                        <input type="text" id="attendanceSearch" class="form-control" placeholder="Search by employee name, department, designation, or status...">
                        <button class="btn btn-outline-secondary" type="button" id="clearSearch">Clear</button>
                    </div>
                </div>
                <div class="col-md-4">
                    <select id="statusFilter" class="form-select">
                        <option value="">All Status</option>
                        <option value="Present">Present</option>
                        <option value="Checked In">Checked In</option>
                        <option value="On Break">On Break</option>
                        <option value="Absent">Absent</option>
                        <option value="Not Checked In">Not Checked In</option>
                    </select>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <select id="departmentFilter" class="form-select">
                        <option value="">All Departments</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->department_name }}">{{ $department->department_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <select id="designationFilter" class="form-select">
                        <option value="">All Designations</option>
                        @foreach($designations as $designation)
                            <option value="{{ $designation->designation }}">{{ $designation->designation }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Results Count -->
    <div class="mb-3">
        <span id="resultsCount" class="text-muted">Showing {{ $employees->count() }} employees</span>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="table-primary text-center">
            <tr>
                <th>ID</th>
                <th>Employee</th>
                <th>Department</th>
                <th>Designation</th>
                <th>Total Present</th>
                <th>Total Absent</th>
                <th>Today's Status</th>
            </tr>
        </thead>
        <tbody class="text-center" id="attendanceTableBody">
            @foreach($employees as $employee)
            @php
                $totalPresent = $employee->total_present;
                $totalAbsent = $employee->total_absent;
                $todayStatus = $employee->getTodayAttendanceStatus();
            @endphp
            <tr class="attendance-row" 
                data-name="{{ strtolower($employee->first_name . ' ' . $employee->last_name) }}"
                data-department="{{ strtolower($employee->department->department_name ?? '') }}"
                data-designation="{{ strtolower($employee->designation->designation ?? '') }}"
                data-status="{{ $todayStatus }}">
                <td>{{ $loop->iteration }}</td>
                <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
                <td>{{ $employee->department->department_name ?? 'N/A' }}</td>
                <td>{{ $employee->designation->designation ?? 'N/A' }}</td>
                <td>
                    <span class="badge bg-success">{{ $totalPresent }}</span>
                </td>
                <td>
                    <span class="badge bg-danger">{{ $totalAbsent }}</span>
                </td>
                <td>
                    @if($todayStatus === 'Present')
                        <span class="badge bg-success">Present</span>
                    @elseif($todayStatus === 'Checked In')
                        <span class="badge bg-primary">Checked In</span>
                    @elseif($todayStatus === 'On Break')
                        <span class="badge bg-warning">On Break</span>
                    @elseif($todayStatus === 'Absent')
                        <span class="badge bg-danger">Absent</span>
                    @else
                        <span class="badge bg-secondary">Not Checked In</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- No Results Message -->
    <div id="noResults" class="alert alert-warning text-center d-none">
        <i class="bi bi-exclamation-triangle"></i> No employees found matching your search criteria.
    </div>
</div>

<script>
    $(document).ready(function() {
        // Search functionality
        $('#attendanceSearch').on('input', function() {
            filterAttendanceTable();
        });

        // Filter functionality
        $('#statusFilter, #departmentFilter, #designationFilter').on('change', function() {
            filterAttendanceTable();
        });

        // Clear search
        $('#clearSearch').on('click', function() {
            $('#attendanceSearch').val('');
            $('#statusFilter').val('');
            $('#departmentFilter').val('');
            $('#designationFilter').val('');
            filterAttendanceTable();
        });

        function filterAttendanceTable() {
            const searchTerm = $('#attendanceSearch').val().toLowerCase().trim();
            const statusFilter = $('#statusFilter').val();
            const departmentFilter = $('#departmentFilter').val().toLowerCase();
            const designationFilter = $('#designationFilter').val().toLowerCase();

            let visibleCount = 0;
            const totalRows = $('.attendance-row').length;

            $('.attendance-row').each(function() {
                const name = $(this).data('name');
                const department = $(this).data('department');
                const designation = $(this).data('designation');
                const status = $(this).data('status');

                let matchesSearch = true;
                let matchesStatus = true;
                let matchesDepartment = true;
                let matchesDesignation = true;

                // Search term matching
                if (searchTerm) {
                    matchesSearch = name.includes(searchTerm) || 
                                  department.includes(searchTerm) || 
                                  designation.includes(searchTerm) ||
                                  status.toLowerCase().includes(searchTerm);
                }

                // Status filter
                if (statusFilter) {
                    matchesStatus = status === statusFilter;
                }

                // Department filter
                if (departmentFilter) {
                    matchesDepartment = department.includes(departmentFilter);
                }

                // Designation filter
                if (designationFilter) {
                    matchesDesignation = designation.includes(designationFilter);
                }

                // Show/hide row based on all filters
                if (matchesSearch && matchesStatus && matchesDepartment && matchesDesignation) {
                    $(this).show();
                    visibleCount++;
                } else {
                    $(this).hide();
                }
            });

            // Update results count
            $('#resultsCount').text(`Showing ${visibleCount} of ${totalRows} employees`);

            // Show/hide no results message
            if (visibleCount === 0) {
                $('#noResults').removeClass('d-none');
            } else {
                $('#noResults').addClass('d-none');
            }

            // Update row numbers for visible rows only
            updateRowNumbers();
        }

        function updateRowNumbers() {
            let counter = 1;
            $('.attendance-row:visible').each(function() {
                $(this).find('td:first').text(counter);
                counter++;
            });
        }

        // Initialize filters
        filterAttendanceTable();
    });
</script>

<style>
    .attendance-row {
        transition: all 0.3s ease;
    }
    
    #attendanceSearch:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    
    .form-select:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
</style>