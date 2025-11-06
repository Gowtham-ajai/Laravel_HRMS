<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\Designation;
use App\Models\Employee;
use App\Models\Register;
use App\Models\Leave;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class EmployeeController extends Controller
{
    //Summary counts
    public function dashboard(Request $request) {
        //Get the authenticated user
        $hrUser = Auth::user();

        $totalEmployees = Employee::whereHas('register', function($query){
            $query->where('role', '!=', 'HR');
        })->count();
        $activeEmployees = Employee::where('status', 'Active')
                            ->whereHas('register', function($query){
                                $query->where('role', '!=', 'HR');
                            })->count();
        $inactiveEmployees = Employee::where('status', 'Inactive')
                            ->whereHas('register', function($query){
                                $query->where('role', '!=', 'HR');
                             })->count();

        //Count only departments that employees belong to
        $departmentsCount = Employee::distinct('department_id')->count('department_id');

        //Get only NON-HR employees for the table
        $employees = Employee::with(['department', 'designation'])
            ->whereDoesntHave('department', function($query) {
                $query->where('department_name', 'like', '%HR%')
                    ->orWhere('department_name', 'like', '%Human Resource%');
            })
            ->get();
        $departments = Department::all(); // collection for dropdowns

        //Add leaves here
        $leaves = Leave::with('employee')
                        ->whereHas('employee.register', function($query) {
                            $query->where('role', '!=', 'HR');
                        })->latest()->get();
        $attendances = Attendance::with('employee')->latest()->get(); // if needed

        //ADD ATTENDANCE SUMMARY VARIABLES
        $presentToday = Attendance::whereDate('date', today())
            ->where('attendance_status', 'Present')
            ->count();
    
        $absentToday = Attendance::whereDate('date', today())
            ->where('attendance_status', 'Absent')
            ->count();

        //Get HR's employee record
        $hrEmployee = Employee::where('registers_id', $hrUser->id)->first();
        
        //Get HR's leaves for the apply leave section
        $hrLeaves = $hrEmployee ? $hrEmployee->leaves()->latest()->get() : collect();

        $editEmployee = null;
        $designations = [];

        if ($request->has('employee_id')) {
                $editEmployee = Employee::with(['department', 'designation'])->find($request->employee_id);
                $designations = Designation::all();
            }

        return view('hr_partials.hr_dashboard', compact([
            'hrUser', 'totalEmployees', 'activeEmployees', 'inactiveEmployees', 'departmentsCount',
            'employees', 'departments', 'leaves', 'attendances', 'editEmployee', 'designations', 
            'presentToday', 'absentToday', 'hrEmployee', 'hrLeaves'
        ]))->with('tab', $request->tab ?? 'dashboardSection');
    }

    //Display all employees in a table
    public function index(Request $request){
        $employees = Employee::with(['department', 'designation'])->get();
        $departments = Department::all(); // For department dropdown
        return view('hr_partials.hr_dashboard', compact('employees', 'departments'));
    }

    //Show form to add new employee
    public function create(){
        $departments = Department::all();

        return view('hr_partials.hr_dashboard', compact('departments'));
    }

    //Store new employee
    public function store(Request $request){
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email:rfc,dns|unique:employees,email',
            'phone'      => 'required|string|size:10|unique:employees,phone',
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
            'status'     => 'required|in:Active,Inactive',
            'date_of_joining' => 'required|date',
            'password'   => 'required|string|min:6|max:12', // HR sets password
            'gender'     => 'required|string|in:Male,Female,Other',
            'date_of_birth' => 'required|date|before:today',
            'photo'      => 'nullable|image|mimes:jpg,jpeg,png|max:2048'
        ]);

         // Upload photo if provided
        $photoPath = null;
        if($request->hasFile('photo')){
            $photoPath = $request->file('photo')->store('employees', 'public');
        }

        // Calculate age before saving
        $age = Carbon::parse($request->date_of_birth)->age;

        // Create Register entry for employee
        $register = Register::create([
            'name' => $request->first_name.' '.$request->last_name,
            'email'=> $request->email,
            'phone'=> $request->phone,
            'password'=> Hash::make($request->password),
            'role' => 'Employee',
        ]);

        // Create Employee entry
        Employee::create([
            'registers_id' => $register->id,
            'first_name'   => $request->first_name,
            'last_name'    => $request->last_name,
            'email'        => $request->email,
            'phone'        => $request->phone,
            'department_id' => $request->department_id,
            'designation_id' => $request->designation_id,
            'status'       => $request->status,
            'date_of_joining' => $request->date_of_joining,
            'gender'       => $request->gender,
            'date_of_birth'=> $request->date_of_birth,
            'age'          => $age, 
            'photo'        => $photoPath,
        ]);

        return redirect()->route('hr.dashboard', ['tab' => 'employeesSection'])
                 ->with('success', 'Employee added successfully!');
    }
    //Show edit form
    public function edit($id){
        $employee = Employee::findOrFail($id);
        $departments = Department::all();
        $designations = Designation::all();

            // Load HR dashboard with the edit form section open
        return view('hr_dashboard', compact('employee', 'departments', 'designations'))
            ->with('tab', 'editEmployeeSection');
    }

    //Update employee record
    public function update(Request $request, $id){
        $employee = Employee::findOrFail($id);

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email'      => 'required|email:rfc,dns|unique:employees,email,' . $id,
            'phone'      => 'required|string|size:10|unique:employees,phone,' . $id,
            'department_id' => 'required|exists:departments,id',
            'designation_id' => 'required|exists:designations,id',
            'status'     => 'required|in:Active,Inactive',
            'date_of_joining' => 'required|date',
        ]);

        // Recalculate age if DOB changed
        $age = Carbon::parse($request->date_of_birth)->age;

         // Handle photo update
        $photoPath = $employee->photo;
        if($request->hasFile('photo')){
            $photoPath = $request->file('photo')->store('employees', 'public');
        }

        $employee->update([
            'first_name' => $request->first_name,
            'last_name'  => $request->last_name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'department_id' => $request->department_id,
            'designation_id' => $request->designation_id,
            'status'     => $request->status,
            'date_of_joining' => $request->date_of_joining,
            'gender'     => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'age'        => $age, //update age
            'photo'      => $photoPath,
        ]);

         // Redirect back to dashboard and show Employees List section
        return redirect()->route('hr.dashboard', ['tab' => 'employeesSection'])
                     ->with('success', 'Employee updated successfully!');
    }

    //Delete employee
    public function destroy($id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();

        return redirect()->route('hr.dashboard', ['tab' => 'employeesSection'])
                 ->with('success', 'Employee deleted successfully!');
    }

    //Fetch designations by department for AJAX
    public function getDesignations($department_id)
    {
        $designations = Designation::where('department_id', $department_id)->get();
        return response()->json($designations);
    }

    //Employee settings
    public function updateSettings(Request $request, $id)
    {
        try {
            Log::info('Update settings started', ['employee_id' => $id]);
            
            $employee = Employee::findOrFail($id);
            $register = Register::find($employee->registers_id);

            if (!$register) {
                Log::error('Register not found for employee', ['employee_id' => $id]);
                return response()->json([
                    'error' => 'Associated user account not found.'
                ], 404);
            }

            // Basic validation without complex rules first
            $request->validate([
                'phone' => 'required|string|size:10',
                'new_password' => 'nullable|string|min:6',
                'photo' => 'nullable|image|mimes:jpg,png,jpeg,gif|max:2048',
            ]);

            Log::info('Validation passed', ['phone' => $request->phone]);

            // Update phone
            $employee->update(['phone' => $request->phone]);
            $register->update(['phone' => $request->phone]);

            Log::info('Phone updated');

            // Update password if provided
            if ($request->filled('new_password')) {
                // Simple password update without same password check for now
                $register->update([
                    'password' => Hash::make($request->new_password)
                ]);
                Log::info('Password updated');
            }

            // Handle photo
            $photoUrl = null;
            if ($request->hasFile('photo')) {
                $photoPath = $request->file('photo')->store('employees', 'public');
                $employee->update(['photo' => $photoPath]);
                $photoUrl = asset('storage/'.$photoPath);
                Log::info('Photo updated', ['path' => $photoPath]);
            } else {
                $photoUrl = $employee->photo ? asset('storage/'.$employee->photo) : null;
            }

            Log::info('Settings update completed successfully');

            return response()->json([
                'message' => 'Settings updated successfully!',
                'photo' => $photoUrl,
                'phone' => $request->phone
            ]);

        } catch (\Exception $e) {
            Log::error('Settings update error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return response()->json([
                'error' => 'Something went wrong: ' . $e->getMessage()
            ], 500);
        }
    }

    //HR settings
    public function hrUpdateSettings(Request $request, $id)
    {
        try {
            $hrUser = Register::findOrFail($id);
            
            // Validate the current user can only update their own settings - FIXED LINE
            if ($hrUser->id !== Auth::id()) {
                return response()->json([
                    'error' => 'Unauthorized action.'
                ], 403);
            }

            $request->validate([
                'phone' => 'required|string|size:10|unique:registers,phone,' . $id,
                'new_password' => 'nullable|string|min:6|max:12',
                'confirm_password' => 'nullable|string|min:6|max:12|same:new_password',
                'photo' => 'nullable|image|mimes:jpg,png,jpeg,gif|max:2048',
            ]);

            // Update phone
            $hrUser->update(['phone' => $request->phone]);

            // Handle password update
            if ($request->filled('new_password')) {
                // Check if new password is same as old password
                if (Hash::check($request->new_password, $hrUser->password)) {
                    return response()->json([
                        'errors' => [
                            'new_password' => ['New password cannot be the same as your current password.']
                        ]
                    ], 422);
                }
                
                // Update password
                $hrUser->update([
                    'password' => Hash::make($request->new_password)
                ]);
            }

            // Handle photo update
            $photoUrl = null;
            if ($request->hasFile('photo')) {
                // Delete old photo if exists
                if ($hrUser->photo && Storage::disk('public')->exists($hrUser->photo)) {
                    Storage::disk('public')->delete($hrUser->photo);
                }
                
                $photoPath = $request->file('photo')->store('hr_photos', 'public');
                $hrUser->update(['photo' => $photoPath]);
                $photoUrl = asset('storage/'.$photoPath);
            } else {
                $photoUrl = $hrUser->photo ? asset('storage/'.$hrUser->photo) : null;
            }

            return response()->json([
                'message' => 'Settings updated successfully!',
                'photo' => $photoUrl,
                'phone' => $request->phone
            ]);

        } catch (\Exception $e) {
            Log::error('HR Settings update error: ' . $e->getMessage());
            return response()->json([
                'error' => 'Something went wrong. Please try again.'
            ], 500);
        }
    }

}
