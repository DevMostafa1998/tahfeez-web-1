<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\DB;

// use Illuminate\Container\Attributes\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
public function index()
{
    $userId = Auth::id();

    $teacher = User::with(['courses', 'groups' => function($query) {
        $query->withCount('students');
    }])->findOrFail($userId);

    return view('profile.index', [
        'teacher' => $teacher
    ]);
}
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {
        $user = $request->user();

        // جلب اسم الفئة من جدول الفئات بناءً على المعرف
        $categoryName = DB::table('categorie')
            ->where('id', $user->category_id)
            ->value('name');

        return view('profile.edit', compact('user', 'categoryName'));
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $rules = [
            'full_name'     => ['sometimes', 'required', 'string', 'max:255'],
            'id_number'     => ['sometimes', 'required', 'string', Rule::unique('user')->ignore($user->id)],
            'date_of_birth' => ['sometimes', 'required', 'date'],
            'phone_number'  => ['sometimes', 'required', 'string', 'max:20'],
            'address'       => ['sometimes', 'required', 'string', 'max:500'],
            'password'      => ['nullable', 'confirmed', 'min:8'],
            'birth_place'     => ['nullable', 'string', 'max:255'],
            'wallet_number'   => ['nullable', 'string', 'max:50'],
            'whatsapp_number' => ['nullable', 'string', 'max:20'],
            'qualification'   => ['nullable', 'string', 'max:255'],
            'specialization'  => ['nullable', 'string', 'max:255'],
            'parts_memorized' => ['nullable', 'numeric', 'min:0', 'max:30'],
            'mosque_name'     => ['nullable', 'string', 'max:255'],
            'is_displaced'    => ['nullable', 'boolean'],
        ];
        $messages = [
            'id_number.unique'       => 'رقم الهوية هذا مسجل مسبقاً في النظام.',
            'password.confirmed'     => 'كلمة المرور غير متطابقة',
            'password.min'           => 'كلمة المرور يجب أن لا تقل عن 8 أحرف.',
        ];
        $validated = $request->validate($rules, $messages);
        if ($request->has('full_name')) {
            $user->full_name = $request->full_name;
            $user->phone_number = $request->phone_number;
            $user->address = $request->address;
            $user->id_number = $request->id_number;
            $user->date_of_birth = $request->date_of_birth;
        }

        // تحديث كلمة المرور إذا تم إدخالها
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->updated_by = Auth::user()->full_name;
        $user->save();
        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'تم تحديث البيانات بنجاح!'
            ]);
        }
        return redirect()->back()->with('status', 'تم التحديث بنجاح!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
