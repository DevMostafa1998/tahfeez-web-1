<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Group extends Model
{
    use SoftDeletes;
    use HasFactory;

    const CREATED_AT = 'creation_at';
    const UPDATED_AT = 'updated_at';
    
    protected $table = 'group';
    protected $fillable = [
        'UserId',
        'GroupName',
        'creation_at',
        'creation_by',
        'updated_at',
        'updated_by',
        'deleted_at',
        'deleted_by',
    ];

    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class, 'UserId');
    }
    public function teacher()
    {
        return $this->belongsTo(User::class, 'UserId');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_group', 'group_id', 'student_id');
    }
}
