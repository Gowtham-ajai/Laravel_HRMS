<div class="container mt-5">
    <h2 class="text-center mb-4">List of Employees</h2>

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
    <div id="employeesSection" class="d-none">
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
                <div class="mt-3 text-muted text-center">
                    Total: {{ $employees->count() }} employees
                </div>
            </div>
        </div>
    </div>
</div>

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
        const employeeRows = document.querySelectorAll('.employee-row');
        const resultsCount = document.getElementById('resultsCount');

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
                const departmentName = row.getAttribute('data-department-name').toLowerCase();
                const designation = row.getAttribute('data-designation');
                const status = row.getAttribute('data-status');

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
            resultsCount.textContent = `Showing: ${visibleCount} of ${employeeRows.length} employees`;
            
            // Show message if no results
            if (visibleCount === 0) {
                resultsCount.innerHTML = `No employees found matching your criteria. <button type="button" id="resetBtn2" class="btn btn-sm btn-outline-primary ms-2">Show All</button>`;
                
                // Add event listener to the new reset button
                document.getElementById('resetBtn2')?.addEventListener('click', resetFilters);
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
            resultsCount.textContent = `Total: ${employeeRows.length} employees`;
        }

        // Event listeners
        searchBtn.addEventListener('click', filterEmployees);
        resetBtn.addEventListener('click', resetFilters);

        // Real-time filtering as user types (optional)
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