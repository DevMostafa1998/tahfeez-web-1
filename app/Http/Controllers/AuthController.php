<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\BusinessLogic\UserLogic;

class AuthController extends Controller
{
    protected $userLogic;

    // نقوم بحقن كلاس الـ Logic داخل الـ Controller
    public function __construct(UserLogic $userLogic)
    {
        $this->userLogic = $userLogic;
    }

    // عرض صفحة الدخول
    public function showLogin() {
        return view('auth.login');
    }

    // تنفيذ عملية الدخول
    public function login(Request $request) {
        $id_number = $request->input('id_number');
        $password = $request->input('password');

        // مناداة البرمجة التي كتبتها في BusinessLogic
        if ($this->userLogic->attemptLogin($id_number, $password)) {
            return redirect()->intended('/dashboard');
        }

        return back()->withErrors(['error' => 'رقم الهوية أو كلمة المرور غير صحيحة']);
    }
}
