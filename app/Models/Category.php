<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Category extends Model
{
   use HasFactory;

    protected $table = 'categorie';
    protected $fillable = ['name', 'center_id'];
    public function center()
        {
            return $this->belongsTo(Center::class);
        }
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'category_id');
    }
}
