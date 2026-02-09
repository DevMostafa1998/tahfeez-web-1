@extends('layouts.app')
@section('title', 'إدارة الدورات العلمية')

@section('content')

    <div class="container-fluid p-4" dir="rtl">

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4 border-0 shadow-sm" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 text-primary fw-bold"><i class="bi bi-book-half me-2"></i>إدارة الدورات العلمية</h1>
            <button class="btn btn-primary px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                <i class="bi bi-plus-lg me-1"></i> إضافة دورة جديدة
            </button>
        </div>

        <div class="card shadow-sm border-0" style="border-radius: 15px;">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>اسم الدورة</th>
                                <th>الفئة المستهدفة</th>
                                <th>تاريخ الإضافة</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($courses as $course)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td class="fw-bold">{{ $course->name }}</td>
                                    <td>
                                        @if ($course->type == 'teachers')
                                            <span class="badge bg-primary">المحفظين</span>
                                        @elseif($course->type == 'students')
                                            <span class="badge bg-success">الطلاب</span>
                                        @else
                                            <span class="badge bg-dark">الجميع</span>
                                        @endif
                                    </td>
                                    <td class="small text-muted">{{ $course->created_at->format('Y-m-d') }}</td>
                                    <td>
                                        <button
                                            class="btn btn-sm btn-outline-primary rounded-circle action-btn edit-test-btn"
                                            data-bs-toggle="modal" data-bs-target="#editCourseModal"
                                            data-id="{{ $course->id }}" data-name="{{ $course->name }}"
                                            data-type="{{ $course->type }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('courses.destroy', $course->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                            @csrf @method('DELETE')
                                            <button
                                                class="btn btn-sm btn-outline-danger rounded-circle action-btn delete-test-btn">

                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-muted py-4">لا توجد دورات مضافة حالياً</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- مودال الإضافة --}}
    <div class="modal fade" id="addCourseModal" tabindex="-1" aria-hidden="true" dir="rtl">
        <div class="modal-dialog modal-lg modal-dialog-centered custom-modal">
            <form action="{{ route('courses.store') }}" method="POST" class="w-100">
                @csrf
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header bg-primary text-white border-0 py-3 px-4">
                        <h5 class="modal-title fw-bold">إضافة دورة جديدة</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3 text-start">
                            <div class="col-md-6">
                                <label class="form-label fw-bold mb-2">اسم الدورة</label>
                                <input type="text" name="name" class="form-control form-control-lg bg-light border-0"
                                    placeholder="اسم الدورة" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold mb-2">الفئة المستهدفة</label>
                                <select name="type" class="form-select form-select-lg bg-light border-0" required>
                                    <option value="" selected disabled>--- اختر الفئة المستهدفة ---</option>
                                    <option value="all">الجميع</option>
                                    <option value="teachers">المحفظين</option>
                                    <option value="students">الطلاب</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light p-3 d-flex justify-content-end gap-1">
                        <button type="submit" class="btn btn-primary px-5 fw-bold rounded-pill">حفظ</button>
                        <button type="button" class="btn btn-secondary px-4 rounded-pill"
                            data-bs-dismiss="modal">إلغاء</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- مودال التعديل --}}
    <div class="modal fade" id="editCourseModal" tabindex="-1" aria-hidden="true" dir="rtl">
        <div class="modal-dialog modal-lg modal-dialog-centered custom-modal">
            <form id="editCourseForm" method="POST" class="w-100">
                @csrf
                @method('PUT')
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header bg-warning text-dark border-0 py-3 px-4">
                        <h5 class="modal-title fw-bold">تعديل الدورة</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 text-start">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold mb-2">اسم الدورة</label>
                                <input type="text" name="name" id="edit_name"
                                    class="form-control form-control-lg bg-light border-0" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold mb-2">الفئة المستهدفة</label>
                                <select name="type" id="edit_type"
                                    class="form-select form-select-lg bg-light border-0" required>
                                    <option value="all">الجميع</option>
                                    <option value="teachers">المحفظين</option>
                                    <option value="students">الطلاب</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light p-3 d-flex justify-content-end gap-1">
                        <button type="submit" class="btn btn-warning px-5 fw-bold rounded-pill">تحديث</button>
                        <button type="button" class="btn btn-secondary px-4 rounded-pill"
                            data-bs-dismiss="modal">إلغاء</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                let id = this.dataset.id;
                let name = this.dataset.name;
                let type = this.dataset.type;
                let form = document.getElementById('editCourseForm');
                form.action = `/courses/${id}`;
                document.getElementById('edit_name').value = name;
                let finalType = (type === "" || type === "null") ? 'all' : type;
                document.getElementById('edit_type').value = finalType;
            });
        });
    </script>
@endpush
