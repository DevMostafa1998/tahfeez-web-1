<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // إضافة العمود
        Schema::table('surahs', function (Blueprint $table) {
            $table->decimal('pages_count', 5, 2)->after('verses_count')->default(0);
        });

        // مصفوفة بيانات عدد الصفحات لكل سورة (ترتيبها حسب رقم السورة 1-114)
        $pages = [
            1 => 1, 2 => 48, 3 => 27, 4 => 29, 5 => 22, 6 => 23, 7 => 26, 8 => 10, 9 => 21, 10 => 11,
            11 => 12, 12 => 12, 13 => 6, 14 => 7, 15 => 5, 16 => 15, 17 => 12, 18 => 12, 19 => 7, 20 => 10,
            21 => 10, 22 => 10, 23 => 8, 24 => 10, 25 => 7, 26 => 9, 27 => 9, 28 => 11, 29 => 7, 30 => 6,
            31 => 4, 32 => 3, 33 => 9, 34 => 6, 35 => 6, 36 => 6, 37 => 7, 38 => 5, 39 => 8, 40 => 9,
            41 => 6, 42 => 6, 43 => 7, 44 => 3, 45 => 4, 46 => 4, 47 => 4, 48 => 4, 49 => 2, 50 => 3,
            51 => 3, 52 => 2, 53 => 3, 54 => 3, 55 => 3, 56 => 3, 57 => 4, 58 => 3, 59 => 3, 60 => 2,
            61 => 1.5, 62 => 1.5, 63 => 1.5, 64 => 2, 65 => 2, 66 => 2, 67 => 2, 68 => 2, 69 => 2, 70 => 2,
            71 => 1.5, 72 => 2, 73 => 1.5, 74 => 2, 75 => 1, 76 => 2, 77 => 1.5, 78 => 1.5, 79 => 1.5, 80 => 1,
            81 => 1, 82 => 1, 83 => 2, 84 => 1, 85 => 1, 86 => 0.5, 87 => 0.5, 88 => 1, 89 => 1.5, 90 => 1,
            91 => 0.5, 92 => 1, 93 => 0.5, 94 => 0.3, 95 => 0.3, 96 => 0.5, 97 => 0.3, 98 => 1, 99 => 0.5, 100 => 0.5,
            101 => 0.5, 102 => 0.5, 103 => 0.3, 104 => 0.3, 105 => 0.3, 106 => 0.3, 107 => 0.3, 108 => 0.2, 109 => 0.3, 110 => 0.2,
            111 => 0.3, 112 => 0.2, 113 => 0.2, 114 => 0.2
        ];

        // تحديث البيانات
        foreach ($pages as $number => $count) {
            DB::table('surahs')->where('number', $number)->update(['pages_count' => $count]);
        }
    }

    public function down(): void
    {
        Schema::table('surahs', function (Blueprint $table) {
            $table->dropColumn('pages_count');
        });
    }
};
