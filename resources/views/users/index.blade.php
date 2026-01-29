@extends('layouts.app')
@section('title', 'إدارة المستخدمين')

@section('content')
<div class="container-fluid p-4" dir="rtl">

    {{-- 1. تنبيه النجاح (بسيط ومباشر) --}}
    @if (session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    icon: 'success',
                    title: 'تمت العملية بنجاح',
                    text: "{{ session('success') }}",
                    timer: 3000,
                    showConfirmButton: false
                });
            });
        </script>
    @endif

    {{-- 2. رأس الصفحة --}}
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-primary fw-bold mb-0">
            <i class="bi bi-people-fill me-2"></i>إدارة المستخدمين
        </h1>
        <a href="{{ route('user.create') }}" class="btn btn-primary px-4 shadow-sm fw-bold rounded-pill">
            <i class="bi bi-plus-lg ms-1"></i> مستخدم جديد
        </a>
    </div>

    {{-- 3. جدول البيانات --}}
    <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="py-3">#</th>
                        <th class="text-start ps-4">اسم المستخدم</th>
                        <th>رقم الهوية</th>
                        <th>رقم الجوال</th>
                        <th>التصنيف</th>
                        <th>الصلاحية</th>
                        <th>الدورات</th>
                        <th style="width: 150px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="text-muted fw-bold">
                            {{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}
                        </td>
                        <td class="text-start ps-4"><strong>{{ $user->full_name }}</strong></td>
                        <td>
                            <span class="badge bg-light text-dark border px-3 py-2 fs-7 fw-bold" style="letter-spacing: 1px;">
                                {{ $user->id_number }}
                            </span>
                        </td>
                        <td dir="ltr">{{ $user->phone_number }}</td>
                        <td>
                            <span class="badge rounded-pill bg-info bg-opacity-10 text-info border border-info">
                                {{ $user->category->name ?? '---' }}
                            </span>
                        </td>
                        <td>
                            <span class="badge rounded-pill {{ $user->is_admin ? 'bg-primary' : 'bg-success' }}">
                                {{ $user->is_admin ? 'مسؤول' : 'محفظ' }}
                            </span>
                        </td>
                        <td>
                            @if(!$user->is_admin)
                                <span class="badge bg-warning text-dark rounded-pill shadow-sm">
                                    {{ optional($user->courses)->count() ?? 0 }} دورات
                                </span>
                            @else
                                <span class="text-muted small">--</span>
                            @endif
                        </td>
                        
                        <td style="width: 150px;">
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; justify-items: center; align-items: center;">

                            <div>
                                @if(!$user->is_admin)
                                    <button class="btn btn-sm btn-outline-info rounded-circle action-btn course-btn"
                                            data-bs-toggle="modal" data-bs-target="#courseUserModal"
                                            data-user-id="{{ $user->id }}"
                                            data-user-name="{{ $user->full_name }}"
                                            data-user-courses="{{ json_encode($user->courses->pluck('id')) }}"
                                            title="إدارة الدورات">
                                        <i class="bi bi-journal-plus"></i>
                                    </button>
                                @else
                                    <div style="width: 35px;"></div>
                                @endif
                            </div>

                            <div>
                                <button class="btn btn-sm btn-outline-warning rounded-circle action-btn"
                                        data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}"
                                        title="تعديل البيانات">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                            </div>

                            <div>
                                <form action="{{ route('user.destroy', $user->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد؟')" class="m-0">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle action-btn" title="حذف">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>

                        </div>
                    </td>
                    </tr>
                    @include('users.edit_modal', ['user' => $user])
                    @empty
                    <tr><td colspan="8" class="py-5 text-muted">لا يوجد مستخدمين حالياً</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

{{-- مودال إدارة الدورات --}}
<div class="modal fade" id="courseUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <form id="courseForm" method="POST">
            @csrf @method('PUT')
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header bg-info text-white border-0 py-3">
                    <h5 class="modal-title fw-bold">دورات المحفظ: <span id="course_user_name"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-start">
                    <input type="hidden" name="update_courses_only" value="1">
                    <div class="row g-2">
                        @foreach($all_courses as $course)
                        <div class="col-6">
                            <div class="form-check p-2 bg-light rounded border">
                                <input class="form-check-input ms-2" type="checkbox" name="courses[]" value="{{ $course->id }}" id="modal_course_{{ $course->id }}">
                                <label class="form-check-label fw-bold cursor-pointer" for="modal_course_{{ $course->id }}">
                                    {{ $course->name }}
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-info text-white px-5 rounded-pill">حفظ التغييرات</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<style>
    .action-btn { width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; }
    .cursor-pointer { cursor: pointer; }
    .page-link { border-radius: 8px !important; margin: 0 2px; border: none; color: #666; }
    .page-item.active .page-link { background-color: #0d6efd; }
</style>

<script>
    // دالة الحذف البسيطة
    function confirmDelete(id) {
        if (confirm('هل أنت متأكد من حذف هذا المستخدم نهائياً؟')) {
            document.getElementById('deleteForm' + id).submit();
        }
    }

    // منطق مودال الدورات
    document.addEventListener('DOMContentLoaded', function() {
        const courseBtns = document.querySelectorAll('.course-btn');
        courseBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const userId = this.dataset.userId;
                const userName = this.dataset.userName;
                const userCourses = JSON.parse(this.dataset.userCourses);

                document.getElementById('courseForm').action = "/user/" + userId;
                document.getElementById('course_user_name').innerText = userName;

                document.querySelectorAll('.course-checkbox, .form-check-input').forEach(cb => {
                    if(cb.type === 'checkbox') {
                        cb.checked = userCourses.includes(parseInt(cb.value));
                    }
                });
            });
        });
    });
</script>
@endpush
