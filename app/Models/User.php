<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

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
        'birth_place',
        'wallet_number',
        'whatsapp_number',
        'qualification',
        'specialization',
        'parts_memorized',
        'mosque_name',
        'is_displaced'

    ];
    protected $dates = ['deleted_at'];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }
     public function courses()
    {

        return $this->belongsToMany(Course::class, 'course_user', 'user_id', 'course_id')
                ->withTimestamps(); 
    }
    public function username()
    {
        return 'id_number';
    }
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
    public function groups()
    {
        return $this->hasMany(Group::class, 'UserId','id');
    }

}
