<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Register;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\Attendance;
use App\Models\HRDepartment;
use App\Models\Leave;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    // Admin Dashboard
    public function dashboard(Request $request)
     {
        // Add any admin-specific data here
        $totalHR = Register::where('role', 'HR')->count();

        $totalEmployees = Employee::whereHas('register', function($query){
            $query->where('role', '!=', 'HR');
        })->count();

        $totalDepartments = HRDepartment::count();
        $employees = Employee::with(['department', 'designation'])
                             ->whereHas('register', function($query){
                                $query->where('role', '!=', 'HR');
                             })->get(); // For the table
                             
        $hrList = Register::where('role', 'HR')->get(); // For HR list
        $hrdepartments = HRDepartment::all(); // For add HR form
        $presentToday = Attendance::whereDate('date', today())
            ->where('attendance_status', 'Present')
            ->count();
        
        // Handle edit HR
        $editHR = null;
        if ($request->has('hr_id')) {
            $editHR = Register::find($request->hr_id);
        }

        // Get HR leaves (leaves where the employee is HR)
        $hrLeaves = Leave::whereHas('employee.register', function($query) {
            $query->where('role', 'hr');
        })->with('employee')->latest()->get();
        
        $pendingHrLeavesCount = Leave::whereHas('employee.register', function($query) {
            $query->where('role', 'hr');
        })->where('status', 'Pending')->count();
        
        return view('admin.dashboard',
                    compact('totalHR', 'totalEmployees', 'totalDepartments', 'employees', 
                            'hrList', 'hrdepartments', 'presentToday', 'editHR', 'hrLeaves', 'pendingHrLeavesCount'));
    }

    // Show Add HR form
    public function createHR()
     {
        $hrdepartments = HRDepartment::all();
        $designations = Designation::all(); 
        return view('admin.dashboard', compact('hrdepartments', 'designations'));
    }

    // Store new HR
    public function storeHR(Request $request)
    {
        $request->validate([
            'first_name'     => 'required|string|max:255',
            'last_name'      => 'required|string|max:255',
            'email'          => 'required|email|unique:registers,email',
            'phone'          => 'required|string|min:10|max:10|unique:registers,phone',
            'password'       => 'required|string|min:6|max:12',
            'hr_department_id' => 'required|exists:hrdepartments,id',
            'gender'         => 'required',
            'date_of_birth'  => 'required|date',
            'photo'          => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'status'         => 'required|in:Active,Inactive',
            'date_of_joining'=> 'required|date',
        ]);

        // Handle Photo Upload
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('hr_photos', 'public');
        }

        // Calculate age from date of birth
        $age = \Carbon\Carbon::parse($request->date_of_birth)->age;

        // Find or create HR department in departments table
        $hrDepartment = \App\Models\Department::where('department_name', 'like', '%HR%')->first();
        if (!$hrDepartment) {
            $hrDepartment = \App\Models\Department::create([
                'name' => 'HR Department',
                'status' => 'Active'
            ]);
        }

        // Find or create HR designation
        $hrDesignation = Designation::where('designation', 'like', '%HR Manager%')->first();
        if (!$hrDesignation) {
            $hrDesignation = Designation::create([
                'name' => 'HR Manager',
                'department_id' => $hrDepartment->id,
                'description' => 'Human Resources Manager'
            ]);
        }

        // Store HR in registers table
        $registerUser = Register::create([
            'name'     => $request->first_name . ' ' . $request->last_name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
            'role'     => 'HR',
            'hr_department_id'  => $request->hr_department_id, // From hrdepartments table
            'gender'            => $request->gender,
            'date_of_birth'     => $request->date_of_birth,
            'photo'             => $photoPath,
            'status'            => $request->status,
            'date_of_joining'   => $request->date_of_joining,
        ]);

        // CREATE EMPLOYEE RECORD FOR HR
        Employee::create([
            'registers_id'    => $registerUser->id,
            'first_name'      => $request->first_name,
            'last_name'       => $request->last_name,
            'email'           => $request->email,
            'phone'           => $request->phone,
            'photo'           => $photoPath,
            'gender'          => $request->gender,
            'date_of_birth'   => $request->date_of_birth,
            'age'             => $age,
            'department_id'   => $hrDepartment->id, // From departments table
            'designation_id'  => $hrDesignation->id, // HR designation
            'status'          => $request->status,
            'date_of_joining' => $request->date_of_joining,
        ]);

        return redirect()->route('admin.dashboard')->with('success', 'New HR added successfully!');
    }

    // Edit HR
    public function editHR($id){
        $editHR = Register::findOrFail($id);
        $hrdepartments = HRDepartment::all();

        return view('admin.dashboard', compact('editHR', 'hrdepartments'))
                    ->with('tab', 'editHRSection');
    }

    // Update HR
    public function updateHR(Request $request, $id)
{
    $hr = Register::findOrFail($id);

    $request->validate([
        'first_name' => 'required|string|max:255',
        'last_name' => 'required|string|max:255',
        'email' => 'required|email|unique:registers,email,' . $id,
        'phone' => 'required|string|size:10|unique:registers,phone,' . $id,
        'hr_department_id' => 'required|exists:hrdepartments,id',
        'gender' => 'required|string|in:Male,Female,Other',
        'date_of_birth' => 'required|date',
        'date_of_joining' => 'required|date',
        'status' => 'required|in:Active,Inactive',
        'password' => 'nullable|string|min:6|max:12',
        'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
    ]);

    // Calculate age
    $age = \Carbon\Carbon::parse($request->date_of_birth)->age;

    // Find HR department in departments table and HR designation
    $hrDepartment = \App\Models\Department::where('department_name', 'like', '%HR%')->first();
    $hrDesignation = Designation::where('designation', 'like', '%HR Manager%')->first();

    // Create them if they don't exist (for safety)
    if (!$hrDepartment) {
        $hrDepartment = \App\Models\Department::create([
            'name' => 'HR Department',
            'status' => 'Active'
        ]);
    }
    if (!$hrDesignation) {
        $hrDesignation = Designation::create([
            'name' => 'HR Manager',
            'department_id' => $hrDepartment->id,
            'description' => 'Human Resources Manager'
        ]);
    }

    // Prepare data for Register
    $data = [
        'name' => $request->first_name . ' ' . $request->last_name,
        'email' => $request->email,
        'phone' => $request->phone,
        'hr_department_id' => $request->hr_department_id,
        'gender' => $request->gender,
        'date_of_birth' => $request->date_of_birth,
        'date_of_joining' => $request->date_of_joining,
        'status' => $request->status,
    ];

    // Handle password update
    if ($request->filled('password')) {
        $data['password'] = Hash::make($request->password);
    }

    // Handle photo upload
    if ($request->hasFile('photo')) {
        // Delete old photo if exists
        if ($hr->photo && Storage::disk('public')->exists($hr->photo)) {
            Storage::disk('public')->delete($hr->photo);
        }
        $data['photo'] = $request->file('photo')->store('hr_photos', 'public');
    }

    $hr->update($data);

    // UPDATE EMPLOYEE RECORD (NEW CODE)
    $employee = Employee::where('registers_id', $id)->first();
    if ($employee) {
        $employeeData = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'age' => $age,
            'department_id' => $hrDepartment->id,
            'designation_id' => $hrDesignation->id,
            'status' => $request->status,
            'date_of_joining' => $request->date_of_joining,
        ];

        if ($request->hasFile('photo')) {
            $employeeData['photo'] = $data['photo'];
        }

        $employee->update($employeeData);
    } else {
        // Create employee record if it doesn't exist (safety fallback)
        Employee::create([
            'registers_id' => $id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'photo' => $data['photo'] ?? $hr->photo,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'age' => $age,
            'department_id' => $hrDepartment->id,
            'designation_id' => $hrDesignation->id,
            'status' => $request->status,
            'date_of_joining' => $request->date_of_joining,
        ]);
    }

    return redirect()->route('admin.dashboard', ['tab' => 'hrList'])
            ->with('success', 'HR updated successfully!');
}

    // Delete HR
    public function destroyHR($id){
        $hr = Register::findOrFail($id);

         // Delete photo if exists
        if ($hr->photo && Storage::disk('public')->exists($hr->photo)) {
            Storage::disk('public')->delete($hr->photo);
        }

        $hr->delete();

        return redirect()->route('admin.dashboard')
            ->with('success', 'HR deleted successfully!');
    }

    // Fetch designations based on department (AJAX)
    public function getDesignations($departmentId)
     {
        $designations = Designation::where('department_id', $departmentId)->get();
        return response()->json($designations);
    }

    // Update HR leave status (Approve/Reject)
    public function updateHrLeaveStatus(Request $request)
    {
        $request->validate([
            'leave_id' => 'required|integer|exists:leaves,id',
            'status' => 'required|string|in:Approved,Rejected'
        ]);

        $leave = Leave::with(['employee.register'])->findOrFail($request->leave_id);
        
        // Authorization check - ensure this is an HR leave and user is admin
        if ($leave->employee->register->role !== 'HR') {
            return response()->json([
                'success' => false,
                'message' => 'You can only process HR leaves.'
            ], 403);
        }

        // Update leave status
        $leave->status = $request->status;
        $leave->save();

        return response()->json([
            'success' => true,
            'message' => 'HR leave status updated successfully!',
            'leave_id' => $leave->id,
            'new_status' => $leave->status
        ]);
    }

    // Get HR leaves for AJAX requests (optional)
    public function getHrLeaves()
    {
        $hrLeaves = Leave::whereHas('employee.register', function($query) {
            $query->where('role', 'HR');
        })->with(['employee', 'employee.register'])->latest()->get();

        return response()->json([
            'success' => true,
            'hrLeaves' => $hrLeaves
        ]);
    }


}
