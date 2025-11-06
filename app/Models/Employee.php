<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'registers_id', 'first_name', 'last_name', 'email', 'phone', 'photo', 'gender',
        'date_of_birth', 'department_id', 'designation_id', 'status', 'date_of_joining'
    ];


    // Relationship: Each employee belongs to one register (user).
    public function register()
    {
        return $this->belongsTo(Register::class, 'registers_id');
    }

    //  Relationship: Each employee belongs to one department.
    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    // Relationship: Each employee belongs to one designation.
    public function designation()
    {
        return $this->belongsTo(Designation::class, 'designation_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    // Helper method to check if employee is HR
    public function isHr()
    {
        return $this->register->role === 'hr';
    }

    // Helper method to check if employee is regular employee
    public function isRegularEmployee()
    {
        return $this->register->role === 'employee';
    }

    public function todayAttendance()
    {
        return $this->hasOne(Attendance::class)->whereDate('date', Carbon::today());
    }

   // In your Employee model - replace the existing getTodayAttendanceStatus method
    public function getTodayAttendanceStatus()
    {
        $todayAttendance = $this->todayAttendance;
        
        if ($todayAttendance) {
            // If employee has checked out, show "Present" for the day
            if ($todayAttendance->status === 'checked_out' && $todayAttendance->check_in) {
                return 'Present';
            }
            
            // If employee is currently on break, show "On Break"
            if ($todayAttendance->status === 'on_break') {
                return 'On Break';
            }
            
            // If employee is checked in but not on break, show "Checked In"
            if ($todayAttendance->status === 'checked_in' && $todayAttendance->check_in) {
                return 'Checked In';
            }
            
            // If employee has checked in today, they are present
            if ($todayAttendance->check_in) {
                return 'Present';
            }
            
            // If attendance_status is explicitly set to Present/Absent, use that
            if ($todayAttendance->attendance_status) {
                return $todayAttendance->attendance_status;
            }
            
            // Check if it's past working hours and no check-in
            $now = Carbon::now();
            
            // If we're near the end of the day (e.g., after 6 PM) and no check-in, mark absent
            if ($now->greaterThan(Carbon::today()->setHour(18)) && !$todayAttendance->check_in) {
                // Update the attendance record to mark as absent
                $todayAttendance->update([
                    'status' => 'checked_out',
                    'attendance_status' => 'Absent'
                ]);
                return 'Absent';
            }
            
            return 'Not Checked In';
        } else {
            // Check if we should create an absent record for today
            $now = Carbon::now();
            
            // If it's past a certain time (e.g., 6 PM) and no attendance record exists, mark as absent
            if ($now->greaterThan(Carbon::today()->setHour(18))) {
                // Create absent record
                Attendance::create([
                    'employee_id' => $this->id,
                    'date' => Carbon::today(),
                    'status' => 'checked_out',
                    'attendance_status' => 'Absent',
                    'check_in' => null,
                    'check_out' => null,
                    'breaks' => [],
                    'total_work_hours' => 0,
                    'total_break_hours' => 0
                ]);
                return 'Absent';
            }
            
            return 'Not Checked In';
        }
    }

    // Add this new method for real-time status checking
    public function getCurrentAttendanceStatus()
    {
        $todayAttendance = $this->todayAttendance;
        
        if (!$todayAttendance) {
            return 'not_checked_in';
        }
        
        return $todayAttendance->status; // checked_in, on_break, checked_out
    }

    // Helper methods for total counts
    public function getTotalPresentAttribute()
    {
        return $this->attendances->where('attendance_status', 'Present')->count();
    }

    public function getTotalAbsentAttribute()
    {
        return $this->attendances->where('attendance_status', 'Absent')->count();
    }

    // Calculate age
    public function age()
    {
        return \Carbon\Carbon::parse($this->date_of_birth)->age;
    }

    // Contract duration from joining date
    public function contractDuration()
    {
        return \Carbon\Carbon::parse($this->date_of_joining)->diff(\Carbon\Carbon::now())->format('%y years, %m months, %d days');
    }

    // Attendance stats
    public function attendanceStats()
    {
        $total = $this->attendances()->count();
        $present = $this->attendances()->where('attendance_status','Present')->count();
        $absent = $this->attendances()->where('attendance_status','Absent')->count();
        $late = $this->attendances()->where('status','Late')->count();
        $percentage = $total > 0 ? round(($present / $total) * 100, 2) : 0;

        return compact('total','present','absent','late','percentage');
    }
}
