<?php

namespace App\BusinessLogic;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserLogic
{
    public function attemptLogin($idNumber, $password)
    {
        if (Auth::attempt(['id_number' => $idNumber, 'password' => $password])) {
            request()->session()->regenerate();
            return true;
        }
        return false;
    }

    public function storeUser($data)
    {
    $isAdminMain = (isset($data['is_admin']) && ($data['is_admin'] == '1' || $data['is_admin'] == 'مسؤول')) ? true : false;

    $isAdminRouls = null;

    if (!$isAdminMain) {
        $isAdminRouls = isset($data['is_admin_rouls']) ? true : false;
    }
        $user = User::create([
            'full_name'     => $data['full_name'],
            'id_number'     => $data['id_number'],
            'password'      => Hash::make($data['password']),
            'date_of_birth' => $data['date_of_birth'],
            'phone_number'  => $data['phone_number'],
            'address'       => $data['address'],
            'category_id'   => $data['category_id'],
            'is_admin'      => $isAdminMain,
            'is_admin_rouls'=> $isAdminRouls,
          'creation_by'   => Auth::user()->full_name ?? 'System',
            'creation_by'   => Auth::user()->full_name ?? 'System',
            'birth_place'     => $data['birth_place'] ?? null,
            'wallet_number'   => $data['wallet_number'] ?? null,
            'whatsapp_number' => $data['whatsapp_number'] ?? null,
            'qualification'   => $data['qualification'] ?? null,
            'specialization'  => $data['specialization'] ?? null,
            'parts_memorized' => $data['parts_memorized'] ?? 0,
            'mosque_name'     => $data['mosque_name'] ?? null,
            'is_displaced'    => $data['is_displaced'] ?? false,
            'gender'          => $data['gender'],
        ]);

        if (isset($data['courses']) && is_array($data['courses'])) {
            $user->courses()->attach($data['courses']);
        }

        return $user;
    }

    public function updateUser($user, $data)
    {
        // 1. تجهيز مصفوفة البيانات الأساسية
        $updateData = [
            'full_name'       => $data['full_name'] ?? $user->full_name,
            'id_number'       => $data['id_number'] ?? $user->id_number,
            'phone_number'    => $data['phone_number'] ?? $user->phone_number,
            'address'         => $data['address'] ?? $user->address,
            'category_id'     => $data['category_id'] ?? $user->category_id,
            'updated_by'      => Auth::user()->full_name ?? 'System',
            'birth_place'     => $data['birth_place'] ?? $user->birth_place,
            'wallet_number'   => $data['wallet_number'] ?? $user->wallet_number,
            'whatsapp_number' => $data['whatsapp_number'] ?? $user->whatsapp_number,
            'qualification'   => $data['qualification'] ?? $user->qualification,
            'specialization'  => $data['specialization'] ?? $user->specialization,
            'parts_memorized' => $data['parts_memorized'] ?? $user->parts_memorized,
            'mosque_name'     => $data['mosque_name'] ?? $user->mosque_name,
            'is_displaced'    => $data['is_displaced'] ?? $user->is_displaced,
            'gender'          => $data['gender'] ?? $user->gender,
        ];

        // 2. تحديث كلمة المرور فقط في حال تم إدخالها
        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        if (isset($data['is_admin'])) {
            $isAdminMain = ($data['is_admin'] == '1' || $data['is_admin'] == 'مسؤول');
            $updateData['is_admin'] = $isAdminMain ? 1 : 0;

            if ($isAdminMain) {
                $updateData['is_admin_rouls'] = null;
            } else {
                $updateData['is_admin_rouls'] = (isset($data['is_admin_rouls']) && $data['is_admin_rouls'] == 1) ? 1 : 0;
            }
        }

        $user->update($updateData);

        if (isset($data['courses'])) {
            $user->courses()->sync($data['courses']);
        }

        return $user;
    }

    public function deleteUser($user)
    {
        if ($user->id === 1) {
            return false;
            abort(403, 'لا يمكن حذف الحساب الرئيسي للمنظومة');
        }
        $user->deleted_by = Auth::user()->full_name ?? 'System';
        $user->save();
        return $user->delete();
    }
}
