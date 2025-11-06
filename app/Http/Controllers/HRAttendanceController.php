<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class HRAttendanceController extends Controller
{
    // HR dashboard - shows own hr attendance
    public function dashboard(){
        $hr = Employee::where('registers_id', Auth::id())->first();

        if(!$hr){
            return response()->json([
                'success' => false,
                'message' => 'HR profile not found'
            ], 404);
        }

        $todayAttendance = Attendance::where('employee_id', $hr->id)
            ->where('date', today())
            ->first();

        return view('hr_partials.hr_dashboard', compact('hr', 'todayAttendance'));
    }

    // checkin
    public function checkIn(Request $request){
        try
        {
             Log::info('HR Checkin method called', ['user_id' => Auth::id()]);

            $hr = Employee::where('registers_id', Auth::id())->first();

            if(!$hr){
                Log::error('HR not found for user', ['user_id'=> Auth::id()]);

                return response()->json([
                    'success' => false,
                    'message' => 'HR Profile not found'
                ], 404);
            }

            // check if already checked in today
            $existingAttendance = Attendance::where('employee_id', $hr->id)
                    ->where('date', today())
                    ->first();

            if($existingAttendance){
                return response()->json([
                    'success' => false,
                    'message' => 'Already checked in today'
                ]);
            } 
  
            $attendance = Attendance::create([
                    'employee_id' => $hr->id,
                    'date' => today(),
                    'check_in' => now()->format('H:i:s'),
                    'status' => 'checked_in',
                    'attendance_status' => 'Present',
                    'breaks' => [],
                    'total_work_hours' => 0,
                    'total_break_hours' => 0
            ]);
            
            Log::info('HR Checkin successful', ['attendance_id' => $attendance->id]);

            return response()->json([
                'success' => true,
                'attendance' => $attendance,
                'message' => 'Checked In Successfully'
            ]);
        } catch(\Exception $e){
            Log::error('HR checkin error:' . $e->getMessage(), [
                'trace' => $e -> getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Check In failed' . $e->getMessage()
            ], 500);
        }
    }

    // check out
    public function checkOut(Request $request){
        try{
            $hr = Employee::where('registers_id', Auth::id())->first();

            if(!$hr){
                return response()->json([
                    'success' => false,
                    'message' => 'HR not found'
                ], 404);
            }

            $attendance = Attendance::where('employee_id', $hr->id)
                    ->where('date', today())
                    ->first();
            
            if (!$attendance) {
                return response()->json([
                    'success' => false, 
                    'message' => 'No check-in record found for today'
                ]);
            }

            // If already checked out, return success
            if ($attendance->status === 'checked_out') {
                return response()->json([
                    'success' => true,
                    'message' => 'Already checked out'
                ]);
            }

            Log::info('HR Checkout processing', [
                'attendance_id' => $attendance->id,
                'current_status' => $attendance->status
            ]);

            // Simple update - don't overcomplicate with multiple updates
            $attendance->update([
                'check_out' => now()->format('H:i:s'),
                'status' => 'checked_out',
                'total_break_hours' => $attendance->calculateTotalBreakHours(),
                'total_work_hours' => $attendance->calculateTotalWorkHours()
            ]);

            Log::info('HR Checkout completed', [
                'attendance_id' => $attendance->id,
                'new_status' => 'checked_out'
            ]);

            return response()->json([
                'success' => true,
                'attendance' => $attendance,
                'message' => 'Checked out successfully'
            ]);

        } catch(\Exception $e){
            Log::error('HR CheckOut error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Check-out failed: ' . $e->getMessage()
            ], 500);
        }
    }
    
    // breakin
    public function breakIn()
    {
         try {
            $hr = Employee::where('registers_id', Auth::id())->first();
            
            if (!$hr) {
                return response()->json([
                    'success' => false,
                    'message' => 'HR not found'
                ], 404);
            }
            
            $attendance = Attendance::where('employee_id', $hr->id)
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
                    'message' => 'Break started successfully'
                ]);
            }

            return response()->json([
                'success' => false, 
                'message' => 'Cannot start break - Not checked in or already on break'
            ]);

        } catch (\Exception $e) {
            Log::error('HR BreakIn error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Break in failed: ' . $e->getMessage()
            ], 500);
        }
    }

    // breakout
    public function breakOut()
    {
        try {
            $hr = Employee::where('registers_id', Auth::id())->first();
            
            if (!$hr) {
                return response()->json([
                    'success' => false,
                    'message' => 'HR not found'
                ], 404);
            }
            
            $attendance = Attendance::where('employee_id', $hr->id)
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
                    'message' => 'Break ended successfully. Total breaks today: ' . 
                                (count($attendance->breaks ?? []) )
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Cannot end break - Not currently on break'
            ]);

        } catch (\Exception $e) {
            Log::error('HR BreakOut error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Break out failed: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getCurrentStatus()
    {
        try {
            $hr = Employee::where('registers_id', Auth::id())->first();
            
            if (!$hr) {
                return response()->json([
                    'attendance' => null,
                    'break_start_time' => null
                ]);
            }
            
            $attendance = Attendance::where('employee_id', $hr->id)
                ->where('date', today())
                ->first();

            return response()->json([
                'attendance' => $attendance,
                'break_start_time' => session('break_start_time')
            ]);

        } catch (\Exception $e) {
            Log::error('HR GetCurrentStatus error: ' . $e->getMessage());
            return response()->json([
                'attendance' => null,
                'break_start_time' => null
            ]);
        }
    }
}
