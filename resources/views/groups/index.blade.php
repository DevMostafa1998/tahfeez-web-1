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
            @if (auth()->check() && auth()->user()->is_admin)
                <button type="button" class="btn btn-primary d-flex align-items-center gap-2 px-4 py-2 rounded-3"
                    data-bs-toggle="modal" data-bs-target="#createGroupModal">
                    <i class="bi bi-plus-lg"></i><span>مجموعة جديدة</span>
                </button>
            @endif
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
                                            <a href="{{ route('group.show', $group->id) }}" class="btn btn-action text-info"
                                                title="عرض الطلاب">
                                                <i class="bi bi-eye-fill"></i>
                                            </a>
                                            @if (auth()->check() && auth()->user()->is_admin)
                                                <button class="btn btn-action text-success" data-bs-toggle="modal"
                                                    data-bs-target="#manageStudents{{ $group->id }}"
                                                    title="إدارة الطلاب">
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
                                            @endif

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
    {{-- مودال عرض تفاصيل المجموعة --}}
    <div class="modal fade" id="viewGroup{{ $group->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header bg-warning text-white border-0" style="border-radius: 20px 20px 0 0;">
                    <h5 class="modal-title fw-bold"><i class="bi bi-info-circle me-2"></i> تفاصيل المجموعة</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    {{-- معلومات المجموعة الأساسية --}}
                    <div class="text-center mb-4">
                        <h4 class="text-primary fw-bold mb-1">{{ $group->GroupName }}</h4>
                        <span class="badge bg-light text-secondary border">بإشراف المحفظ:
                            {{ $group->teacher->full_name ?? 'غير محدد' }}</span>
                    </div>

                    <hr class="text-muted opacity-25">

                    {{-- قائمة الطلاب --}}
                    <h6 class="fw-bold mb-3"><i class="bi bi-people me-2"></i> الطلاب المسجلون
                        ({{ $group->students->count() }})</h6>
                    <div class="list-group list-group-flush rounded-3 border overflow-auto" style="max-height: 250px;">
                        @forelse($group->students as $index => $student)
                            <div class="list-group-item d-flex justify-content-between align-items-center py-2 px-3">
                                <span class="text-dark">{{ $index + 1 }}. {{ $student->full_name }}</span>
                                @if ($student->date_of_birth)
                                    <span class="badge bg-success-subtle text-success border-0 fw-normal">
                                        {{ \Carbon\Carbon::parse($student->date_of_birth)->age }} سنة
                                    </span>
                                @endif
                            </div>
                        @empty
                            <div class="list-group-item text-center text-muted small py-3">
                                لا يوجد طلاب مسجلين في هذه المجموعة حالياً
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary px-4 rounded-3" data-bs-dismiss="modal">إغلاق</button>
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
