<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    protected $fillable = ['student_id', 'status', 'section', 'scanned_at'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
    protected $casts = [
    'scanned_at' => 'datetime',
    ];
}
