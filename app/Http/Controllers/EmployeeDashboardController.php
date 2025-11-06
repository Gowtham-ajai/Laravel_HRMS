<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Employee;
use App\Models\Attendance;
use Carbon\Carbon;

class EmployeeDashboardController extends Controller
{
     // Employee dashboard
    public function index()
    {
       // Get employee by registers_id
        $employee = Employee::where('registers_id', Auth::id())->first();

        if (!$employee) {
            // Handle case where employee doesn't exist
            abort(404, 'Employee profile not found');
        }

        $leaves = $employee->leaves()->latest()->get();

        // Get today's attendance (don't create if doesn't exist)
        $today = now()->toDateString();
        $todayAttendance = Attendance::where('employee_id', $employee->id)
                                   ->whereDate('date', $today)
                                   ->first();

        return view('emp_dashboard', compact('employee', 'leaves', 'todayAttendance'));
    }


    public function applyLeave(Request $request)
    {
        $employee = Employee::where('registers_id', Auth::id())->firstOrFail();

        $validated = $request->validate([
            'leave_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'remarks' => 'nullable|string'
        ]);

        $leave = $employee->leaves()->create([
            'leave_type' => $validated['leave_type'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'remarks' => $validated['remarks'],
            'status' => 'Pending'
        ]);

        // Return JSON for AJAX
        return response()->json([
            'success' => true,
            'message' => 'Leave request submitted successfully!',
            'leave' => $leave
        ]);
    }
}
