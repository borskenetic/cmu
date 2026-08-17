<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class College extends Model
{
    protected $fillable = ['name'];

    public function programs()
    {
        return $this->hasMany(Program::class)->orderBy('program_name');
    }
}
