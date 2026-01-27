@extends('layouts.app')

@section('content')
    <style>
        .main-card {
            background-color: #ffffff;
            border-radius: 15px;
            border: 1px solid #ececec;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            padding: 20px;
            margin-top: 20px;
        }

        .table thead th {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
            /* تغميق خط الرأس قليلًا */
            color: #333;
            font-weight: 600;
            padding: 15px;
        }

        /* تصميم المربع الأبيض الصغير للأيقونة */
        .icon-box {
            background-color: #ffffff;
            width: 45px;
            height: 45px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.08);
            /* ظل ناعم للمربع */
            border: 1px solid #f0f0f0;
            margin-left: 15px;
            /* مسافة بين المربع والنص */
        }

        .icon-box i {
            font-size: 1.4rem;
            color: #0d6efd;
            /* لون الأيقونة */
        }

        .table tbody td {
            padding: 15px;
            vertical-align: middle;
            border-bottom: 1px solid #dee2e6;
            /* فاصل أوضح بين الصفوف */
        }

        .table tbody tr:last-child td {
            border-bottom: none;
            /* إزالة الخط من آخر صف */
        }

        .column-fit {
            width: 1%;
            white-space: nowrap;
        }

        .btn-action {
            width: 35px;
            height: 35px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            border: 1px solid #eee;
            background-color: #fff;
            transition: all 0.3s;
            margin: 0 2px;
        }

        .btn-edit:hover {
            background-color: #f0f7ff;
            color: #0d6efd !important;
            border-color: #0d6efd;
        }

        .btn-delete:hover {
            background-color: #fff5f5;
            color: #dc3545 !important;
            border-color: #dc3545;
        }
    </style>
    <div class="container mt-5" dir="rtl">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <div class="icon-box">
                    <i class="bi bi-journal-check"></i>
                </div>
                <h2 class="text-primary fw-bold mb-0">سجل اختبارات تسميع القرآن</h2>
            </div>

            <a href="{{ route('quran_tests.create') }}" class="btn btn-success px-4 shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> إضافة اختبار جديد
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success border-0 shadow-sm">{{ session('success') }}</div>
        @endif

        <div class="main-card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">الطالب</th>
                            <th>التاريخ</th>
                            <th>الأجزاء</th>
                            <th>النوع</th>
                            <th>النتيجة</th>
                            <th class="text-center column-fit pe-4">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tests as $test)
                            <tr>
                                <td class="ps-4 fw-medium">{{ $test->student?->full_name }}</td>
                                <td>{{ $test->date->format('Y-m-d') }}</td>
                                <td><span class="text-muted">{{ $test->juz_count }} </span></td>
                                <td><span class="badge bg-soft-info text-info border border-info"
                                        style="--bs-bg-opacity: .1;">{{ $test->examType }}</span></td>
                                <td>
                                    <span class="badge {{ $test->result_status == 'ناجح' ? 'bg-success' : 'bg-danger' }}">
                                        {{ $test->result_status }}
                                    </span>
                                </td>
                                <td class="text-center column-fit pe-4">
                                    <button type="button" class="btn-action btn-edit text-primary edit-test-btn"
                                        data-bs-toggle="modal" data-bs-target="#editTestModal" data-id="{{ $test->id }}"
                                        data-student="{{ $test->studentId }}"
                                        data-student-name="{{ $test->student?->full_name }}"
                                        data-date="{{ $test->date->format('Y-m-d') }}" data-juz="{{ $test->juz_count }}"
                                        data-type="{{ $test->examType }}" data-status="{{ $test->result_status }}"
                                        data-note="{{ $test->note }}" title="تعديل">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <button type="button" class="btn-action btn-delete text-danger delete-test-btn"
                                        data-id="{{ $test->id }}" data-name="{{ $test->student?->full_name }}"
                                        title="حذف">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- استدعاء ملف المودل المنفصل --}}
    @include('quran_tests.edit_modal')

    {{-- الإسكربتات --}}
    @push('scripts')
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <script>
            $(document).ready(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                function clearNoteField() {
                    $('#edit_note').val('');
                }

                // 1. تعبئة بيانات المودال
                $(document).on('click', '.edit-test-btn', function() {
                    clearNoteField();

                    const id = $(this).data('id');
                    const studentId = $(this).data('student');
                    const studentName = $(this).data('student-name');
                    const noteValue = $(this).data('note');

                    $('#editTestForm').attr('action', `/quran_tests/${id}`);
                    $('#edit_student_name_display').val(studentName);
                    $('#edit_studentId').val(studentId);
                    $('#edit_date').val($(this).data('date'));
                    $('#edit_juz_count').val($(this).data('juz'));
                    $('#edit_examType').val($(this).data('type'));
                    $('#edit_result_status').val($(this).data('status'));
                    $('#edit_note').val(noteValue);

                    $('.is-invalid').removeClass('is-invalid');
                    $('.invalid-feedback').remove();
                });

                // 2. إرسال التعديل عبر AJAX
                $('#editTestForm').on('submit', function(e) {
                    e.preventDefault();
                    const form = $(this);

                    $.ajax({
                        url: form.attr('action'),
                        type: 'POST',
                        data: form.serialize(),
                        success: function(response) {
                            $('#editTestModal').modal('hide');
                            Swal.fire({
                                icon: 'success',
                                title: 'تم التحديث!',
                                text: response.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(() => {
                                location.reload();
                            });
                        },
                        error: function(xhr) {
                            if (xhr.status === 422) {
                                let errors = xhr.responseJSON.errors;
                                $('.is-invalid').removeClass('is-invalid');
                                $('.invalid-feedback').remove();
                                $.each(errors, function(key, value) {
                                    let input = $(`[name="${key}"]`);
                                    input.addClass('is-invalid');
                                    input.after(
                                        `<div class="invalid-feedback d-block">${value[0]}</div>`
                                    );
                                });
                            }
                        }
                    });
                });

                // 3. وظيفة الحذف
                $(document).on('click', '.delete-test-btn', function() {
                    const id = $(this).data('id');
                    const studentName = $(this).data('name');

                    Swal.fire({
                        title: 'هل أنت متأكد؟',
                        text: `سيتم حذف سجل اختبار الطالب "${studentName}" نهائياً!`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'نعم، احذف السجل',
                        cancelButtonText: 'إلغاء',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: `/quran_tests/${id}`,
                                type: 'DELETE',
                                success: function(response) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'تم الحذف!',
                                        text: response.message,
                                        timer: 1500,
                                        showConfirmButton: false
                                    }).then(() => {
                                        location.reload();
                                    });
                                },
                                error: function() {
                                    Swal.fire('خطأ!',
                                        'تعذر حذف السجل، يرجى المحاولة لاحقاً.', 'error'
                                    );
                                }
                            });
                        }
                    });
                });

                $('#editTestModal').on('hidden.bs.modal', function() {
                    clearNoteField();
                });
            });
        </script>
    @endpush
@endsection
