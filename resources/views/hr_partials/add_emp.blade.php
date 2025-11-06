
<div class="container mt-5">
    <h2 class="text-center mb-4">Add New Employee</h2>

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
                    <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}">
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
