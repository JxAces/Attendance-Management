<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_no',
        'full_name',
        'year_level',
        'major',
        'department_program',
        'gender',
        'registration_date',
        'scholarship_status',
        'address',
        'gpa',
        'total_units',
    ];
}
