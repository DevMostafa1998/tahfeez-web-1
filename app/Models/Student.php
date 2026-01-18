<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes;
    protected $table = 'student';
    protected $fillable = [
        'full_name',
        'id_number',
        'date_of_birth',
        'phone_number',
        'address',
        'is_displaced',
        'user_id',
        'creation_by',
        'updated_by',
        'deleted_by',
    ];
    protected $casts = [
        'date_of_birth' => 'date',
        'is_displaced' => 'boolean',
    ];
}
