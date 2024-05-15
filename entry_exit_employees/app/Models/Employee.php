<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'first_name',
        'last_name',
        'middle_names',
        'employment_country',
        'identification_type',
        'identification_number',
        'email',
        'hire_date',
        'department',
        'status',
    ];
}