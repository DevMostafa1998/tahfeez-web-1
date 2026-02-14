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
        $isAdminMain = (isset($data['is_admin']) && ($data['is_admin'] == '1' || $data['is_admin'] == 'مسؤول')) ? true : false;

        $isAdminRouls = null;

        if (!$isAdminMain) {
            $isAdminRouls = isset($data['is_admin_rouls']) ? true : false;
        }
        $user = User::create([
            'full_name'     => $data['full_name'],
            'id_number'     => $data['id_number'],
            'password'      => Hash::make($data['password']),
            'date_of_birth' => $data['date_of_birth'],
            'phone_number'  => $data['phone_number'],
            'address'       => $data['address'],
            'category_id'   => $data['category_id'],
            'is_admin'      => $isAdminMain,
            'is_admin_rouls' => $isAdminRouls,
            'creation_by'   => Auth::user()->full_name ?? 'System',
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
        // 1. تجهيز مصفوفة البيانات الأساسية
        $updateData = [
            'full_name'       => $data['full_name'] ?? $user->full_name,
            'id_number'       => $data['id_number'] ?? $user->id_number,
            'phone_number'    => $data['phone_number'] ?? $user->phone_number,
            'date_of_birth'   => $data['date_of_birth'] ?? $user->date_of_birth,
            'address'         => $data['address'] ?? $user->address,
            'category_id'     => $data['category_id'] ?? $user->category_id,
            'updated_by'      => Auth::user()->full_name ?? 'System',
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

        // 2. تحديث كلمة المرور فقط في حال تم إدخالها
        if (!empty($data['password'])) {
            $updateData['password'] = Hash::make($data['password']);
        }

        if (isset($data['is_admin'])) {
            $isAdminMain = ($data['is_admin'] == '1' || $data['is_admin'] == 'مسؤول');
            $updateData['is_admin'] = $isAdminMain ? 1 : 0;

            if ($isAdminMain) {
                $updateData['is_admin_rouls'] = null;
            } else {
                $updateData['is_admin_rouls'] = (isset($data['is_admin_rouls']) && $data['is_admin_rouls'] == 1) ? 1 : 0;
            }
        }

        $user->update($updateData);
        if (isset($data['update_courses_only'])) {
            $courses = $data['courses'] ?? [];
            $user->courses()->sync($courses);
            return $user;
        }
        if (isset($data['courses'])) {
            $user->courses()->sync($data['courses']);
        }

        return $user;
    }

    public function deleteUser($user)
    {
        if ($user->id === 1) {
            return false;
            abort(403, 'لا يمكن حذف الحساب الرئيسي للمنظومة');
        }
        $user->deleted_by = Auth::user()->full_name ?? 'System';
        $user->save();
        return $user->delete();
    }
    public function getUsersForDataTable($request)
    {
        $draw = $request->get('draw');
        $start = $request->get('start');
        $length = $request->get('length');
        $search = $request->get('search')['value'];

        // جلب معلومات الترتيب من الطلب
        $orderColumnIndex = $request->get('order')[0]['column'] ?? 0;
        $orderDirection = $request->get('order')[0]['dir'] ?? 'desc';

        // خريطة الأعمدة لربط كود الـ JavaScript بأسماء الحقول في قاعدة البيانات
        $columns = [
            0 => 'full_name',
            1 => 'id_number',
            2 => 'phone_number',
            3 => 'gender',
            4 => 'category_id', // ربط التصنيف بمعرفه
            5 => 'is_admin'
        ];

        $query = User::with(['courses', 'category']);

        $totalRecords = User::count();

        // 1. منطق البحث العام
        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%$search%")
                    ->orWhere('id_number', 'like', "%$search%")
                    ->orWhere('phone_number', 'like', "%$search%");
            });
        }

        // 2. منطق الترتيب (Sorting) - هذا ما يحل مشكلة الأسهم في رأس الجدول
        $orderBy = $columns[$orderColumnIndex] ?? 'id';
        $query->orderBy($orderBy, $orderDirection);

        $filteredRecords = $query->count();

        $users = $query->skip($start)
            ->take($length)
            ->get();

        $data = [];
        foreach ($users as $user) {
            $data[] = [
                'full_name' => '<strong>' . $user->full_name . '</strong>',
                'id_number' => '<span class="badge bg-light text-dark border px-4 py-2 fw-bold id-badge">' . $user->id_number . '</span>',
                'phone_number' => '<span dir="ltr" class="fw-bold text-primary-emphasis">' . $user->phone_number . '</span>',
                'gender' => $this->renderGenderBadge($user),
                'category' => $user->category->name ?? '---',
                'role' => $this->renderRoleBadge($user),
                'courses_count' => (!$user->is_admin) ? '<span class="badge bg-warning text-dark rounded-pill px-3">' . $user->courses->count() . ' دورات</span>' : '--',
                'actions' => $this->renderActions($user)
            ];
        }

        return response()->json([
            "draw" => intval($draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $filteredRecords,
            "data" => $data
        ]);
    }

    // دوال مساعدة لتنسيق HTML داخل الـ Logic (للحفاظ على نظافة الكود)
    private function renderGenderBadge($user)
    {
        if ($user->gender == 'male' || $user->gender == 'ذكر') {
            return '<span class="badge bg-blue-subtle text-primary border px-3"><i class="bi bi-person-fill ms-1"></i> ذكر</span>';
        }
        return '<span class="badge bg-pink-subtle text-danger border px-3"><i class="bi bi-person ms-1"></i> أنثى</span>';
    }

    private function renderRoleBadge($user)
    {
        $bgColor = $user->is_admin ? 'bg-primary' : 'bg-success';

        $html = '<span class="badge rounded-pill ' . $bgColor . ' px-3 py-1" style="font-size: 0.85rem;">';

        if ($user->is_admin) {
            $html .= '<i class="bi bi-shield-check me-1"></i> مسؤول';
        } elseif ($user->is_admin_rouls) {
            $html .= '<i class="fas fa-user-shield me-1" style="font-size: 0.8rem;"></i> محفظ';
        } else {
            $html .= '<i class="bi bi-person me-1"></i> محفظ';
        }

        $html .= '</span>';
        return $html;
    }

    private function renderActions($user)
    {
        $btns = '<div class="d-flex justify-content-center gap-2">';

        // زر الدورات
        if (!$user->is_admin) {
            $btns .= '<button class="btn btn-sm btn-outline-info rounded-circle action-btn course-btn"
                    data-user-id="' . $user->id . '" data-user-name="' . $user->full_name . '"
                    data-user-courses="' . json_encode($user->courses->pluck('id')) . '"><i class="bi bi-journal-plus"></i></button>';
        }

        // زر العرض
        $btns .= '<a href="' . route('teachers.show', $user->id) . '" class="btn btn-sm btn-outline-primary rounded-circle action-btn"><i class="bi bi-eye"></i></a>';

        // زر التعديل والحذف
        if ($user->id !== 1) {
            // ملاحظة: التعديل عبر المودال يتطلب آلية Dynamic Modal أو استخدام صفحة Edit منفصلة
            $btns .= '<button type="button" onclick="editUser(' . $user->id . ')" class="btn btn-sm btn-outline-warning rounded-circle action-btn"><i class="bi bi-pencil-square"></i></button>';
            $btns .= '<button type="button" onclick="confirmDelete(' . $user->id . ')" class="btn btn-sm btn-outline-danger rounded-circle action-btn"><i class="bi bi-trash3"></i></button>';
        }

        $btns .= '</div>';
        return $btns;
    }
}
