<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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

    public function day()
    {
        return $this->belongsTo(Day::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
