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
        return User::create([
            'full_name'     => $data['full_name'],
            'id_number'     => $data['id_number'],
            'password'      => $data['password'],
            'date_of_birth' => $data['date_of_birth'],
            'phone_number'  => $data['phone_number'],
            'address'       => $data['address'],
            'category_id'   => $data['category_id'],
            'is_admin'      => $data['is_admin'] ?? false,
            'creation_by'   => Auth::user()->full_name ?? 'System',
        ]);
    }
}
