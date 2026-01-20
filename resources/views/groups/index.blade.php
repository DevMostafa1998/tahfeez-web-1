@extends('layouts.app')

@section('title', 'إدارة المجموعات')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/user_table.css') }}" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
    <div class="container-fluid p-4" dir="rtl">
        {{-- تنبيهات النجاح --}}
        @if (session('success'))
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'تمت العملية',
                    text: "{{ session('success') }}",
                    timer: 2000,
                    showConfirmButton: false
                });
            </script>
        @endif

        {{-- الهيدر --}}
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-white p-2 rounded-3 shadow-sm">
                    <i class="bi bi-people-fill fs-3 text-primary"></i>
                </div>
                <div>
                    <h1 class="page-title m-0 h3">إدارة المجموعات</h1>
                </div>
            </div>
            <button type="button" class="btn btn-primary d-flex align-items-center gap-2 px-4 py-2 rounded-3"
                data-bs-toggle="modal" data-bs-target="#createGroupModal">
                <i class="bi bi-plus-lg"></i><span>مجموعة جديدة</span>
            </button>
        </div>

        {{-- جدول البيانات --}}
        <div class="card card-table shadow-sm border-0 overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-secondary">
                            <tr>
                                <th class="ps-4">اسم المجموعة</th>
                                <th class="text-center">اسم المحفظ</th>
                                <th class="text-center">تاريخ الإنشاء</th>
                                <th class="text-center">عدد الطلاب</th>
                                <th class="text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($groups as $group)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center rounded-3"
                                                style="width: 40px; height: 40px;">
                                                <i class="bi bi-collection-fill"></i>
                                            </div>
                                            <span class="fw-bold">{{ $group->GroupName }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <span class="fw-medium">{{ $group->teacher->full_name ?? 'غير محدد' }}</span>
                                    </td>
                                    <td class="text-center text-muted small">{{ $group->creation_at }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-info-subtle text-info border px-3">
                                            {{ $group->students_count }} طلاب
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <button class="btn btn-action text-success" data-bs-toggle="modal"
                                                data-bs-target="#manageStudents{{ $group->id }}" title="إدارة الطلاب">
                                                <i class="bi bi-people-fill"></i>
                                            </button>
                                            <button class="btn btn-action text-primary" data-bs-toggle="modal"
                                                data-bs-target="#editGroup{{ $group->id }}" title="تعديل">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <form action="{{ route('group.destroy', $group->id) }}" method="POST"
                                                id="deleteForm{{ $group->id }}" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="button" class="btn btn-action text-danger"
                                                    onclick="confirmDelete({{ $group->id }})" title="حذف">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                {{-- استدعاء مودالات التعديل وإدارة الطلاب داخل الحلقة --}}
                                @include('groups.edit_modal')
                                @include('groups.manage_students_modal')

                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">لا يوجد مجموعات حالياً.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- استدعاء مودال الإضافة خارج الحلقة --}}
    @include('groups.create_modal')

@endsection

@push('scripts')
    <script>
        function filterStudents(input, listId) {
            let filter = input.value.toLowerCase();
            let items = document.getElementById(listId).getElementsByClassName('student-item');
            for (let item of items) {
                let text = item.innerText.toLowerCase();
                item.style.display = text.includes(filter) ? "" : "none";
            }
        }

        function confirmDelete(id) {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "سيتم حذف المجموعة نهائياً!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'تراجع',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) document.getElementById('deleteForm' + id).submit();
            });
        }
        $(document).ready(function() {
            $('#createGroupForm').on('submit', function(e) {
                e.preventDefault();

                let form = $(this);
                let submitBtn = form.find('button[type="submit"]');
                let formData = form.serialize();

                submitBtn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm"></span> جاري الحفظ...');

                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            // 1. إغلاق المودال وتفريغ الحقول
                            $('#createGroupModal').modal('hide');
                            form[0].reset();

                            // 2. إضافة الصف الجديد للجدول يدوياً في البداية
                            let newRow = `
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-circle bg-primary-subtle text-primary fw-bold">
                                        ${response.group.GroupName.charAt(0)}
                                    </div>
                                    <span class="fw-bold">${response.group.GroupName}</span>
                                </div>
                            </td>
                            <td>${response.group.teacher_name}</td>
                            <td><span class="badge bg-info-subtle text-info border px-3">0 طلاب</span></td>
                            <td><span class="text-muted small">${response.group.created_at}</span></td>
                            <td>
                                <button class="btn btn-action text-primary"><i class="bi bi-pencil-square"></i></button>
                            </td>
                        </tr>`;

                            $('table tbody').prepend(newRow); // إضافة في أول الجدول

                            // 3. تنبيه النجاح
                            Swal.fire({
                                icon: 'success',
                                title: 'تمت العملية',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    },
                    error: function(xhr) {
                        submitBtn.prop('disabled', false).text('حفظ');
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            Swal.fire('خطأ!', Object.values(errors)[0][0], 'error');
                        }
                    }
                });
            });
        });
    </script>
@endpush
