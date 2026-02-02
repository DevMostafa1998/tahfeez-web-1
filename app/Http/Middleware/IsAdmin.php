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
    if (Auth::check() && Auth::user()->is_admin == 1) {
        return $next($request);
    }

    if ($request->expectsJson()) {
        return response()->json(['error' => 'Unauthorised.'], 403);
    }
    return response('
        <div style="text-align: center; margin-top: 50px; font-family: sans-serif;" dir="rtl">
            <h1 style="color: red;">403</h1>
            <h2>غير مصرح لك بدخول هذه الصفحة</h2>
            <br>
            <a href="javascript:history.back()"
            style="background: #007bff; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;">
            الرجوع للصفحة السابقة
            </a>
        </div>
    ', 403);}
}
