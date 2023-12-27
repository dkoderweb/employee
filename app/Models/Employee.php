<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'email',
        'phone',
    ];

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'employee_departments', 'employee_id', 'department_id');
    }
}
