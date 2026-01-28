@extends('layouts.app')
@section('title', 'إدارة المستخدمين')

@section('content')
<div class="container-fluid p-4" dir="rtl">

    {{-- رسائل التنبيه s--}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- رأس الصفحة --}}
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-primary fw-bold mb-0">
            <i class="bi bi-people-fill me-2"></i>إدارة المستخدمين
        </h1>
        <a href="{{ route('user.create') }}" class="btn btn-primary px-4 shadow-sm fw-bold rounded-pill">
            <i class="bi bi-plus-lg ms-1"></i> مستخدم جديد
        </a>
    </div>

    {{-- الجدول --}}
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
                        <td class="text-muted fw-bold">{{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}</td>
                        <td class="text-start ps-4"><strong>{{ $user->full_name }}</strong></td>
                        <td>
                            <span class="badge bg-light text-dark border px-3 py-2 fs-7 fw-bold shadow-sm"
                                style="letter-spacing: 1px; min-width: 100px;">
                                {{ $user->id_number }}
                            </span>
                        </td>
                           <td dir="ltr">{{ $user->phone_number }}</td>
                        <td><span class="badge rounded-pill bg-info bg-opacity-10 text-info border border-info">{{ $user->category->name ?? '---' }}</span></td>
                        <td>
                            <span class="badge rounded-pill {{ $user->is_admin ? 'bg-primary' : 'bg-success' }}">
                                {{ $user->is_admin ? 'مسؤول' : 'محفظ' }}
                            </span>
                        </td>
                        <td>
                            @if(!$user->is_admin)
                                <span class="badge bg-warning text-dark rounded-pill shadow-sm">
                                    {{ optional($user->courses)->count() ?? 0 }} دورات
                                    <i class="bi bi-book ms-1"></i>
                                </span>
                            @else
                                <span class="text-muted small">--</span>
                            @endif
                        </td>
                        <td style="width: 150px;">
                        {{-- نستخدم Grid بـ 3 أعمدة ثابتة العرض --}}
                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; justify-items: center; align-items: center;">

                            {{-- 1. عمود إدارة الدورات --}}
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
                                    {{-- نترك المكان فارغاً تماماً للآدمن ولكن المساحة محجوزة --}}
                                    <div style="width: 35px;"></div>
                                @endif
                            </div>

                            {{-- 2. عمود التعديل --}}
                            <div>
                                <button class="btn btn-sm btn-outline-warning rounded-circle action-btn"
                                        data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}"
                                        title="تعديل البيانات">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                            </div>

                            {{-- 3. عمود الحذف --}}
                            <div>{{-- الكود داخل الـ Loop --}}
                            <form action="{{ route('user.destroy', $user->id) }}" method="POST" id="deleteForm{{ $user->id }}" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>

                            <button type="button" onclick="confirmDelete({{ $user->id }})"
                                    class="btn btn-sm btn-outline-danger rounded-circle action-btn shadow-sm" title="حذف">
                                <i class="bi bi-trash3"></i>
                            </button>
                            </div>

                        </div>
                    </td>
                    </tr>

                    {{-- استدعاء المودال الخاص بك لكل مستخدم --}}
                    @include('users.edit_modal', ['user' => $user])

                    @empty
                    <tr><td colspan="8" class="py-5 text-muted">لا يوجد مستخدمين</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white border-0 py-3">
            {{ $users->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

{{-- مودال إدارة الدورات (أبقيته في index بناءً على طلبك) --}}
<div class="modal fade" id="courseUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <form id="courseForm" method="POST">
            @csrf @method('PUT')
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header bg-info text-white border-0 py-3">
                    <h5 class="modal-title fw-bold"><i class="bi bi-book-half ms-2"></i>دورات المحفظ: <span id="course_user_name"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 text-start">
                    <input type="hidden" name="update_courses_only" value="1">
                    <div class="row g-2">
                        @foreach($all_courses as $course)
                        <div class="col-6">
                            <div class="form-check p-2 bg-light rounded border d-flex align-items-center">
                                <input class="form-check-input course-checkbox ms-2" type="checkbox" name="courses[]" value="{{ $course->id }}" id="modal_course_{{ $course->id }}">
                                <label class="form-check-label fw-bold w-100 cursor-pointer" for="modal_course_{{ $course->id }}">
                                    {{ $course->name }}
                                </label>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light">
                    <button type="submit" class="btn btn-info text-white px-5 fw-bold rounded-pill">تحديث الدورات</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<style>
    .action-btn { width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center; transition: 0.3s; }
    .cursor-pointer { cursor: pointer; }
</style>

<script>
  function confirmDelete(id) {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: "سيتم حذف بيانات المستخدم نهائياً!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'نعم، احذف',
            cancelButtonText: 'تراجع',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // سيقوم بالبحث عن deleteForm + الرقم (مثلاً deleteForm5) ويقوم بإرساله
                document.getElementById('deleteForm' + id).submit();
            }
        });
    }
document.addEventListener('DOMContentLoaded', function() {
    // تشغيل مودال الدورات فقط (لأن مودال التعديل أصبح يعمل مباشرة بـ ID)
    // داخل قسم الـ Script في صفحة index
    document.querySelectorAll('.course-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.dataset.userId;
            const userName = this.dataset.userName;
            const userCourses = JSON.parse(this.dataset.userCourses);

            const form = document.getElementById('courseForm');

            // تعديل الرابط ليتوافق مع تسمية Laravel الافتراضية
            // تأكد أن الرابط هو /user/ID أو حسب ما هو معرف في الـ Route
            form.action = "{{ url('user') }}/" + userId;

            document.getElementById('course_user_name').innerText = userName;

            document.querySelectorAll('.course-checkbox').forEach(cb => {
                cb.checked = userCourses.includes(parseInt(cb.value));
            });
        });
    });
     @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: 'تم بنجاح',
                text: "{{ session('success') }}",
                confirmButtonColor: '#0dcaf0',
                timer: 2500
            });
        @endif
});
</script>
@endpush
