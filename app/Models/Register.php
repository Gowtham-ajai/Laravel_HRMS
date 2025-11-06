<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Register extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'hr_department_id', 'gender', 'date_of_birth', 'photo',
        'status', 'date_of_joining'
    ];


    // Add to your existing Register model
    public function hrDepartment()
    {
        // Manual relationship without foreign key constraint
        return $this->belongsTo(HRDepartment::class, 'hr_department_id');
    }

    public function isHR()
    {
        return $this->role === 'HR';
    }

    // Helper method to get HR department name
    public function getHrDepartmentNameAttribute()
    {
        return $this->hrDepartment ? $this->hrDepartment->department_name : 'Not Assigned';
    }
}
