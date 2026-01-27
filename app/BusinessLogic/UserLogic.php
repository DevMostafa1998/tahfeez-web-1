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
        $user = User::create([
            'full_name'     => $data['full_name'],
            'id_number'     => $data['id_number'],
            'password'      => Hash::make($data['password']),
            'date_of_birth' => $data['date_of_birth'],
            'phone_number'  => $data['phone_number'],
            'address'       => $data['address'],
            'category_id'   => $data['category_id'],
            'is_admin'      => (isset($data['is_admin']) && ($data['is_admin'] == '1' || $data['is_admin'] == 'مسؤول')) ? true : false,
            'creation_by'   => Auth::user()->full_name ?? 'System',
        ]);

        // ربط الدورات
        if (isset($data['courses']) && is_array($data['courses'])) {
            $user->courses()->attach($data['courses']);
        }

        return $user;
    }

    public function updateUser($user, $data)
    {
        $updateData = [
            'full_name'    => $data['full_name'],
            'id_number'    => $data['id_number'],
            'phone_number' => $data['phone_number'],
            'address'      => $data['address'],
            'category_id'  => $data['category_id'],
            'is_admin'     => (isset($data['is_admin']) && ($data['is_admin'] == '1' || $data['is_admin'] == 'مسؤول')) ? true : false,
            'updated_by'   => Auth::user()->full_name ?? 'System',
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);

        // تحديث الدورات (Sync يحذف القديم ويضيف الجديد)
        if (isset($data['courses']) && is_array($data['courses'])) {
            $user->courses()->sync($data['courses']);
        } else {
            $user->courses()->detach(); // حذف الكل إذا لم يتم اختيار شيء
        }

        return $user;
    }

    public function deleteUser($user)
    {
        $user->deleted_by = Auth::user()->full_name ?? 'System';
        $user->save();
        return $user->delete();
    }
}
