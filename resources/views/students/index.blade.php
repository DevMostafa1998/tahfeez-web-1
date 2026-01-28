@extends('layouts.app')

@section('title', 'إدارة الطلاب')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/user_table.css') }}" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .action-btn { width: 35px; height: 35px; display: inline-flex; align-items: center; justify-content: center; transition: 0.3s; }
        .cursor-pointer { cursor: pointer; }

        .btn-outline-warning:hover { background-color: #ffc107 !important; color: #fff !important; }
        .btn-outline-danger:hover { background-color: #dc3545 !important; color: #fff !important; }
        .btn-outline-info:hover { background-color: #0dcaf0 !important; color: #fff !important; }

        .pagination { margin-bottom: 0; gap: 5px; }
        .page-link { border: none; border-radius: 8px !important; padding: 8px 16px; color: #666; }
        .page-item.active .page-link { background-color: #0d6efd; box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3); }
    </style>
@endpush

@section('content')
    <div class="container-fluid p-4" dir="rtl">
        {{-- رأس الصفحة --}}
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-white p-2 rounded-3 shadow-sm text-primary">
                    <i class="bi bi-person-badge-fill fs-3"></i>
                </div>
                <div>
                    <h1 class="page-title m-0 h3 fw-bold text-primary">إدارة الطلاب</h1>
                </div>
            </div>
            <a href="{{ route('student.create') }}"
                class="btn btn-primary d-flex align-items-center gap-2 px-4 py-2 rounded-pill shadow-sm fw-bold">
                <i class="bi bi-plus-lg"></i><span>طالب جديد</span>
            </a>
        </div>

        <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
            <div class="card-header bg-white border-0 pt-4 px-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h3 class="h5 fw-bold mb-0">
                        @if (request('filter') == 'not_memorized_today')
                            <span class="text-danger"><i class="bi bi-exclamation-circle-fill me-2"></i>الطلاب الغائبين اليوم</span>
                        @else
                            <span class="text-dark">قائمة جميع الطلاب</span>
                        @endif
                    </h3>
                    @if (request('filter'))
                        <a href="{{ route('student.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                            <i class="bi bi-arrow-right-short"></i> عرض الكل
                        </a>
                    @endif
                </div>
            </div>

            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-center">
                        <thead class="bg-light text-secondary">
                            <tr>
                                <th class="py-3" style="width: 50px;">#</th>
                                <th class="text-start ps-4">اسم الطالب</th>
                                <th>رقم الهوية</th>
                                <th>الحالة</th>
                                <th>الدورات</th>
                                <th style="width: 150px;">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                                <tr>
                                    <td class="text-muted fw-bold">{{ ($students->currentPage() - 1) * $students->perPage() + $loop->iteration }}</td>
                                    <td class="text-start ps-4"><strong>{{ $student->full_name }}</strong></td>
                                    <td>
                                        <span class="badge bg-light text-dark border px-3 py-2 fs-8 fw-bold shadow-sm" style="letter-spacing: 1px;">
                                            {{ $student->id_number }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill {{ $student->is_displaced ? 'bg-warning-subtle text-warning' : 'bg-success-subtle text-success' }} border px-3">
                                            {{ $student->is_displaced ? 'نازح' : 'مقيم' }}
                                        </span>
                                    </td>
                                        <td>
                                        <span class="badge bg-warning text-dark rounded-pill shadow-sm">
                                            {{-- عرض القيمة القادمة من الاستعلام مباشرة --}}
                                            {{ $student->courses_count ?? 0 }} دورات
                                            <i class="bi bi-book ms-1"></i>
                                        </span>
                                    </td>

                                    <td>
                                        <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; justify-items: center; align-items: center;">

                                            <button class="btn btn-sm btn-outline-info rounded-circle action-btn course-btn"
                                                    data-bs-toggle="modal" data-bs-target="#courseStudentModal"
                                                    data-student-id="{{ $student->id }}"
                                                    data-student-name="{{ $student->full_name }}"
                                                    data-student-courses="{{ isset($student->course_ids) ? json_encode(explode(',', $student->course_ids)) : '[]' }}"
                                                    title="إدارة الدورات">
                                                <i class="bi bi-journal-plus"></i>
                                            </button>

                                            {{-- زر التعديل --}}
                                            <div>
                                                <button class="btn btn-sm btn-outline-warning rounded-circle action-btn"
                                                        data-bs-toggle="modal" data-bs-target="#editStudentModal{{ $student->id }}"
                                                        title="تعديل">
                                                    <i class="bi bi-pencil-square"></i>
                                                </button>
                                            </div>

                                            {{-- زر الحذف --}}
                                            <div>
                                                <form action="{{ route('student.destroy', $student->id) }}" method="POST" id="deleteForm{{ $student->id }}" class="m-0">
                                                    @csrf @method('DELETE')
                                                    <button type="button" onclick="confirmDelete({{ $student->id }})"
                                                            class="btn btn-sm btn-outline-danger rounded-circle action-btn" title="حذف">
                                                        <i class="bi bi-trash3"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @include('students.edit_modal', ['student' => $student])
                            @empty
                                <tr><td colspan="6" class="py-5 text-muted">لا يوجد طلاب</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="text-muted small">إجمالي الطلاب: {{ $students->total() }}</div>
                    {{ $students->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>

    {{-- مودال إدارة الدورات الخاص بالطلاب --}}
    <div class="modal fade" id="courseStudentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <form id="courseForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header bg-info text-white border-0 py-3">
                        <h5 class="modal-title fw-bold"><i class="bi bi-book-half ms-2"></i>دورات الطالب: <span id="modal_student_name"></span></h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 text-start">
                        <input type="hidden" name="update_courses_only" value="1">
                        <div class="row g-2">
                        @foreach(\DB::table('courses')->whereIn('type', ['students', null])->orWhereNull('type')->get() as $course)
                            <div class="col-6">
                                <div class="form-check p-2 bg-light rounded border d-flex align-items-center">
                                    <input class="form-check-input course-checkbox ms-2" type="checkbox"
                                        name="courses[]" value="{{ $course->id }}"
                                        id="student_course_{{ $course->id }}">
                                    <label class="form-check-label fw-bold w-100 cursor-pointer" for="student_course_{{ $course->id }}">
                                        {{ $course->name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                                                </div>
                    </div>
                    <div class="modal-footer border-0 bg-light">
                        <button type="submit" class="btn btn-info text-white px-5 fw-bold rounded-pill">حفظ التغييرات</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: "سيتم حذف بيانات الطالب نهائياً!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            confirmButtonText: 'نعم، احذف',
            cancelButtonText: 'تراجع',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteForm' + id).submit();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.course-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const studentId = this.dataset.studentId;
                const studentName = this.dataset.studentName;

                let studentCourses = [];
                try {
                    const rawData = this.dataset.studentCourses;
                    studentCourses = JSON.parse(rawData).map(id => parseInt(id));
                } catch (e) {
                    console.error("خطأ في قراءة مصفوفة الدورات:", e);
                    studentCourses = [];
                }

                const form = document.getElementById('courseForm');
                form.action = "{{ url('student') }}/" + studentId;

                const namePlaceholder = document.getElementById('modal_student_name');
                if(namePlaceholder) namePlaceholder.innerText = studentName;

                document.querySelectorAll('.course-checkbox').forEach(cb => {
                    const courseId = parseInt(cb.value);
                    cb.checked = studentCourses.includes(courseId);
                });
            });
        });

        // اختياري: تنبيهات النجاح بعد تحديث الدورات
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
