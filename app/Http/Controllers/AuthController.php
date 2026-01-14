<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BusinessLogic\UserLogic;

class AuthController extends Controller
{
    protected $userLogic;

    public function __construct(UserLogic $userLogic)
    {
        $this->userLogic = $userLogic;
    }

    public function showLogin() {
        return view('auth.login');
    }

    public function login(Request $request) {

        $request->validate([
            'id_number' => 'required|string',
            'password'  => 'required',
        ]);

        $id_number = $request->input('id_number');
        $password = $request->input('password');

        if ($this->userLogic->attemptLogin($id_number, $password)) {

        return redirect()->route('dashboard');
        }

        return back()
            ->withErrors(['error' => 'رقم الهوية أو كلمة المرور غير صحيحة'])
            ->withInput($request->only('id_number'));
    }
}
