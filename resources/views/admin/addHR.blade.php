@extends('layouts.master')

@section('title', 'Add New HR')

@section('content')
<div class="container mt-5">
    <h2 class="text-center mb-4">Add New HR</h2>

    <!-- Success / Error Messages -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="card p-4 shadow-sm">
        <form action="{{ route('admin.storeHR') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="row g-3">

                <!-- First Name -->
                <div class="col-md-6">
                    <label for="first_name" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="first_name" name="first_name" value="{{ old('first_name') }}">
                    @error('first_name') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <!-- Last Name -->
                <div class="col-md-6">
                    <label for="last_name" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name') }}">
                    @error('last_name') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <!-- Email -->
                <div class="col-md-6">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}">
                    @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <!-- Phone -->
                <div class="col-md-6">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" maxlength="10">
                    @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <!-- Password -->
                <div class="col-md-6">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password">
                    @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <!-- Gender -->
                <div class="col-md-6">
                    <label for="gender" class="form-label">Gender</label>
                    <select class="form-select" id="gender" name="gender">
                        <option value="">-- Select Gender --</option>
                        <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                        <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                        <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                    @error('gender') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <!-- DOB -->
                <div class="col-md-6">
                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth') }}">
                    @error('date_of_birth') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <!-- Photo -->
                <div class="col-md-6">
                    <label for="photo" class="form-label">HR Photo</label>
                    <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                    @error('photo') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <!-- Department -->
                <div class="col-md-6">
                    <label>Department</label>
                    <select name="department_id" id="department_id" class="form-select">
                        <option value="">-- Select Department --</option>
                        @foreach($hrdepartments as $department)
                            <option value="{{ $department->id }}">{{ $department->department_name }}</option>
                        @endforeach
                    </select>
                    @error('department_id') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <!-- Designation -->
                <div class="col-md-6">
                    <label>Designation</label>
                    <select name="designation_id" id="designation_id" class="form-select">
                        <option value="">-- Select Designation --</option>
                    </select>
                    @error('designation_id') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <!-- Status -->
                <div class="col-md-6">
                    <label for="status" class="form-label">Status</label>
                    <select name="status" id="status" class="form-select">
                        <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Inactive" {{ old('status') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <!-- Joining Date -->
                <div class="col-md-6">
                    <label for="date_of_joining" class="form-label">Date of Joining</label>
                    <input type="date" class="form-control" id="date_of_joining" name="date_of_joining" value="{{ old('date_of_joining') }}">
                    @error('date_of_joining') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <!-- Submit -->
                <div class="col-12 text-center mt-3">
                    <button type="submit" class="btn btn-success">Add HR</button>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">Back</a>
                </div>

            </div>
        </form>
    </div>
</div>

<!-- Script for dynamic designation loading -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script>
$(document).ready(function(){
    $('#department_id').change(function(){
        let departmentId = $(this).val();
        if(departmentId){
            $.ajax({
                url: '/admin/get-designations/' + departmentId,
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
@endsection
