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
        $updateData = [
            'full_name'    => $data['full_name'] ?? $user->full_name,
            'id_number'    => $data['id_number'] ?? $user->id_number,
            'phone_number' => $data['phone_number'] ?? $user->phone_number,
            'address'      => $data['address'] ?? $user->address,
            'category_id'  => $data['category_id'] ?? $user->category_id,
            'updated_by'   => Auth::user()->full_name ?? 'System',
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

        if (isset($data['is_admin'])) {
            $updateData['is_admin'] = ($data['is_admin'] == 'مسؤول' || $data['is_admin'] == '1') ? true : false;
        }

        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        $user->update($updateData);

        if (isset($data['courses']) && is_array($data['courses'])) {
            $user->courses()->sync($data['courses']);
        } elseif (isset($data['update_courses_only']) || isset($data['courses'])) {
            $user->courses()->detach();
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
