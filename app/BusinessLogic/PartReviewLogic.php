<?php

namespace App\BusinessLogic;

use App\Models\Message;
use App\Models\PartReviewRequest;
use App\Models\Student;
use App\Models\User;
use App\Notifications\SystemUpdateNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PartReviewLogic
{
    public static function milestoneList(): array
    {
        $raw = \Illuminate\Support\Facades\DB::table('settings')
            ->where('key', 'part_review_milestones')
            ->value('value');

        $list = $raw ? json_decode($raw, true) : null;
        if (is_array($list) && !empty($list)) {
            $list = array_values(array_unique(array_map('intval', $list)));
            sort($list);
            return $list;
        }

        $out = [];
        for ($n = 3; $n <= 30; $n += 3) {
            $out[] = $n;
        }
        return $out;
    }

    /**
     * المراحل التي يتحققها الطالب عند بلوغ $parts جزءاً
     */
    public function milestones(int $parts): array
    {
        return array_values(array_filter(
            self::milestoneList(),
            fn ($m) => $m <= min($parts, 30)
        ));
    }

    /**
     * المرحلة السابقة في القائمة المُعدّة (أو null للأولى)
     */
    public static function previousMilestone(int $milestone): ?int
    {
        $prev = null;
        foreach (self::milestoneList() as $m) {
            if ($m < $milestone) {
                $prev = $m;
            }
        }
        return $prev;
    }

    public function computeParts(Student $student): int
    {
        $totalPages = $this->pagesForStudent($student->id);
        return (int) floor(min($totalPages / 20.13, 30));
    }

    public function computePartsMap(array $studentIds): array
    {
        if (empty($studentIds)) {
            return [];
        }

        $rows = DB::table('student_daily_memorizations')
            ->select('student_id', 'sura_name',
                DB::raw('MIN(verses_from) as min_v'),
                DB::raw('MAX(verses_to) as max_v'))
            ->whereIn('student_id', $studentIds)
            ->groupBy('student_id', 'sura_name')
            ->get();

        $surahs = DB::table('surahs')->get()->keyBy('name_ar');
        $pages = [];

        foreach ($rows as $r) {
            $surah = $surahs->get($r->sura_name);
            if (!$surah) {
                continue;
            }
            $totalVerses = $surah->verses_count ?? 1;
            $pagesInSurah = $surah->pages_count ?? ($totalVerses / 12);
            $memorized = max(0, ((int) $r->max_v - (int) $r->min_v) + 1);
            $pages[$r->student_id] = ($pages[$r->student_id] ?? 0)
                + ($memorized / $totalVerses) * $pagesInSurah;
        }

        $result = [];
        foreach ($studentIds as $sid) {
            $result[$sid] = (int) floor(min(($pages[$sid] ?? 0) / 20.13, 30));
        }
        return $result;
    }

    /**
     * إجمالي الصفحات المكافئة للطالب من سجلات الحفظ
     */
    private function pagesForStudent(int $studentId): float
    {
        $rows = DB::table('student_daily_memorizations')
            ->select('sura_name',
                DB::raw('MIN(verses_from) as min_v'),
                DB::raw('MAX(verses_to) as max_v'))
            ->where('student_id', $studentId)
            ->groupBy('sura_name')
            ->get();

        $surahs = DB::table('surahs')->get()->keyBy('name_ar');
        $total = 0.0;
        foreach ($rows as $r) {
            $surah = $surahs->get($r->sura_name);
            if (!$surah) {
                continue;
            }
            $totalVerses = $surah->verses_count ?? 1;
            $pagesInSurah = $surah->pages_count ?? ($totalVerses / 12);
            $memorized = max(0, ((int) $r->max_v - (int) $r->min_v) + 1);
            $total += ($memorized / $totalVerses) * $pagesInSurah;
        }
        return $total;
    }

     */
    public function syncStudent(Student $student, ?int $recorderId = null): int
    {
        try {
            $parts = $this->computeParts($student);

            if ($parts !== (int) ($student->parts_memorized ?? 0)) {
                $student->parts_memorized = $parts;
                $student->save();
            }

            if ($parts < 3) {
                return 0;
            }

            $existing = PartReviewRequest::where('student_id', $student->id)
                ->pluck('milestone')
                ->all();

            $missing = array_values(array_diff($this->milestones($parts), $existing));
            if (empty($missing)) {
                return 0;
            }

            // المحفظ = معلم آخر مجموعة للطالب (أو من سجّل إن لم توجد مجموعة)
            $keeperId = $student->groups->first()?->teacher?->id ?? $recorderId;
            $centerId = $student->center_id;

            foreach ($missing as $milestone) {
                PartReviewRequest::create([
                    'student_id' => $student->id,
                    'keeper_id'  => $keeperId,
                    'center_id'  => $centerId,
                    'milestone'  => $milestone,
                    'status'     => 'pending',
                ]);
            }

            $this->notifyKeeper($student, $keeperId, $missing, $centerId, $parts);

            return count($missing);
        } catch (\Exception $e) {
            Log::error('PartReview sync failed for student ' . $student->id . ': ' . $e->getMessage());
            return 0;
        }
    }

    /**
     * إشعار المحفظ: جرس تنبيهات + رسالة داخل المحادثات
     */
    private function notifyKeeper(Student $student, ?int $keeperId, array $milestones, $centerId, int $totalParts): void
    {
        if (!$keeperId) {
            return;
        }

        $keeper = User::find($keeperId);
        if (!$keeper) {
            return;
        }

        $groupName = $this->studentGroupName($student);
        $groupText = $groupName ? " – مجموعة ({$groupName})" : '';
        // المراحل الجديدة تُعرض كنطاق واحد متصل: من أول جزء بعد المرحلة السابقة إلى آخر مرحلة
        $firstMilestone = $milestones[0];
        $lastMilestone = end($milestones);
        $prevBefore = self::previousMilestone($firstMilestone);
        $rangeStart = ($prevBefore ?? 0) + 1;
        $stageText = $rangeStart <= 1
            ? "الأجزاء 1 إلى {$lastMilestone}"
            : "من الجزء {$rangeStart} إلى الجزء {$lastMilestone}";

        $title = 'مراجعة الأجزاء مطلوبة';
        $body = "الطالب ({$student->full_name}){$groupText} أتم تسميع إجمالي {$totalParts} أجزاء "
            . "(المراحل: {$stageText}) استعداداً للاختبار الرسمي. "
            . "يرجى مراجعتها وإعادة تسميعها ثم تأكيد الجاهزية.";

        // 1) إشعار جرس التنبيهات
        $keeper->notify(new SystemUpdateNotification($title, $body));

        // 2) رسالة داخل المحادثات (من مسؤول المركز أو أول أدمن متاح)
        $senderId = null;
        if ($centerId) {
            $senderId = User::where('is_admin', 1)->where('center_id', $centerId)->value('id');
        }
        $senderId = $senderId ?? User::where('is_admin', 1)->value('id') ?? $keeper->id;

        Message::create([
            'sender_id'   => $senderId,
            'receiver_id' => $keeper->id,
            'body'        => $body,
            'center_id'   => $centerId,
            'is_read'     => false,
        ]);
    }

    /**
     * اسم آخر مجموعة ينتمي إليها الطالب
     */
    private function studentGroupName(Student $student): ?string
    {
        $group = $student->groups->first();
        return $group?->GroupName;
    }

    /**
     * تأكيد المحفظ بانتهاء المراجعة → إشعار مسؤول/مسؤولي المركز برسالة داخل المحادثات
     */
    public function completeReview(PartReviewRequest $review): void
    {
        $student = $review->student;
        $senderId = $review->keeper_id;

        $review->update([
            'status'       => 'completed',
            'completed_by' => $senderId,
            'completed_at' => now(),
        ]);

        if (!$senderId || !$student) {
            return;
        }

        $admins = User::where('is_admin', 1)->whereNull('deleted_at');
        if ($review->center_id) {
            $admins->where('center_id', $review->center_id);
        }
        $adminList = $admins->get();

        if ($adminList->isEmpty() && $review->center_id) {
            $adminList = User::where('is_admin', 1)->whereNull('deleted_at')->get();
        }

        $groupName = $this->studentGroupName($student);
        $groupText = $groupName ? " – مجموعة ({$groupName})" : '';
        $body = "الطالب ({$student->full_name}){$groupText} أنهى مراجعة {$review->range_label} "
            . "وهو الآن جاهز لإجراء الاختبار الرسمي.";

        foreach ($adminList as $admin) {
            Message::create([
                'sender_id'   => $senderId,
                'receiver_id' => $admin->id,
                'body'        => $body,
                'center_id'   => $review->center_id,
                'is_read'     => false,
            ]);
        }
    }
}
