<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. تعطيل (حذف) الـ Triggers مؤقتاً للسماح بالتحديث
        DB::unprepared("DROP TRIGGER IF EXISTS prevent_surahs_update");
        DB::unprepared("DROP TRIGGER IF EXISTS prevent_surahs_delete");

        // 2. إضافة الأعمدة الجديدة للجدول
        Schema::table('surahs', function (Blueprint $table) {
            $table->integer('verses_count')->after('name_en')->nullable();
            $table->integer('juz_number')->after('verses_count')->nullable();
        });

        // 3. قائمة البيانات المراد تحديثها
        $surasData = [
            [1, 7, 1],
            [2, 286, 1],
            [3, 200, 3],
            [4, 176, 4],
            [5, 120, 6],
            [6, 165, 7],
            [7, 206, 8],
            [8, 75, 9],
            [9, 129, 10],
            [10, 109, 11],
            [11, 123, 11],
            [12, 111, 12],
            [13, 43, 13],
            [14, 52, 13],
            [15, 99, 14],
            [16, 128, 14],
            [17, 111, 15],
            [18, 110, 15],
            [19, 98, 16],
            [20, 135, 16],
            [21, 112, 17],
            [22, 78, 17],
            [23, 118, 18],
            [24, 64, 18],
            [25, 77, 19],
            [26, 227, 19],
            [27, 93, 19],
            [28, 88, 20],
            [29, 69, 20],
            [30, 60, 21],
            [31, 34, 21],
            [32, 30, 21],
            [33, 73, 21],
            [34, 54, 22],
            [35, 45, 22],
            [36, 83, 22],
            [37, 182, 23],
            [38, 88, 23],
            [39, 75, 23],
            [40, 85, 24],
            [41, 54, 24],
            [42, 53, 25],
            [43, 89, 25],
            [44, 59, 25],
            [45, 37, 25],
            [46, 35, 26],
            [47, 38, 26],
            [48, 29, 26],
            [49, 18, 26],
            [50, 45, 26],
            [51, 60, 27],
            [52, 49, 27],
            [53, 62, 27],
            [54, 55, 27],
            [55, 78, 27],
            [56, 96, 27],
            [57, 29, 27],
            [58, 22, 28],
            [59, 24, 28],
            [60, 13, 28],
            [61, 14, 28],
            [62, 11, 28],
            [63, 11, 28],
            [64, 18, 28],
            [65, 12, 28],
            [66, 12, 28],
            [67, 30, 29],
            [68, 52, 29],
            [69, 52, 29],
            [70, 44, 29],
            [71, 28, 29],
            [72, 28, 29],
            [73, 20, 29],
            [74, 56, 29],
            [75, 40, 29],
            [76, 31, 29],
            [77, 50, 29],
            [78, 40, 30],
            [79, 46, 30],
            [80, 42, 30],
            [81, 29, 30],
            [82, 19, 30],
            [83, 36, 30],
            [84, 25, 30],
            [85, 22, 30],
            [86, 17, 30],
            [87, 19, 30],
            [88, 26, 30],
            [89, 30, 30],
            [90, 20, 30],
            [91, 15, 30],
            [92, 21, 30],
            [93, 11, 30],
            [94, 8, 30],
            [95, 8, 30],
            [96, 19, 30],
            [97, 5, 30],
            [98, 8, 30],
            [99, 8, 30],
            [100, 11, 30],
            [101, 11, 30],
            [102, 8, 30],
            [103, 3, 30],
            [104, 9, 30],
            [105, 5, 30],
            [106, 4, 30],
            [107, 7, 30],
            [108, 3, 30],
            [109, 6, 30],
            [110, 3, 30],
            [111, 5, 30],
            [112, 4, 30],
            [113, 5, 30],
            [114, 6, 30]
        ];

        // 4. تنفيذ التحديث لكل سورة
        foreach ($surasData as $data) {
            DB::table('surahs')
                ->where('number', $data[0])
                ->update([
                    'verses_count' => $data[1],
                    'juz_number' => $data[2]
                ]);
        }

        // DB::unprepared("
        //     CREATE TRIGGER prevent_surahs_update BEFORE UPDATE ON surahs
        //     FOR EACH ROW BEGIN
        //         SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Forbidden: Update not allowed on Surahs table';
        //     END;
        // ");

        // DB::unprepared("
        //     CREATE TRIGGER prevent_surahs_delete BEFORE DELETE ON surahs
        //     FOR EACH ROW BEGIN
        //         SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Forbidden: Delete not allowed on Surahs table';
        //     END;
        // ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('surahs', function (Blueprint $table) {
            $table->dropColumn(['verses_count', 'juz_number']);
        });
    }
};
