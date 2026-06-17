<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Student extends Model
{
    protected $fillable = [
        'district_id',
        'barcode',
        'firstname',
        'lastname',
        'course',
        'mobile_number',
        'year',
        'profile_picture',
        'qrcode',
        'birth_date',
        'blood_type',
        'emergency_person',
        'emergency_relationship',
        'emergency_number',
        'emergency_address',
        'student_signature',
        'middle_initial',
        'role_id',
        'normalized_name',
        'address',
    ];
    
    protected static function booted()
    {
        static::creating(function ($student) {
            if (empty($student->qrcode)) {
                // Example: encode a UUID or ID
                $student->qrcode = Str::uuid()->toString();
            }
        });
    }
    
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
    
    public function role()
    {
        return $this->belongsTo(Role::class);
    }
}
