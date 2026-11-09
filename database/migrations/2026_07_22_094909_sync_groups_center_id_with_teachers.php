<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Group;
use App\Models\User;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $groups = Group::all();

        foreach ($groups as $group) {
            if ($group->UserId) {
                $teacher = User::find($group->UserId);
                if ($teacher && $teacher->center_id) {
                    $group->center_id = $teacher->center_id;
                    $group->save();
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
