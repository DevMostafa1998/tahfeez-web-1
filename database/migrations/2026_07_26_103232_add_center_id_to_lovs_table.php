<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $tableName = Schema::hasTable('lov') ? 'lov' : 'lovs';

        Schema::table($tableName, function (Blueprint $table) use ($tableName) {
            if (!Schema::hasColumn($tableName, 'center_id')) {
                $table->unsignedBigInteger('center_id')->nullable()->after('id');
            }
        });

        $centersTable = Schema::hasTable('centers') ? 'centers' : 'center';
        $firstCenterId = DB::table($centersTable)->value('id');

        if (!$firstCenterId) {
            $firstCenterId = DB::table($centersTable)->insertGetId([
                'name' => 'المركز الرئيسي',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        DB::table($tableName)
            ->whereNull('center_id')
            ->update(['center_id' => $firstCenterId]);

        try {
            Schema::table($tableName, function (Blueprint $table) use ($centersTable) {
                $table->foreign('center_id', 'fk_lov_center_id_custom')
                      ->references('id')
                      ->on($centersTable)
                      ->onDelete('cascade');
            });
        } catch (\Exception $e) {
        }
    }

    public function down(): void
    {
        $tableName = Schema::hasTable('lov') ? 'lov' : 'lovs';

        Schema::table($tableName, function (Blueprint $table) use ($tableName) {
            if (Schema::hasColumn($tableName, 'center_id')) {
                try {
                    $table->dropForeign('fk_lov_center_id_custom');
                } catch (\Exception $e) {
                }
                $table->dropColumn('center_id');
            }
        });
    }
};
