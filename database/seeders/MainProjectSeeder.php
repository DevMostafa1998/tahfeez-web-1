<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MainProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. إنشاء الفئات
        $adminCat = Category::create(['name' => 'مسؤول']);
        $teacherCat = Category::create(['name' => 'محفظ']);

        // 2. إنشاء حساب المسؤول (Admin)
        User::create([
            'full_name'     => 'admin ',
            'id_number'     => '123456789',
            'password'      => '123456',
            'date_of_birth' => '1990-01-01',
            'phone_number'  => '0590000000',
            'address'       => 'غزة',
            'is_admin'      => true,         //  مسؤول
            'category_id'   => $adminCat->id,
        ]);



        $this->command->info('تم إنشاء المسؤول بنجاح!');
    }
}
