<?php

use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\EmployeeDashboardController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HRAttendanceController;
use App\Models\Attendance;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/add-hr', [AdminController::class, 'createHR'])->name('admin.addHR');
    Route::post('/admin/store-hr', [AdminController::class, 'storeHR'])->name('admin.storeHR');
    Route::get('/admin/get-designations/{department}', [AdminController::class, 'getDesignations']);
});

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::view('/about', 'about')->name('about');
Route::view('/contact', 'contact')->name('contact');
Route::get('/testimonials', [HomeController::class, 'testimonials'])->name('testimonials');
Route::view('/gallery', 'gallery')->name('gallery');

Route::get('/login', function () {
    return redirect()->route('login');
});

// Login
// Route::get('/login', [LoginController::class, 'login'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.post');

Route::middleware(['auth'])->group(function () {

    // HR dashboard
    Route::get('/hr/dashboard', [EmployeeController::class, 'dashboard'])
        ->name('hr.dashboard')
        ->middleware('hr');

    // Employee dashboard & actions
    Route::middleware(['employee'])->group(function () {
        Route::get('/employee/dashboard', [EmployeeDashboardController::class, 'index'])->name('employee.dashboard');
        Route::post('/employee/apply-leave', [EmployeeDashboardController::class, 'applyLeave'])->name('employee.applyLeave');
    });

    // Registration (if required)
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register/store', [RegisterController::class, 'store'])->name('register.store');
});

// Simple password reset route
Route::post('/reset-password-direct', [LoginController::class, 'resetPasswordDirect'])->name('password.reset.direct');



Route::middleware(['auth', 'hr'])->group(function () {

    // HR Attendance Dashboard
    Route::get('/hr/attendance', [AttendanceController::class, 'index'])->name('hr.attendance.index');

    // Employees CRUD
    Route::get('/hr/employees', [EmployeeController::class, 'index'])->name('employees.index');
    Route::get('/hr/employees/create', [EmployeeController::class, 'create'])->name('employees.create');
    Route::post('/hr/employees/store', [EmployeeController::class, 'store'])->name('employees.store');
    Route::get('/hr/employees/edit/{id}', [EmployeeController::class, 'edit'])->name('employees.edit');
    Route::put('/hr/employees/update/{id}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/hr/employees/delete/{id}', [EmployeeController::class, 'destroy'])->name('employees.destroy');

    // AJAX route to fetch designations
    Route::get('/hr/get-designations/{department_id}', [EmployeeController::class, 'getDesignations'])->name('get-designations');

    // HR settings
    Route::post('/hr/{id}/update-settings', [EmployeeController::class, 'hrUpdateSettings'])->name('hr.updateSettings');

    // HR Attendance routes
    Route::get('/hr/attendance/dashboard', [HRAttendanceController::class, 'dashboard'])->name('hr.attendance.dashboard');
    Route::post('/hr/attendance/checkin', [HRAttendanceController::class, 'checkIn'])->name('hr.attendance.checkin');
    Route::post('/hr/attendance/checkout', [HRAttendanceController::class, 'checkOut'])->name('hr.attendance.checkout');
    Route::post('/hr/attendance/breakin', [HRAttendanceController::class, 'breakIn'])->name('hr.attendance.breakin');
    Route::post('/hr/attendance/breakout', [HRAttendanceController::class, 'breakOut'])->name('hr.attendance.breakout');
    Route::get('/hr/attendance/status', [HRAttendanceController::class, 'getCurrentStatus'])->name('hr.attendance.status');
});

// logout
Route::post('/logout', function () {
    Auth::logout();
    return redirect('/'); // redirect to home page
})->name('logout');


Route::middleware(['auth'])->group(function () {    
    // specific attendance & leave
    Route::get('/attendances/{id}/attendance', [AttendanceController::class, 'mark'])->name('employees.attendance.mark');
    Route::post('/attendances/{id}/attendance', [AttendanceController::class, 'storeMark'])->name('employees.attendance.store');

    // update employee settings
    Route::post('/employee/{id}/update-settings', [EmployeeController::class, 'updateSettings'])->name('employee.updateSettings');
});

// overall leave
Route::get('/leaves', [LeaveController::class, 'index'])->name('leaves.index');

// Approve / Reject leave
Route::post('/leaves/{id}/approve', [LeaveController::class, 'approve'])->name('leaves.approve');
Route::post('/leaves/{id}/reject', [LeaveController::class, 'reject'])->name('leaves.reject');

// For HR approval / rejection
Route::post('/hr/leave/update-status', [LeaveController::class, 'updateStatus'])->name('hr.updateLeaveStatus');
// HR Leaves Routes for Admin
Route::post('/admin/hr-leaves/update-status', [AdminController::class, 'updateHrLeaveStatus'])->name('admin.hrLeaves.updateStatus');
Route::get('/admin/hr-leaves', [AdminController::class, 'getHrLeaves'])->name('admin.hrLeaves.get');

