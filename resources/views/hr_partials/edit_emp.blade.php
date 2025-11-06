<!-- In your edit_emp.blade.php file -->
<div class="container mt-5">
    <h2 class="text-center mb-4"><i class="bi bi-person-gear"></i> Edit Employee</h2>

    <!-- Success / Error Messages -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card p-4 shadow-sm">
        <form action="{{ route('employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="row g-3">

                <div class="col-md-6">
                    <label for="edit_first_name" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="edit_first_name" name="first_name" value="{{ old('first_name', $employee->first_name) }}">
                    @error('first_name') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-6">
                    <label for="edit_last_name" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="edit_last_name" name="last_name" value="{{ old('last_name', $employee->last_name) }}">
                    @error('last_name') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-6">
                    <label for="edit_email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="edit_email" name="email" value="{{ old('email', $employee->email) }}">
                    @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-6">
                    <label for="edit_phone" class="form-label">Phone</label>
                    <input type="text" class="form-control" id="edit_phone" name="phone" value="{{ old('phone', $employee->phone) }}">
                    @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-6">
                    <label for="edit_gender" class="form-label">Gender</label>
                    <select class="form-select" id="edit_gender" name="gender" required>
                        <option value="">-- Select Gender --</option>
                        <option value="Male" {{ old('gender', $employee->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender', $employee->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('gender', $employee->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('gender') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-6">
                    <label for="edit_date_of_birth" class="form-label">Date of Birth</label>
                    <input type="date" class="form-control" id="edit_date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $employee->date_of_birth) }}">
                    @error('date_of_birth') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-6">
                    <label for="edit_photo" class="form-label">Employee Photo</label>
                    <input type="file" class="form-control" id="edit_photo" name="photo" accept="image/*">
                    @if($employee->photo)
                        <div class="mt-2">
                            <img src="{{ asset('storage/'.$employee->photo) }}" alt="Current Photo" class="img-thumbnail" style="width: 100px;">
                        </div>
                    @endif
                    @error('photo') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-6">
                    <label>Department</label>
                    <select name="department_id" id="edit_department_id" class="form-select">
                        <option value="">-- Select Department --</option>
                        @foreach($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id', $employee->department_id) == $department->id ? 'selected' : '' }}>
                                {{ $department->department_name }}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-6">
                    <label>Designation</label>
                    <select name="designation_id" id="edit_designation_id" class="form-select">
                        <option value="">-- Select Designation --</option>
                        @foreach($designations as $designation)
                            <option value="{{ $designation->id }}" {{ old('designation_id', $employee->designation_id) == $designation->id ? 'selected' : '' }}>
                                {{ $designation->designation }}
                            </option>
                        @endforeach
                    </select>
                    @error('designation_id') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-6">
                    <label for="edit_status" class="form-label">Status</label>
                    <select name="status" id="edit_status" class="form-select">
                        <option value="Active" {{ old('status', $employee->status) == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ old('status', $employee->status) == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-md-6">
                    <label for="edit_date_of_joining" class="form-label">Date of Joining</label>
                    <input type="date" class="form-control" id="edit_date_of_joining" name="date_of_joining" value="{{ old('date_of_joining', $employee->date_of_joining) }}">
                    @error('date_of_joining') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="col-12 text-center mt-3">
                    <button type="submit" class="btn btn-primary">Update Employee</button>
                    <a href="{{ route('hr.dashboard', ['tab' => 'employeesSection']) }}" class="btn btn-secondary">Back to List</a>
                </div>

            </div>
        </form>
    </div>
</div>