<?php

namespace App\BusinessLogic;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserLogic
{

     // محاولة تسجيل الدخول

    public function attemptLogin($idNumber, $password)
    {
        if (Auth::attempt(['id_number' => $idNumber, 'password' => $password])) {
            request()->session()->regenerate();
            return true;
        }
        return false;
    }


    // إنشاء مستخدم جديد

    public function storeUser($data)
    {
        return User::create([
            'full_name'     => $data['full_name'],
            'id_number'     => $data['id_number'],
            'password'      => Hash::make($data['password']),
            'date_of_birth' => $data['date_of_birth'],
            'phone_number'  => $data['phone_number'],
            'address'       => $data['address'],
            'category_id'   => $data['category_id'],
            'is_admin'      => ($data['is_admin'] == 'مسؤول') ? true : false,
            'creation_by'   => Auth::user()->full_name ?? 'System',
        ]);
    }


     // تحديث بيانات مستخدم

    public function updateUser($user, $data)
    {
        $updateData = [
            'full_name'    => $data['full_name'],
            'id_number'    => $data['id_number'],
            'phone_number' => $data['phone_number'],
            'address'      => $data['address'],
            'is_admin'     => $data['is_admin'],
            'category_id'  => $data['category_id'],
            'updated_by'   => Auth::user()->full_name ?? 'System',
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        return $user->update($updateData);
    }


     // حذف مستخدم (

    public function deleteUser($user)
    {
        $user->deleted_by = Auth::user()->full_name ?? 'System';
        $user->save();
        return $user->delete();
    }
}
