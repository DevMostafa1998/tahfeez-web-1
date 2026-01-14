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
        // 1. التحقق من البيانات (خطوة مهمة جداً)
        $request->validate([
            'id_number' => 'required|string',
            'password'  => 'required',
        ]);

        $id_number = $request->input('id_number');
        $password = $request->input('password');

        // 2. محاولة تسجيل الدخول عبر الـ Business Logic
        if ($this->userLogic->attemptLogin($id_number, $password)) {
            // 3. التوجيه إلى اسم الـ route المسمى 'dashboard'
            return redirect()->route('dashboard');
        }

        // 4. في حال الفشل: العودة مع رسالة خطأ والحفاظ على رقم الهوية المدخل
        return back()
            ->withErrors(['error' => 'رقم الهوية أو كلمة المرور غير صحيحة'])
            ->withInput($request->only('id_number'));
    }
}
