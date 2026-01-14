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
        // 1. إنشاء الفئات (بما أنك حذفت الـ description)
        $adminCat = Category::create(['name' => 'مسؤول']);
        $teacherCat = Category::create(['name' => 'محفظ']);

        // 2. إنشاء حساب المسؤول (Admin)
        User::create([
            'full_name'     => 'admin ',
            'id_number'     => '123456789', // هذا ما ستستخدمه في الدخول
            'password'      => '123456',     // سيتم تشفيره تلقائياً من الموديل
            'date_of_birth' => '1990-01-01',
            'phone_number'  => '0590000000',
            'address'       => 'غزة',
            'is_admin'      => true,         // هو مسؤول
            'category_id'   => $adminCat->id,
        ]);

        // 3. إنشاء حساب محفظ تجريبي
        User::create([
            'full_name'     => 'احمد المحفظ',
            'id_number'     => '987654321', // رقم هوية المحفظ
            'password'      => '111111',
            'date_of_birth' => '1995-05-05',
            'phone_number'  => '0591111111',
            'address'       => 'خانيونس',
            'is_admin'      => false,        // ليس مسؤولاً بل محفظ
            'category_id'   => $teacherCat->id,
        ]);

        $this->command->info('تم إنشاء المسؤول والمحفظ بنجاح!');
    }
}
