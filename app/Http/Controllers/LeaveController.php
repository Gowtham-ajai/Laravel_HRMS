<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Employee;
use App\Models\Leave;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class LeaveController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // Use role-based HR checking instead of is_hr
        $isHr = $this->isUserHr($user);
        
        if ($user->is_admin) {
            // Admin sees ALL leaves with employee and register info
            $leaves = Leave::with(['employee.register'])->latest()->get();
        } elseif ($isHr) {
            // HR sees: employee leaves (where register role is 'employee') + their own leaves
            $leaves = Leave::with(['employee.register'])
                ->whereHas('employee.register', function($query) {
                    $query->where('role', 'employee'); // Regular employee leaves
                })
                ->orWhereHas('employee', function($query) use ($user) {
                    $query->where('registers_id', $user->id); // HR's own leaves
                })
                ->latest()
                ->get();
        } else {
            // Regular employees see only their leaves
            $employee = Employee::where('registers_id', $user->id)->first();
            $leaves = $employee ? $employee->leaves()->with('employee.register')->latest()->get() : collect();
        }

        return view('leaves.index', compact('leaves'));
    }

    // Approve/Reject with authorization based on register role
    public function approve($id)
    {
        $leave = Leave::with(['employee.register'])->findOrFail($id);
        $user = Auth::user();

        // Authorization logic - Check the employee's register role
        if ($leave->employee->register->role === 'hr' && !$user->is_admin) {
            return response()->json(['success' => false, 'message' => 'Only admin can approve HR leaves'], 403);
        }

        if ($leave->employee->register->role === 'employee' && !$user->is_hr) {
            return response()->json(['success' => false, 'message' => 'Only HR can approve employee leaves'], 403);
        }

        $leave->status = 'Approved';
        $leave->save();

        return response()->json([
            'success' => true,
            'status' => $leave->status,
            'leave_id' => $leave->id,
            'employee_id' => $leave->employee_id
        ]);
    }

    public function reject($id)
    {
        $leave = Leave::with(['employee.register'])->findOrFail($id);
        $user = Auth::user();

        // Same authorization logic
        if ($leave->employee->register->role === 'hr' && !$user->is_admin) {
            return response()->json(['success' => false, 'message' => 'Only admin can reject HR leaves'], 403);
        }

        if ($leave->employee->register->role === 'employee' && !$user->is_hr) {
            return response()->json(['success' => false, 'message' => 'Only HR can reject employee leaves'], 403);
        }

        $leave->status = 'Rejected';
        $leave->save();

        return response()->json([
            'success' => true,
            'status' => $leave->status,
            'leave_id' => $leave->id,
            'employee_id' => $leave->employee_id
        ]);
    }

    // In your LeaveController updateStatus method
    public function updateStatus(Request $request)
    {
        $request->validate([
            'leave_id' => 'required|integer|exists:leaves,id',
            'status' => 'required|string|in:Approved,Rejected'
        ]);

        $leave = Leave::with('employee.register')->findOrFail($request->leave_id);
        $user = Auth::user();

        // Authorization logic
        if ($leave->employee->register->role === 'hr' && !$user->is_admin) {
            return response()->json([
                'success' => false,
                'message' => 'Only admin can approve HR leaves.'
            ], 403);
        }

        if ($leave->employee->register->role === 'employee' && !$user->is_hr) {
            return response()->json([
                'success' => false,
                'message' => 'Only HR can approve employee leaves.'
            ], 403);
        }

        $leave->status = $request->status;
        $leave->save();

        return response()->json([
            'success' => true,
            'message' => 'Leave status updated successfully!',
            'leave_id' => $leave->id,
            'new_status' => $leave->status
        ]);
    }

    // Add a method to check available leaves
    public function applyLeave(Request $request)
    {
        // Get employee record
        $employee = Employee::where('registers_id', Auth::id())->firstOrFail();

        $validated = $request->validate([
            'leave_type' => 'required|string|in:Sick,Casual,Annual,Unpaid',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'remarks' => 'nullable|string'
        ]);

        // Calculate total days requested
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $totalDaysRequested = $startDate->diffInDays($endDate) + 1; // Inclusive of both start and end dates

        // Check if the leave spans across multiple months
        if (!$startDate->isSameMonth($endDate)) {
            return response()->json([
                'success' => false,
                'message' => 'Leave cannot span across multiple months. Please apply for leaves within the same month.'
            ], 422);
        }

        // Get the month and year of the requested leave
        $leaveMonth = $startDate->month;
        $leaveYear = $startDate->year;

        // Calculate already taken leaves for this month (BOTH approved AND pending leaves count)
        $existingLeavesThisMonth = Leave::where('employee_id', $employee->id)
            ->whereIn('status', ['Approved', 'Pending']) // Count both approved AND pending leaves
            ->whereYear('start_date', $leaveYear)
            ->whereMonth('start_date', $leaveMonth)
            ->get();

        $alreadyTakenDays = 0;
        foreach ($existingLeavesThisMonth as $existingLeave) {
            $existingStart = Carbon::parse($existingLeave->start_date);
            $existingEnd = Carbon::parse($existingLeave->end_date);
            $existingDays = $existingStart->diffInDays($existingEnd) + 1;
            $alreadyTakenDays += $existingDays;
        }

        // Check for duplicate leave dates
        $isDuplicateLeave = Leave::where('employee_id', $employee->id)
            ->whereIn('status', ['Approved', 'Pending'])
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->exists();

        if ($isDuplicateLeave) {
            return response()->json([
                'success' => false,
                'message' => 'You already have a leave application for the selected dates. Please choose different dates.'
            ], 422);
        }

        // Check if this new leave would exceed the 2-day limit
        $totalDaysAfterThisLeave = $alreadyTakenDays + $totalDaysRequested;

        if ($totalDaysAfterThisLeave > 2) {
            $remainingDays = 2 - $alreadyTakenDays;
            
            if ($remainingDays <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already used all 2 leaves for this month. No more leaves available.'
                ], 422);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "You can only take {$remainingDays} more days of leave this month. You have already used {$alreadyTakenDays} days and requested {$totalDaysRequested} days."
                ], 422);
            }
        }

        // If validation passes, create the leave
        $leave = Leave::create([
            'employee_id' => $employee->id,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'leave_type' => $validated['leave_type'],
            'remarks' => $validated['remarks'],
            'status' => 'Pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Leave request submitted successfully! Admin will review it.',
            'leave' => $leave,
            'days_requested' => $totalDaysRequested,
            'remaining_days_this_month' => 2 - $alreadyTakenDays - $totalDaysRequested
        ]);
    }

    public function checkAvailableLeaves(Request $request)
    {
        $employee = Employee::where('registers_id', Auth::id())->firstOrFail();
        
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $totalDaysRequested = $startDate->diffInDays($endDate) + 1;

        // Check if the leave spans across multiple months
        if (!$startDate->isSameMonth($endDate)) {
            return response()->json([
                'success' => false,
                'message' => 'Leave cannot span across multiple months.'
            ]);
        }

        $leaveMonth = $startDate->month;
        $leaveYear = $startDate->year;

        // Calculate already taken leaves for this month (BOTH approved AND pending)
        $existingLeavesThisMonth = Leave::where('employee_id', $employee->id)
            ->whereIn('status', ['Approved', 'Pending']) // Count both approved AND pending
            ->whereYear('start_date', $leaveYear)
            ->whereMonth('start_date', $leaveMonth)
            ->get();

        $alreadyTakenDays = 0;
        foreach ($existingLeavesThisMonth as $existingLeave) {
            $existingStart = Carbon::parse($existingLeave->start_date);
            $existingEnd = Carbon::parse($existingLeave->end_date);
            $existingDays = $existingStart->diffInDays($existingEnd) + 1;
            $alreadyTakenDays += $existingDays;
        }

        // Check for duplicate dates (excluding the current check)
        $isDuplicateLeave = Leave::where('employee_id', $employee->id)
            ->whereIn('status', ['Approved', 'Pending'])
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->exists();

        $remainingDays = 2 - $alreadyTakenDays;
        $canApply = !$isDuplicateLeave && $totalDaysRequested <= $remainingDays;

        // Build appropriate message
        $message = '';
        if ($isDuplicateLeave) {
            $message = 'You already have a leave application for the selected dates. Please choose different dates.';
        } elseif ($totalDaysRequested > $remainingDays) {
            $message = "You can only take {$remainingDays} more days of leave this month. You have already used {$alreadyTakenDays} days and requested {$totalDaysRequested} days.";
        } else {
            $message = "You can apply for {$totalDaysRequested} days of leave. Remaining days this month: {$remainingDays}";
        }

        return response()->json([
            'success' => true,
            'can_apply' => $canApply,
            'requested_days' => $totalDaysRequested,
            'already_taken_days' => $alreadyTakenDays,
            'remaining_days' => $remainingDays,
            'is_duplicate' => $isDuplicateLeave,
            'message' => $message
        ]);
    }

    public function getLeaveSummary(Request $request)
    {
        $employee = Employee::where('registers_id', Auth::id())->firstOrFail();
        
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Count BOTH approved AND pending leaves
        $existingLeavesThisMonth = Leave::where('employee_id', $employee->id)
            ->whereIn('status', ['Approved', 'Pending'])
            ->whereYear('start_date', $currentYear)
            ->whereMonth('start_date', $currentMonth)
            ->get();

        $alreadyTakenDays = 0;
        foreach ($existingLeavesThisMonth as $existingLeave) {
            $existingStart = Carbon::parse($existingLeave->start_date);
            $existingEnd = Carbon::parse($existingLeave->end_date);
            $existingDays = $existingStart->diffInDays($existingEnd) + 1;
            $alreadyTakenDays += $existingDays;
        }

        $remainingDays = 2 - $alreadyTakenDays;

        return response()->json([
            'success' => true,
            'approved_days' => $alreadyTakenDays,
            'remaining_days' => $remainingDays
        ]);
    }

    // HR applies leave with validation
    public function applyHrLeave(Request $request)
    {
        $user = Auth::user();
        
        // Check if user is HR based on role (case-insensitive)
        if (!$this->isUserHr($user)) {
            return response()->json(['success' => false, 'message' => 'Only HR can apply for HR leaves'], 403);
        }

        $validated = $request->validate([
            'leave_type' => 'required|string|in:Sick,Casual,Annual,Unpaid',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'remarks' => 'nullable|string'
        ]);

        // Get HR's employee record
        $hrEmployee = Employee::where('registers_id', Auth::id())->firstOrFail();

        // Calculate total days requested
        $startDate = Carbon::parse($validated['start_date']);
        $endDate = Carbon::parse($validated['end_date']);
        $totalDaysRequested = $startDate->diffInDays($endDate) + 1; // Inclusive of both start and end dates

        // Check if the leave spans across multiple months
        if (!$startDate->isSameMonth($endDate)) {
            return response()->json([
                'success' => false,
                'message' => 'Leave cannot span across multiple months. Please apply for leaves within the same month.'
            ], 422);
        }

        // Get the month and year of the requested leave
        $leaveMonth = $startDate->month;
        $leaveYear = $startDate->year;

        // Calculate already taken leaves for this month (BOTH approved AND pending leaves count)
        $existingLeavesThisMonth = Leave::where('employee_id', $hrEmployee->id)
            ->whereIn('status', ['Approved', 'Pending']) // Count both approved AND pending leaves
            ->whereYear('start_date', $leaveYear)
            ->whereMonth('start_date', $leaveMonth)
            ->get();

        $alreadyTakenDays = 0;
        foreach ($existingLeavesThisMonth as $existingLeave) {
            $existingStart = Carbon::parse($existingLeave->start_date);
            $existingEnd = Carbon::parse($existingLeave->end_date);
            $existingDays = $existingStart->diffInDays($existingEnd) + 1;
            $alreadyTakenDays += $existingDays;
        }

        // Check for duplicate leave dates
        $isDuplicateLeave = Leave::where('employee_id', $hrEmployee->id)
            ->whereIn('status', ['Approved', 'Pending'])
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->exists();

        if ($isDuplicateLeave) {
            return response()->json([
                'success' => false,
                'message' => 'You already have a leave application for the selected dates. Please choose different dates.'
            ], 422);
        }

        // Check if this new leave would exceed the 2-day limit
        $totalDaysAfterThisLeave = $alreadyTakenDays + $totalDaysRequested;

        if ($totalDaysAfterThisLeave > 2) {
            $remainingDays = 2 - $alreadyTakenDays;
            
            if ($remainingDays <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already used all 2 leaves for this month. No more leaves available.'
                ], 422);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "You can only take {$remainingDays} more days of leave this month. You have already used {$alreadyTakenDays} days and requested {$totalDaysRequested} days."
                ], 422);
            }
        }

        // If validation passes, create the leave
        $leave = Leave::create([
            'employee_id' => $hrEmployee->id,
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'leave_type' => $validated['leave_type'],
            'remarks' => $validated['remarks'],
            'status' => 'Pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'HR leave submitted! Admin will approve.',
            'leave' => $leave,
            'days_requested' => $totalDaysRequested,
            'remaining_days_this_month' => 2 - $alreadyTakenDays - $totalDaysRequested
        ]);
    }

    // Check available leaves for HR - FIXED VERSION
    public function checkHrAvailableLeaves(Request $request)
    {
        $user = Auth::user();
        
        // Check if user is HR based on role (case-insensitive)
        if (!$this->isUserHr($user)) {
            return response()->json(['success' => false, 'message' => 'Unauthorized - HR access required'], 403);
        }

        $hrEmployee = Employee::where('registers_id', Auth::id())->firstOrFail();
        
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);
        $totalDaysRequested = $startDate->diffInDays($endDate) + 1;

        // Check if the leave spans across multiple months
        if (!$startDate->isSameMonth($endDate)) {
            return response()->json([
                'success' => false,
                'message' => 'Leave cannot span across multiple months.'
            ]);
        }

        $leaveMonth = $startDate->month;
        $leaveYear = $startDate->year;

        // Calculate already taken leaves for this month (BOTH approved AND pending)
        $existingLeavesThisMonth = Leave::where('employee_id', $hrEmployee->id)
            ->whereIn('status', ['Approved', 'Pending'])
            ->whereYear('start_date', $leaveYear)
            ->whereMonth('start_date', $leaveMonth)
            ->get();

        $alreadyTakenDays = 0;
        foreach ($existingLeavesThisMonth as $existingLeave) {
            $existingStart = Carbon::parse($existingLeave->start_date);
            $existingEnd = Carbon::parse($existingLeave->end_date);
            $existingDays = $existingStart->diffInDays($existingEnd) + 1;
            $alreadyTakenDays += $existingDays;
        }

        // Check for duplicate dates
        $isDuplicateLeave = Leave::where('employee_id', $hrEmployee->id)
            ->whereIn('status', ['Approved', 'Pending'])
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->exists();

        $remainingDays = 2 - $alreadyTakenDays;
        $canApply = !$isDuplicateLeave && $totalDaysRequested <= $remainingDays;

        // Build appropriate message
        $message = "";
        if ($isDuplicateLeave) {
            $message = "You already have a leave application for the selected dates. Please choose different dates.";
        } else if ($totalDaysRequested > $remainingDays) {
            $message = "You can only take {$remainingDays} more days of leave this month. You have already used {$alreadyTakenDays} days and requested {$totalDaysRequested} days.";
        } else {
            $message = "You can apply for {$totalDaysRequested} days of leave. Remaining days this month: {$remainingDays}";
        }

        return response()->json([
            'success' => true,
            'can_apply' => $canApply,
            'requested_days' => $totalDaysRequested,
            'already_taken_days' => $alreadyTakenDays,
            'remaining_days' => $remainingDays,
            'is_duplicate' => $isDuplicateLeave,
            'message' => $message
        ]);
    }

    // Get HR leave summary - FIXED VERSION
    public function getHrLeaveSummary(Request $request)
    {
        try {
            // Get the authenticated user
            $user = Auth::user();
            
            // Check if user is HR based on role (case-insensitive)
            if (!$this->isUserHr($user)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized - HR access required. Your role: ' . ($user->role ?? 'not set')
                ], 403);
            }

            // Get the employee record
            $hrEmployee = Employee::where('registers_id', $user->id)->first();
            
            if (!$hrEmployee) {
                return response()->json([
                    'success' => false,
                    'message' => 'No employee record found for HR user'
                ], 404);
            }

            $currentMonth = Carbon::now()->month;
            $currentYear = Carbon::now()->year;

            // Count BOTH approved AND pending leaves
            $existingLeavesThisMonth = Leave::where('employee_id', $hrEmployee->id)
                ->whereIn('status', ['Approved', 'Pending'])
                ->whereYear('start_date', $currentYear)
                ->whereMonth('start_date', $currentMonth)
                ->get();

            $alreadyTakenDays = 0;
            foreach ($existingLeavesThisMonth as $existingLeave) {
                $existingStart = Carbon::parse($existingLeave->start_date);
                $existingEnd = Carbon::parse($existingLeave->end_date);
                $existingDays = $existingStart->diffInDays($existingEnd) + 1;
                $alreadyTakenDays += $existingDays;
            }

            $remainingDays = 2 - $alreadyTakenDays;

            return response()->json([
                'success' => true,
                'approved_days' => $alreadyTakenDays,
                'remaining_days' => $remainingDays,
                'employee_name' => $hrEmployee->first_name . ' ' . $hrEmployee->last_name
            ]);

        } catch (\Exception $e) {
            Log::error('Error in getHrLeaveSummary: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Server error: ' . $e->getMessage()
            ], 500);
        }
    }

    // ADD THIS HELPER METHOD TO YOUR CONTROLLER
    private function isUserHr($user)
    {
        // Check role property - handle both uppercase and lowercase
        if (isset($user->role)) {
            $role = strtolower(trim($user->role));
            if ($role === 'hr') {
                return true;
            }
        }
        
        // Fallback: Check if user has any employee record
        $hrEmployee = Employee::where('registers_id', $user->id)->first();
        if ($hrEmployee) {
            return true;
        }
        
        return false;
    }

}
