<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $table = 'user';

    protected $fillable = [
        'full_name',
        'password',
        'id_number',
        'date_of_birth',
        'phone_number',
        'address',
        'is_admin',
        'category_id',
        'creation_by',
        'updated_by',
        'deleted_by',
    ];

    protected $dates = ['deleted_at'];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    // العلاقات
    public function courses()
    {
        return $this->belongsToMany(Course::class, 'course_user');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function groups()
    {
        return $this->hasMany(Group::class, 'UserId');
    }
}
