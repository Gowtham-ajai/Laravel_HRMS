<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class Attendance extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'employee_id',
        'date',
        'check_in',
        'check_out',
        'breaks',
        'total_work_hours',
        'total_break_hours',
        'status', 'attendance_status',
    ];

    protected $casts = [
        'breaks' => 'array',
        'date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }   

    public function addBreak($breakIn, $breakOut, $duration)
    {
        $breaks = $this->breaks ?? [];
        $breaks[] = [
            'break_in' => $breakIn,
            'break_out' => $breakOut,
            'duration' => $duration
        ];
        
        $this->breaks = $breaks;
        $this->total_break_hours = $this->calculateTotalBreakHours();
        $this->save();
    }

    public function calculateTotalBreakHours()
    {
        $breaks = $this->breaks ?? [];
        $totalSeconds = 0;
        
        foreach ($breaks as $break) {
            $totalSeconds += $break['duration'];
        }
        
        return $totalSeconds / 3600; // Convert to hours
    }

    public function calculateTotalWorkHours()
    {
        if (!$this->check_in || !$this->check_out) {
            Log::warning('Missing check_in or check_out for work hours calculation', [
                'check_in' => $this->check_in,
                'check_out' => $this->check_out
            ]);
            return 0;
        }

        try {
            Log::info('Calculating work hours', [
                'check_in' => $this->check_in,
                'check_out' => $this->check_out,
                'total_break_hours' => $this->total_break_hours,
                'breaks_count' => count($this->breaks ?? [])
            ]);

            // Parse times with today's date to handle overnight shifts if needed
            $checkInTime = Carbon::parse($this->date . ' ' . $this->check_in);
            $checkOutTime = Carbon::parse($this->date . ' ' . $this->check_out);
            
            // If check_out is earlier than check_in (overnight), add one day to check_out
            if ($checkOutTime->lessThan($checkInTime)) {
                $checkOutTime->addDay();
            }

            // Calculate total time between check-in and check-out in seconds
            $totalSeconds = $checkOutTime->diffInSeconds($checkInTime);
            
            Log::info('Time calculation', [
                'total_seconds' => $totalSeconds,
                'total_minutes' => $totalSeconds / 60,
                'total_hours' => $totalSeconds / 3600
            ]);

            // Subtract total break time in seconds
            $totalBreakSeconds = $this->total_break_hours * 3600;
            $netWorkSeconds = $totalSeconds - $totalBreakSeconds;
            
            Log::info('Break deduction', [
                'total_break_seconds' => $totalBreakSeconds,
                'net_work_seconds' => $netWorkSeconds,
                'net_work_hours' => $netWorkSeconds / 3600
            ]);

            // Ensure we don't return negative time
            $result = max(0, $netWorkSeconds) / 3600;
            
            Log::info('Final work hours result', [
                'result' => $result
            ]);

            return $result;

        } catch (\Exception $e) {
            Log::error('Work hours calculation error: ' . $e->getMessage(), [
                'check_in' => $this->check_in,
                'check_out' => $this->check_out
            ]);
            return 0;
        }
    }

     // Get current status for real-time updates
    public function getCurrentStatusAttribute()
    {
        return $this->status; // checked_in, on_break, checked_out
    }

    // Automatically determine attendance status based on check-in
    public function getAttendanceStatusAttribute($value)
    {
        // If attendance_status is already set, return it
        if ($value) {
            return $value;
        }

        // Auto-calculate based on check-in time
        if ($this->check_in) {
            return 'Present';
        }

        // If no check-in and it's past working hours (6 PM), mark as absent
        $now = Carbon::now();
        $attendanceDate = Carbon::parse($this->date);
        
        // Only mark as absent if it's today's date and past 6 PM
        if ($attendanceDate->isToday() && $now->greaterThan(Carbon::today()->setHour(18))) {
            return 'Absent';
        }

        // For future dates or before 6 PM, return null
        return null;
    }

    // Scope for today's attendance
    public function scopeToday($query)
    {
        return $query->whereDate('date', Carbon::today());
    }

    // Scope for present employees
    public function scopePresent($query)
    {
        return $query->where('attendance_status', 'Present');
    }

    // Scope for absent employees
    public function scopeAbsent($query)
    {
        return $query->where('attendance_status', 'Absent');
    }

    // Check if attendance is for today
    public function getIsTodayAttribute()
    {
        return Carbon::parse($this->date)->isToday();
    }

    // Get formatted work hours
    public function getFormattedWorkHoursAttribute()
    {
        if (!$this->total_work_hours) {
            return '00:00:00';
        }

        $hours = floor($this->total_work_hours);
        $minutes = floor(($this->total_work_hours - $hours) * 60);
        $seconds = 0;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    // Get formatted break hours
    public function getFormattedBreakHoursAttribute()
    {
        if (!$this->total_break_hours) {
            return '00:00:00';
        }

        $hours = floor($this->total_break_hours);
        $minutes = floor(($this->total_break_hours - $hours) * 60);
        $seconds = 0;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    // NEW METHOD: Update status and attendance_status together
    public function updateStatus($newStatus)
    {
        $updates = ['status' => $newStatus];
        
        // Automatically set attendance_status to Present when checking in
        if ($newStatus === 'checked_in' && !$this->attendance_status) {
            $updates['attendance_status'] = 'Present';
        }
        
        // If checking out, ensure attendance_status is Present
        if ($newStatus === 'checked_out' && $this->check_in) {
            $updates['attendance_status'] = 'Present';
        }
        
        $this->update($updates);
    }
}
