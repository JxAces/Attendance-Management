<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ECMember extends Model
{
    use HasFactory;
    protected $fillable = [
        'id_no',
        'full_name',
    ];

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}