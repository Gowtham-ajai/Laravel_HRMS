<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;


class AttendanceController extends Controller
{

    // HR Dashboard - Overall Employees Attendance
    public function index()
    {
        $employees = Employee::with(['department', 'designation', 'attendances'])->get();

        // Calculate today's summary based on automatic status
        $presentToday = 0;
        $absentToday = 0;
        
        foreach ($employees as $employee) {
            $status = $employee->getTodayAttendanceStatus();
            if ($status === 'Present') {
                $presentToday++;
            } elseif ($status === 'Absent') {
                $absentToday++;
            }
        }
  
        return view('attendances.index', compact(
            'employees', 
            'presentToday', 
            'absentToday'
        ));
    }
 
    // Mark attendance for a single employee
    public function mark($id)
    {
        $employee = Employee::findOrFail($id);
        return view('attendances.mark', compact('employee'));
    }

    // Store attendance manually (Admin use)
    public function storeMark(Request $request, $id)
    {
        $request->validate([
            'date' => 'required|date',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',
        ]);

        Attendance::create([
            'employee_id' => $id,
            'date' => $request->date,
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
        ]);

        return redirect()->route('attendances.index')->with('success', 'Attendance recorded successfully.');
    }

     // Employee Dashboard (show emp_dashboard with attendance)
    public function dashboard()
    {
        $employee = Employee::where('registers_id', Auth::id())->first();
        
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found'
            ], 404);
        }
        
        $todayAttendance = Attendance::where('employee_id', $employee->id)
            ->where('date', today())
            ->first();

        return view('emp_dashboard', compact('todayAttendance', 'employee'));
    }

    public function checkIn(Request $request)
    {
        try {
            $employee = Employee::where('registers_id', Auth::id())->first();
            
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee profile not found'
                ], 404);
            }

            // Check if already checked in today
            $existingAttendance = Attendance::where('employee_id', $employee->id)
                ->where('date', today())
                ->first();

            if ($existingAttendance && $existingAttendance->check_in) {
                return response()->json([
                    'success' => false,
                    'message' => 'Already checked in today'
                ]);
            }

            // Create or update attendance record
            if ($existingAttendance) {
                $existingAttendance->update([
                    'check_in' => now()->format('H:i:s'),
                    'status' => 'checked_in',
                    'attendance_status' => 'Present' // Explicitly set to Present
                ]);
                $attendance = $existingAttendance;
            } else {
                $attendance = Attendance::create([
                    'employee_id' => $employee->id,
                    'date' => today(),
                    'check_in' => now()->format('H:i:s'),
                    'status' => 'checked_in',
                    'attendance_status' => 'Present', // Explicitly set to Present
                    'breaks' => [],
                    'total_work_hours' => 0,
                    'total_break_hours' => 0
                ]);
            }

            return response()->json([
                'success' => true,
                'attendance' => $attendance,
                'current_status' => 'checked_in',
                'attendance_status' => 'Present',
                'message' => 'Checked in successfully'
            ]);

        } catch (\Exception $e) {
            Log::error('CheckIn error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Check-in failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function checkOut(Request $request)
    {
        try {
            $employee = Employee::where('registers_id', Auth::id())->first();
            
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found'
                ], 404);
            }
            
            $attendance = Attendance::where('employee_id', $employee->id)
                ->where('date', today())
                ->first();

            if ($attendance) {
                // Calculate total break hours first
                $totalBreakHours = $attendance->calculateTotalBreakHours();
                
                // Update with check_out time and break hours
                $attendance->update([
                    'check_out' => now()->format('H:i:s'),
                    'status' => 'checked_out',
                    'total_break_hours' => $totalBreakHours,
                    'attendance_status' => 'Present' //final status for the day - Present
                ]);

                // Calculate and update work hours
                $totalWorkHours = $attendance->calculateTotalWorkHours();
                $attendance->update([
                    'total_work_hours' => $totalWorkHours
                ]);

                return response()->json([
                    'success' => true,
                    'attendance' => $attendance->fresh(),
                    'current_status' => 'checked_out',
                    'attendance_status' => 'Present',
                    'message' => 'Checked out successfully'
                ]);
            }

            return response()->json([
                'success' => false, 
                'message' => 'No check-in record found'
            ]);

        } catch (\Exception $e) {
            Log::error('CheckOut error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Check-out failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function breakIn()
    {
         try {
            $employee = Employee::where('registers_id', Auth::id())->first();
            
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found'
                ], 404);
            }
            
            $attendance = Attendance::where('employee_id', $employee->id)
                ->where('date', today())
                ->first();

            if ($attendance && $attendance->status === 'checked_in') {
                $attendance->update([
                    'status' => 'on_break'
                ]);

                // Store current break start time in session (for this specific break)
                session(['current_break_start' => now()->timestamp]);

                return response()->json([
                    'success' => true,
                    'message' => 'Break started successfully',
                    'current_status' => 'on_break'
                ]);
            }

            return response()->json([
                'success' => false, 
                'message' => 'Cannot start break - Not checked in or already on break'
            ]);

        } catch (\Exception $e) {
            Log::error('BreakIn error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Break in failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function breakOut()
    {
        try {
            $employee = Employee::where('registers_id', Auth::id())->first();
            
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found'
                ], 404);
            }
            
            $attendance = Attendance::where('employee_id', $employee->id)
                ->where('date', today())
                ->first();

            if ($attendance && $attendance->status === 'on_break') {
                $breakStartTime = session('current_break_start');
                
                if (!$breakStartTime) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Break start time not found'
                    ]);
                }
                
                $breakEndTime = now()->timestamp;
                $breakDuration = $breakEndTime - $breakStartTime;

                // Add this break to breaks array
                $attendance->addBreak(
                    Carbon::createFromTimestamp($breakStartTime)->format('H:i:s'),
                    Carbon::createFromTimestamp($breakEndTime)->format('H:i:s'),
                    $breakDuration
                );

                $attendance->update([
                    'status' => 'checked_in'
                ]);

                // Clear current break session
                session()->forget('current_break_start');

                return response()->json([
                    'success' => true,
                    'message' => 'Break ended successfully',
                    'current_status' => 'checked_in'
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Cannot end break - Not currently on break'
            ]);

        } catch (\Exception $e) {
            Log::error('BreakOut error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Break out failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCurrentStatus()
    {
        try {
            $employee = Employee::where('registers_id', Auth::id())->first();
            
            if (!$employee) {
                return response()->json([
                    'attendance' => null,
                    'break_start_time' => null
                ]);
            }
            
            $attendance = Attendance::where('employee_id', $employee->id)
                ->where('date', today())
                ->first();

            return response()->json([
                'attendance' => $attendance,
                'break_start_time' => session('break_start_time')
            ]);

        } catch (\Exception $e) {
            Log::error('GetCurrentStatus error: ' . $e->getMessage());
            return response()->json([
                'attendance' => null,
                'break_start_time' => null
            ]);
        }
    }

    // Auto-mark absent employees at the end of day (can be called via scheduler)
    public function autoMarkAbsent()
    {
        try {
            $today = Carbon::today();
            $employees = Employee::all();
            $markedAbsent = 0;

            foreach ($employees as $employee) {
                $attendance = Attendance::where('employee_id', $employee->id)
                    ->whereDate('date', $today)
                    ->first();

                if (!$attendance) {
                    // No attendance record for today, mark as absent
                    Attendance::create([
                        'employee_id' => $employee->id,
                        'date' => $today,
                        'status' => 'checked_out',
                        'attendance_status' => 'Absent', // Make sure this is set
                        'check_in' => null,
                        'check_out' => null,
                        'breaks' => [],
                        'total_work_hours' => 0,
                        'total_break_hours' => 0
                    ]);
                    $markedAbsent++;
                } elseif (!$attendance->check_in && !$attendance->attendance_status) {
                    // Has attendance record but no check-in, mark as absent
                    $attendance->update([
                        'attendance_status' => 'Absent' // Make sure this is set
                    ]);
                    $markedAbsent++;
                }
            }

            Log::info("Auto-marked {$markedAbsent} employees as absent for {$today->format('Y-m-d')}");

            return response()->json([
                'success' => true,
                'message' => "Auto-marked {$markedAbsent} employees as absent",
                'marked_absent' => $markedAbsent
            ]);

        } catch (\Exception $e) {
            Log::error('AutoMarkAbsent error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Auto-mark absent failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
