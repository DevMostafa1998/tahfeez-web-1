@extends('layouts.app')

@section('title', 'إدارة الطلاب')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/user_table.css') }}" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
@endpush

@section('content')
    <div class="container-fluid p-4" dir="rtl">
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-white p-2 rounded-3 shadow-sm">
                    <i class="bi bi-person-badge-fill fs-3 text-primary"></i>
                </div>
                <div>
                    <h1 class="page-title m-0 h3">إدارة الطلاب</h1>
                </div>
            </div>
            <a href="{{ route('student.create') }}"
                class="btn btn-primary d-flex align-items-center gap-2 px-4 py-2 rounded-3">
                <i class="bi bi-plus-lg"></i><span>طالب جديد</span>
            </a>
        </div>

        <div class="card card-table overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">اسم الطالب رباعي</th>
                                <th class="text-center">رقم الهوية</th>
                                <th class="text-center">رقم الجوال</th>
                                <th class="text-center">العنوان</th>
                                <th class="text-center">حالة السكن</th>
                                <th class="text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-circle bg-primary text-white d-flex align-items-center justify-content-center rounded-circle"
                                                style="width: 40px; height: 40px;">
                                                {{ mb_substr($student->full_name, 0, 1) }}
                                            </div>
                                            <span class="fw-bold">{{ $student->full_name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">{{ $student->id_number }}</td>
                                    <td class="text-center">{{ $student->phone_number }}</td>
                                    <td class="text-center">{{ $student->address }}</td>
                                    <td class="text-center">
                                        <span
                                            class="badge {{ $student->is_displaced ? 'bg-warning-subtle text-warning-emphasis' : 'bg-success-subtle text-success' }} border badge-status">
                                            {{ $student->is_displaced ? 'نازح' : 'مقيم' }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <button type="button" class="btn btn-action text-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editStudentModal{{ $student->id }}">
                                                <i class="bi bi-pencil-square fs-5"></i>
                                            </button>

                                            <form action="{{ route('student.destroy', $student->id) }}" method="POST"
                                                class="d-inline" onsubmit="return confirm('هل أنت متأكد؟')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-action text-danger"><i
                                                        class="bi bi-trash3 fs-5"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                <div class="modal fade" id="editStudentModal{{ $student->id }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-lg modal-dialog-centered">
                                        <div class="modal-content"
                                            style="border-radius: 15px; overflow: hidden; border: none;">
                                            <div class="modal-header modal-header-yellow">
                                                <h5 class="modal-title fw-bold">تعديل بيانات الطالب</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                    aria-label="Close"></button>
                                            </div>
                                            <form action="{{ route('student.update', $student->id) }}" method="POST">
                                                @csrf @method('PUT')
                                                <div class="modal-body p-4 text-start">
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-bold">الاسم رباعي</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text bg-white"><i
                                                                        class="bi bi-person text-primary"></i></span>
                                                                <input type="text" name="full_name" class="form-control"
                                                                    value="{{ $student->full_name }}" required autofocus>

                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-bold">رقم الهوية</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text bg-white"><i
                                                                        class="bi bi-card-heading text-primary"></i></span>
                                                                <input type="text" name="id_number" class="form-control"
                                                                    value="{{ $student->id_number }}" required>

                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-bold">رقم الجوال</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text bg-white"><i
                                                                        class="bi bi-telephone text-primary"></i></span>
                                                                <input type="text" name="phone_number"
                                                                    class="form-control"
                                                                    value="{{ $student->phone_number }}" required>

                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-bold">العنوان</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text bg-white"><i
                                                                        class="bi bi-geo-alt text-primary"></i></span>
                                                                <input type="text" name="address" class="form-control"
                                                                    value="{{ $student->address }}" required>

                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-bold">تاريخ الميلاد</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text bg-white"><i
                                                                        class="bi bi-calendar3 text-primary"></i></span>
                                                                <input type="date" name="date_of_birth"
                                                                    class="form-control"
                                                                    value="{{ $student->date_of_birth->format('Y-m-d') }}"
                                                                    required>

                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-bold">حالة السكن</label>
                                                            <div class="input-group">
                                                                <span class="input-group-text bg-white"><i
                                                                        class="bi bi-house-door text-primary"></i></span>
                                                                <select name="is_displaced" class="form-select" required>
                                                                    <option value="0"
                                                                        {{ $student->is_displaced == 0 ? 'selected' : '' }}>
                                                                        مقيم</option>
                                                                    <option value="1"
                                                                        {{ $student->is_displaced == 1 ? 'selected' : '' }}>
                                                                        نازح</option>
                                                                </select>

                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0 justify-content-start px-4 pb-4">
                                                    <button type="submit" class="btn btn-save-yellow px-4 py-2">حفظ
                                                        التغييرات</button>
                                                    <button type="button" class="btn btn-light border px-4 py-2 fw-bold"
                                                        data-bs-dismiss="modal">إلغاء</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">لا يوجد طلاب.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('js')
    <script>
        $(document).ready(function() {
            $('.update-student-form').on('submit', function(e) {
                e.preventDefault();

                let form = $(this);
                let studentId = form.data('id');
                let formData = form.serialize();
                let submitBtn = form.find('button[type="submit"]');

                // إظهار مؤشر التحميل
                submitBtn.prop('disabled', true).find('.spinner-border').removeClass('d-none');

                $.ajax({
                    url: `/student/${studentId}`, // تأكد من مطابقة مسار الـ Route عندك
                    method: 'POST', // نستخدم POST مع _method PUT في البيانات
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            // 1. إغلاق المودال
                            $(`#editStudentModal${studentId}`).modal('hide');

                            // 2. تحديث بيانات الصف في الجدول فوراً
                            updateRow(studentId, response.student);

                            // 3. رسالة نجاح
                            Swal.fire({
                                icon: 'success',
                                title: 'تمت العملية',
                                text: 'تم تحديث بيانات الطالب بنجاح',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        let errors = xhr.responseJSON.errors;
                        let errorMessage = 'حدث خطأ ما، يرجى المحاولة لاحقاً';
                        if (errors) {
                            errorMessage = Object.values(errors)[0][0];
                        }
                        Swal.fire('خطأ!', errorMessage, 'error');
                    },
                    complete: function() {
                        submitBtn.prop('disabled', false).find('.spinner-border').addClass(
                            'd-none');
                    }
                });
            });

            // دالة لتحديث بيانات الصف دون إعادة تحميل الصفحة
            function updateRow(id, student) {
                let row = $(`#editStudentModal${id}`).closest('tr');
                row.find('td:nth-child(1) span.fw-bold').text(student.full_name);
                row.find('td:nth-child(2)').text(student.id_number);
                row.find('td:nth-child(3)').text(student.phone_number);
                row.find('td:nth-child(4)').text(student.address);

                // تحديث حالة السكن (نازح/مقيم)
                let statusBadge = student.is_displaced == 1 ?
                    '<span class="badge bg-warning-subtle text-warning-emphasis border badge-status">نازح</span>' :
                    '<span class="badge bg-success-subtle text-success border badge-status">مقيم</span>';
                row.find('td:nth-child(5)').html(statusBadge);
            }
        });
    </script>
@endpush
