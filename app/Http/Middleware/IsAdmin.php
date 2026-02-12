<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && (Auth::user()->is_admin == 1 || Auth::user()->is_admin_rouls == 1)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['error' => 'Unauthorised.'], 403);
        }

        return response('
            <div style="text-align: center; margin-top: 50px; font-family: sans-serif;" dir="rtl">
                <h1 style="color: red;">403</h1>
                <h2 style="color: #333;">عذراً، هذه المنطقة مخصصة للمسؤولين فقط</h2>
                <p style="color: #666;">يبدو أنك لا تملك صلاحية الوصول لهذه الصفحة، تواصل مع الإدارة إذا كنت تعتقد أن هذا خطأ.</p>
                <br>
                <a href="javascript:history.back()"
                style="background: #007bff; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
                الرجوع للصفحة السابقة
                </a>
            </div>
        ', 403);
    }
}