Route::middleware(['auth', 'hr'])->group(function () {
    Route::post('/hr/apply-leave', [LeaveController::class, 'applyHrLeave'])->name('hr.applyLeave');
    Route::post('/hr/check-available-leaves', [LeaveController::class, 'checkHrAvailableLeaves'])->name('hr.checkAvailableLeaves');
    Route::get('/hr/leave-summary', [LeaveController::class, 'getHrLeaveSummary'])->name('hr.getLeaveSummary');
});

// check available leaves
Route::middleware(['auth'])->group(function () {
    Route::post('/check-available-leaves', [LeaveController::class, 'checkAvailableLeaves'])->name('employee.checkAvailableLeaves');
    Route::get('/leave-summary', [LeaveController::class, 'getLeaveSummary'])->name('employee.getLeaveSummary');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/attendance/dashboard', [AttendanceController::class, 'dashboard'])->name('attendance.dashboard');
    Route::post('/attendance/checkin', [AttendanceController::class, 'checkIn'])->name('attendance.checkin');
    Route::post('/attendance/checkout', [AttendanceController::class, 'checkOut'])->name('attendance.checkout');
    Route::post('/attendance/breakin', [AttendanceController::class, 'breakIn'])->name('attendance.breakin');
    Route::post('/attendance/breakout', [AttendanceController::class, 'breakOut'])->name('attendance.breakout');
    Route::get('/attendance/status', [AttendanceController::class, 'getCurrentStatus'])->name('attendance.status');
});

// Auto-mark absent route (for testing or scheduling)
Route::post('/attendance/auto-mark-absent', [AttendanceController::class, 'autoMarkAbsent'])->name('attendance.auto-mark-absent');


// Contact routes
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Admin Contact Management
Route::middleware(['auth', 'admin'])->group(function () {

    // contact page
    Route::get('/admin/contact-messages', [ContactController::class, 'adminIndex'])->name('admin.contact.messages');
    Route::get('/admin/contact-messages/{id}', [ContactController::class, 'show'])->name('admin.contact.message.show');
    Route::delete('/admin/contact-messages/{id}', [ContactController::class, 'destroy'])->name('admin.contact.message.delete');
    Route::get('/admin/contact-messages/unread/count', [ContactController::class, 'getUnreadCount'])->name('admin.contact.unread.count');

    // HR list edit,delete
    Route::get('/admin/hr/{id}/edit', [AdminController::class, 'editHR'])->name('admin.hr.edit');
    Route::put('/admin/hr/{id}', [AdminController::class, 'updateHR'])->name('admin.hr.update');
    Route::delete('/admin/hr/{id}', [AdminController::class, 'destroyHR'])->name('admin.hr.destroy');
});

// Add this to your web.php routes file temporarily
Route::get('/fix-attendance-status', function() {
    $fixedPresent = 0;
    $fixedAbsent = 0;
    
    // Fix records with check_in but no attendance_status
    $presentRecords = Attendance::whereNotNull('check_in')
                                ->whereNull('attendance_status')
                                ->get();
    
    foreach ($presentRecords as $record) {
        $record->update(['attendance_status' => 'Present']);
        $fixedPresent++;
    }
    
    // Fix records created by auto-mark absent that might be missing attendance_status
    $absentRecords = Attendance::whereNull('check_in')
                               ->whereNull('attendance_status')
                               ->get();
    
    foreach ($absentRecords as $record) {
        $record->update(['attendance_status' => 'Absent']);
        $fixedAbsent++;
    }
    
    return "Fixed {$fixedPresent} present records and {$fixedAbsent} absent records";
});


// Add this to routes/web.php
Route::get('/test-email', function() {
    try {
        Mail::raw('Test email from Laravel', function($message) {
            $message->to('rohitgowtham796@gmail.com')
                    ->subject('Test Email');
        });
        
        return 'Test email sent successfully!';
    } catch (\Exception $e) {
        return 'Error: ' . $e->getMessage();
    }
});

// Add to routes/web.php
Route::get('/debug-hr-status', function() {
    $hr = \App\Models\Employee::where('registers_id', \Illuminate\Support\Facades\Auth::id())->first();
    
    if (!$hr) return "HR not found";
    
    $todayAttendance = \App\Models\Attendance::where('employee_id', $hr->id)
        ->where('date', today())
        ->first();
        
    return [
        'hr_id' => $hr->id,
        'today_attendance' => $todayAttendance,
        'server_time' => now(),
        'session_break_start' => session('current_break_start')
    ];
});