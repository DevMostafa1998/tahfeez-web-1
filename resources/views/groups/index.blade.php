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
                                            {{-- زر إدارة الطلاب --}}
                                            <button class="btn btn-action text-success" data-bs-toggle="modal"
                                                data-bs-target="#manageStudents{{ $group->id }}" title="إدارة الطلاب">
                                                <i class="bi bi-people-fill"></i>
                                            </button>
                                            {{-- زر التعديل --}}
                                            <button class="btn btn-action text-primary" data-bs-toggle="modal"
                                                data-bs-target="#editGroup{{ $group->id }}" title="تعديل المجموعة">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            {{-- زر الحذف --}}
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

                                {{-- 1. مودال تعديل المجموعة --}}
                                <div class="modal fade" id="editGroup{{ $group->id }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content border-0 shadow" style="border-radius: 15px;">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title fw-bold">تعديل مجموعة: {{ $group->GroupName }}</h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('group.update', $group->id) }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <div class="modal-body p-4 text-start">
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold small">اسم المجموعة</label>
                                                        <input type="text" name="GroupName" class="form-control"
                                                            value="{{ $group->GroupName }}" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-bold small">اسم المحفظ</label>
                                                        <select name="UserId" class="form-select" required>
                                                            @foreach ($teachers as $teacher)
                                                                <option value="{{ $teacher->id }}"
                                                                    {{ $group->UserId == $teacher->id ? 'selected' : '' }}>
                                                                    {{ $teacher->full_name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0">
                                                    <button type="submit" class="btn btn-primary px-4">تحديث
                                                        البيانات</button>
                                                    <button type="button" class="btn btn-light border px-4"
                                                        data-bs-dismiss="modal">إلغاء</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                {{-- 2. مودال إدارة الطلاب --}}
                                <div class="modal fade" id="manageStudents{{ $group->id }}" tabindex="-1"
                                    aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content border-0 shadow" style="border-radius: 15px;">
                                            <div class="modal-header bg-warning text-white">
                                                <h5 class="modal-title fw-bold">إدارة طلاب: {{ $group->GroupName }}</h5>
                                                <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('studentgroup.store') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="group_id" value="{{ $group->id }}">
                                                <div class="modal-body p-4">
                                                    <div class="mb-3">
                                                        <input type="text" class="form-control"
                                                            placeholder="بحث عن طالب..."
                                                            onkeyup="filterStudents(this, 'list{{ $group->id }}')">
                                                    </div>
                                                    <div class="border rounded p-3 bg-light"
                                                        style="max-height: 350px; overflow-y: auto;"
                                                        id="list{{ $group->id }}">
                                                        <div class="row g-2">
                                                            @foreach ($group->students as $st)
                                                                <div class="col-md-6 student-item">
                                                                    <div
                                                                        class="form-check card p-2 border-success border-opacity-25 shadow-sm">
                                                                        <input class="form-check-input ms-2"
                                                                            type="checkbox" name="student_ids[]"
                                                                            value="{{ $st->id }}" checked
                                                                            id="st{{ $group->id }}{{ $st->id }}">
                                                                        <label class="form-check-label"
                                                                            for="st{{ $group->id }}{{ $st->id }}">
                                                                            <span
                                                                                class="fw-bold d-block">{{ $st->full_name }}</span>
                                                                            <small class="text-success">مسجل حالياً</small>
                                                                            <span
                                                                                class="badge bg-success-subtle text-success border-0 fw-normal">
                                                                                {{ \Carbon\Carbon::parse($st->date_of_birth)->age }}
                                                                                سنة
                                                                            </span>
                                                                        </label>

                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                            @foreach ($availableStudents as $st)
                                                                <div class="col-md-6 student-item">
                                                                    <div class="form-check card p-2 border shadow-sm">
                                                                        <input class="form-check-input ms-2"
                                                                            type="checkbox" name="student_ids[]"
                                                                            value="{{ $st->id }}"
                                                                            id="st_av{{ $group->id }}{{ $st->id }}">
                                                                        <label class="form-check-label"
                                                                            for="st_av{{ $group->id }}{{ $st->id }}">
                                                                            <span
                                                                                class="fw-bold d-block">{{ $st->full_name }}</span>
                                                                            <small class="text-muted">غير مسجل</small>
                                                                            <span
                                                                                class="badge bg-success-subtle text-success border-0 fw-normal">
                                                                                {{ \Carbon\Carbon::parse($st->date_of_birth)->age }}
                                                                                سنة </span>
                                                                        </label>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0">
                                                    <button type="submit" class="btn btn-warning px-4">حفظ</button>
                                                    <button type="button" class="btn btn-light border px-4"
                                                        data-bs-dismiss="modal">إغاء</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

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

    {{-- مودال إضافة مجموعة جديدة --}}
    <div class="modal fade" id="createGroupModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow" style="border-radius: 15px;">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title fw-bold">إضافة مجموعة جديدة</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('group.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-4 text-start">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">اسم المجموعة</label>
                            <input type="text" name="GroupName" class="form-control" required
                                placeholder="مثال: مجموعة التميز">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">اختر المحفظ</label>
                            <select name="UserId" class="form-select" required>
                                <option value="" selected disabled>اختر...</option>
                                @foreach ($teachers as $teacher)
                                    <option value="{{ $teacher->id }}">{{ $teacher->full_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-primary px-4">حفظ</button>
                        <button type="button" class="btn btn-light border px-4" data-bs-dismiss="modal">إلغاء</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
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
    </script>
@endpush
