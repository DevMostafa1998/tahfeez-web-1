@extends('layouts.app')
@section('title', 'إدارة الطلاب')

@section('content')
<div class="container-fluid p-4" dir="rtl">

    {{-- رسائل التنبيه --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- رأس الصفحة --}}
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-primary fw-bold mb-0">
            <i class="bi bi-mortarboard-fill me-2"></i>إدارة الطلاب
        </h1>
        <a href="{{ route('student.create') }}" class="btn btn-primary px-4 shadow-sm fw-bold rounded-pill">
            <i class="bi bi-plus-lg ms-1"></i> إضافة طالب جديد
        </a>
    </div>

    {{-- جدول عرض البيانات --}}
    <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="py-3">#</th>
                        <th class="text-start ps-4">اسم الطالب</th>
                        <th>رقم الهوية</th>
                        <th>عدد الدورات</th>
                        <th>الحالة</th>
                        <th style="width: 120px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    <tr>
                        <td class="text-muted fw-bold">
                            {{ ($students->currentPage() - 1) * $students->perPage() + $loop->iteration }}
                        </td>

                        <td class="text-start ps-4">
                            <strong class="text-dark">{{ $student->full_name }}</strong>
                        </td>

                        <td>
                            <span class="badge bg-light text-dark border px-3 py-2 fw-bold">
                                {{ $student->id_number }}
                            </span>
                        </td>

                        <td>
                            <div class="d-flex justify-content-center align-items-center">
                                <span class="badge bg-warning text-dark rounded-pill border border-warning shadow-sm px-3">
                                    {{ $student->courses->whereIn('type', ['students', null])->count() }} دورات                                    <i class="bi bi-book ms-1"></i>
                                </span>
                            </div>
                        </td>

                        <td>
                            <span class="badge rounded-pill {{ $student->is_displaced ? 'bg-danger' : 'bg-success' }} px-3">
                                {{ $student->is_displaced ? 'نازح' : 'مقيم' }}
                            </span>
                        </td>

                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn btn-sm btn-outline-warning rounded-circle action-btn edit-student-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editStudentModal"
                                        data-student="{{ json_encode($student) }}"
                                        data-student-courses="{{ json_encode($student->courses->pluck('id')) }}"
                                        title="تعديل">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <form action="{{ route('student.destroy', $student->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle action-btn" title="حذف">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-muted py-5 fs-5">لا يوجد طلاب مضافين حالياً</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ================= مودال تعديل الطالب ================= --}}
<div class="modal fade" id="editStudentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="editStudentForm" method="POST">
            @csrf
            @method('PUT')
            @if ($errors->any())
        <div class="alert alert-danger mx-4 mt-3">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; text-align: right;" dir="rtl">

                <div class="modal-header bg-warning text-dark border-0 py-3 d-flex flex-row-reverse justify-content-between align-items-center">
                    <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title fw-bold m-0">
                        <i class="bi bi-pencil-square ms-2"></i>تعديل بيانات الطالب
                    </h5>
                </div>

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">الاسم رباعي <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" id="edit_full_name" class="form-control bg-light border-0" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">رقم الهوية <span class="text-danger">*</span></label>
                            <input type="text" name="id_number" id="edit_id_number" class="form-control bg-light border-0" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">رقم الهاتف <span class="text-danger">*</span></label>
                            <input type="tel" name="phone_number" id="edit_phone_number" class="form-control bg-light border-0" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold small">حالة السكن</label>
                            <select name="is_displaced" id="edit_is_displaced" class="form-select bg-light border-0">
                                <option value="0">مقيم</option>
                                <option value="1">نازح</option>
                            </select>
                        </div>

                        <div class="col-6">
                            <label class="form-label fw-bold small">العنوان</label>
                            <input type="text" name="address" id="edit_address" class="form-control bg-light border-0">
                        </div>

                        {{-- قسم الدورات فقط --}}
                        <div class="col-12 mt-4">
                            <div class="card border-warning mb-3">
                                <div class="card-header bg-warning bg-opacity-25 border-warning fw-bold text-dark">
                                    <i class="bi bi-book-half me-1"></i> الدورات المسندة للطالب
                                </div>
                                <div class="card-body bg-light">
                                    <div class="row g-2">
                                        @foreach($student_courses as $course)
                                            <div class="col-md-4 col-sm-6">
                                                <div class="form-check p-2 bg-white rounded border d-flex align-items-center">
                                                    <input class="form-check-input student-course-checkbox ms-2"
                                                           style="float: none;" type="checkbox"
                                                           name="courses[]" value="{{ $course->id }}"
                                                           id="edit_course_{{ $course->id }}">
                                                    <label class="form-check-label small fw-bold w-100 cursor-pointer" for="edit_course_{{ $course->id }}">
                                                        {{ $course->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 p-3 bg-light d-flex justify-content-end">
                    <button type="submit" class="btn btn-warning px-5 fw-bold shadow-sm rounded-pill">حفظ التعديلات</button>
                    <button type="button" class="btn btn-secondary px-4 rounded-pill shadow-sm" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<style>
    .action-btn { display: inline-flex; justify-content: center; align-items: center; width: 32px; height: 32px; }
    .cursor-pointer { cursor: pointer; }
    .form-check-input:checked { background-color: #ffc107; border-color: #ffc107; }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.edit-student-btn').forEach(button => {
            button.addEventListener('click', function() {
                const student = JSON.parse(this.dataset.student);
                const studentCourses = JSON.parse(this.dataset.studentCourses);

                const form = document.getElementById('editStudentForm');
                form.action = `/student/${student.id}`;

                document.getElementById('edit_full_name').value = student.full_name;
                document.getElementById('edit_id_number').value = student.id_number;
                document.getElementById('edit_phone_number').value = student.phone_number;
                document.getElementById('edit_address').value = student.address;
                document.getElementById('edit_is_displaced').value = student.is_displaced ? "1" : "0";

                document.querySelectorAll('.student-course-checkbox').forEach(checkbox => {
                    checkbox.checked = studentCourses.includes(parseInt(checkbox.value));
                });
            });
        });
    });
</script>
@endpush
