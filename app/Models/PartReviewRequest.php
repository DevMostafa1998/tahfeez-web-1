<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PartReviewRequest extends Model
{
    protected $fillable = [
        'student_id',
        'keeper_id',
        'center_id',
        'milestone',
        'status',
        'completed_by',
        'completed_at',
    ];

    protected $casts = [
        'milestone'    => 'integer',
        'completed_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function keeper()
    {
        return $this->belongsTo(User::class, 'keeper_id');
    }

    // نطاق الأجزاء الجديدة في هذه المرحلة: من أول جزء بعد المرحلة السابقة إلى الحالي
    public function getRangeLabelAttribute(): string
    {
        $prev = \App\BusinessLogic\PartReviewLogic::previousMilestone($this->milestone);
        $start = ($prev ?? 0) + 1;
        return $start > 1
            ? "من الجزء {$start} إلى الجزء {$this->milestone}"
            : "الأجزاء 1 إلى {$this->milestone}";
    }
}
