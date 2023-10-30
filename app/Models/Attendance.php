<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enum\AttendanceLevel;

class Attendance extends Model
{
    protected $fillable = [
        'day_id',
        'student_id',
        'm_in',
        'm_out',
        'af_in',
        'af_out',
    ];

    protected $casts = [
        'm_in' => AttendanceLevel::class,
        'm_out' => AttendanceLevel::class,
        'af_in' => AttendanceLevel::class,
        'af_out' => AttendanceLevel::class,
    ];


    public function day()
    {
        return $this->belongsTo(Day::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function ecmember()
    {
        return $this->belongsTo(ECMember::class);
    }
}
