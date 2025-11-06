<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_id', 'designation'
    ];

    // Relationship: Each designation belongs to one department.

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    // Relationship: A designation can have many employees.
    public function employees()
    {
        return $this->hasMany(Employee::class, 'designation_id');
    }
}
