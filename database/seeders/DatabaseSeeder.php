<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $categoryId = DB::table('categorie')->insertGetId([
            'name' => 'عام',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        User::factory()->create([
            'full_name'     => 'admin',
            'id_number'     => '123456789',
            'password'      => '123456',
            'is_admin'      => true,
            'gender'        => 'male',
            'category_id'   => $categoryId,
            'date_of_birth' => '1990-01-01',
            'birth_place'   => 'Gaza',
            'phone_number'  => '0599123456',
            'address'       => 'دير البلح',
        ]);
    }
}
