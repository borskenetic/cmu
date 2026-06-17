<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PendingStudent extends Model
{
    protected $fillable = [
        'district_id',
        'barcode',
        'firstname',
        'lastname',
        'middle_initial',
        'birth_date',
        'blood_type',
        'course',
        'year',
        'mobile_number',
        'profile_picture',
        'emergency_person',
        'emergency_relationship',
        'emergency_number',
        'emergency_address',
        'student_signature',
        'address',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
