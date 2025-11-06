<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HRDepartment extends Model
{
    use HasFactory;

    // Explicitly define the table name
    protected $table = 'hrdepartments';

    protected $fillable =[
        'department_name', 'status'
    ];


    // Relationship with HR employees - WITHOUT foreign key constraint
    public function hrEmployees()
    {
        return $this->hasMany(Register::class, 'hr_department_id')->where('role', 'HR');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function getEmployeeCountAttribute()
    {
        return $this->hrEmployees()->count();
    }
}
