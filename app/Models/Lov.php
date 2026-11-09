<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lov extends Model
{
    protected $table = 'lov';
    protected $fillable = ['type', 'description', 'is_valid', 'center_id'];

    public static function getOptions($type)
    {
        return self::where('type', $type)
            ->where('is_valid', true)
            ->get(['id', 'description']);
    }
    public function center()
    {
        return $this->belongsTo(Center::class, 'center_id');
    }
}
