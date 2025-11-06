<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'department_name'
    ];

    // Relationship: A department can have many employees.
    public function employees(){
        return $this->hasMany(Employee::class, 'department_id');
    }

    // Relationship: A department can have many designations.
    public function designations(){
        return $this->hasMany(Designation::class, 'department_id');
    }
}
