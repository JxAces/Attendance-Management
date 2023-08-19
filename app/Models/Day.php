<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Day extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        // Add other fillable attributes as needed
    ];
    
    public $timestamps = false;

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
    
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }   
}

