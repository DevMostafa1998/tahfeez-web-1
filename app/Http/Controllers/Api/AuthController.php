<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. التحقق من المدخلات
        $request->validate([
            'id_number' => 'required',
            'password' => 'required',
        ]);

        // 2. البحث عن المحفظ برقم الهوية
        $user = User::where('id_number', $request->id_number)->first();

        // 3. التحقق من كلمة المرور
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'بيانات الدخول غير صحيحة'
            ], 401);
        }

        // 4. توليد التوكن
        $token = $user->createToken('mobile_app_token')->plainTextToken;

        // 5. إرجاع الرد لتطبيق الفلاتر
        return response()->json([
            'status' => 'success',
            'token' => $token,
            'user' => $user
        ]);
    }
}
