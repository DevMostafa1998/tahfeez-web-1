@extends('layouts.app')

@section('title', 'إدارة الطلاب')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/user_table.css') }}" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
    <div class="container-fluid p-4" dir="rtl">
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
                                @if(request('filter') == 'not_memorized_today')
                                    <span class="text-danger">
                                        <i class="bi bi-exclamation-circle-fill me-2"></i>قائمة الطلاب الغائبين عن التسميع (اليوم)
                                    </span>
                                @else
                                    <span class="text-dark">قائمة جميع الطلاب</span>
                                @endif
                            </h3>

                            @if(request('filter'))
                                {{-- زر للعودة لعرض كل الطلاب يظهر فقط عند وجود فلتر --}}
                                <a href="{{ route('student.index') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                                    <i class="bi bi-arrow-right-short"></i> عرض جميع الطلاب
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 text-center">
                                {{-- ... الجدول ... --}}
                        <thead class="bg-light text-secondary">
                            <tr>
                                <th class="py-3" style="width: 50px;">#</th>
                                <th class="text-start ps-4">اسم الطالب رباعي</th>
                                <th>رقم الهوية</th>
                                <th>رقم الجوال</th>
                                <th>العنوان</th>
                                <th>حالة السكن</th>
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
                                        <span class="badge bg-light text-dark border px-3 py-2 fs-8 fw-bold" style="letter-spacing: 1px;">
                                            {{ $student->id_number }}
                                        </span>
                                    </td>
                                      <td>{{ $student->phone_number }}</td>
                                    <td class="small">{{ $student->address }}</td>

                                    <td>
                                        <span class="badge rounded-pill {{ $student->is_displaced ? 'bg-warning-subtle text-warning' : 'bg-success-subtle text-success' }} border px-3">
                                            {{ $student->is_displaced ? 'نازح' : 'مقيم' }}
                                        </span>
                                    </td>

                                        <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button type="button"
                                                class="btn btn-sm btn-outline-warning rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                                data-bs-toggle="modal"
                                                data-bs-target="#editStudentModal{{ $student->id }}"
                                                style="width: 35px; height: 35px;">
                                                <i class="bi bi-pencil-square fs-6"></i>
                                            </button>

                                            <form action="{{ route('student.destroy', $student->id) }}" method="POST"
                                                id="deleteForm{{ $student->id }}" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="button" onclick="confirmDelete({{ $student->id }})"
                                                    class="btn btn-sm btn-outline-danger rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                                    style="width: 35px; height: 35px;">
                                                    <i class="bi bi-trash3 fs-6"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                {{-- مودال التعديل لكل طالب --}}
                                <div class="modal fade" id="editStudentModal{{ $student->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;" dir="rtl">

                                        {{-- رأس المودال: العنوان يمين والزر يسار --}}
                                        <div class="modal-header bg-warning text-dark border-0 py-3 d-flex flex-row-reverse justify-content-between align-items-center">
                                            <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
                                            <h5 class="modal-title fw-bold m-0">
                                                <i class="bi bi-person-gear ms-2"></i>تعديل بيانات الطالب
                                            </h5>
                                        </div>

                                        <form action="{{ route('student.update', $student->id) }}" method="POST">
                                            @csrf
                                            @method('PUT')

                                            <div class="modal-body p-4">
                                                <div class="row g-3">

                                                    {{-- الاسم رباعي --}}
                                                    <div class="col-md-6 text-end">
                                                        <label class="form-label fw-bold small d-block" style="text-align: right;">الاسم رباعي</label>
                                                        <input type="text" name="full_name" class="form-control bg-light border-0 shadow-none"
                                                            style="text-align: right;" value="{{ $student->full_name }}" required>
                                                    </div>

                                                    {{-- رقم الهوية --}}
                                                    <div class="col-md-6 text-end">
                                                        <label class="form-label fw-bold small d-block" style="text-align: right;">رقم الهوية</label>
                                                        <input type="text" name="id_number" class="form-control bg-light border-0 shadow-none"
                                                            style="text-align: right;" value="{{ $student->id_number }}" required>
                                                    </div>

                                                    {{-- رقم الجوال --}}
                                                    <div class="col-md-6 text-end">
                                                        <label class="form-label fw-bold small d-block" style="text-align: right;">رقم الجوال</label>
                                                        <input type="text" name="phone_number" class="form-control bg-light border-0 shadow-none"
                                                            style="text-align: right;" value="{{ $student->phone_number }}" required>
                                                    </div>

                                                    {{-- العنوان --}}
                                                    <div class="col-md-6 text-end">
                                                        <label class="form-label fw-bold small d-block" style="text-align: right;">العنوان</label>
                                                        <input type="text" name="address" class="form-control bg-light border-0 shadow-none"
                                                            style="text-align: right;" value="{{ $student->address }}" required>
                                                    </div>

                                                    {{-- حالة السكن --}}
                                                    <div class="col-md-6 text-end">
                                                        <label class="form-label fw-bold small d-block" style="text-align: right;">حالة السكن</label>
                                                        <select name="is_displaced" class="form-select bg-light border-0 shadow-none"
                                                                style="text-align: right; direction: rtl;">
                                                            <option value="0" {{ !$student->is_displaced ? 'selected' : '' }}>مقيم</option>
                                                            <option value="1" {{ $student->is_displaced ? 'selected' : '' }}>نازح</option>
                                                        </select>
                                                    </div>

                                                </div>
                                            </div>

                                            {{-- أزرار التحكم --}}
                                            <div class="modal-footer border-0 p-3 bg-light d-flex justify-content-end">
                                                <button type="submit" class="btn btn-warning px-5 fw-bold shadow-sm rounded-pill">حفظ التغييرات</button>
                                                <button type="button" class="btn btn-secondary px-4 rounded-pill shadow-sm" data-bs-dismiss="modal">إلغاء</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-4 text-muted small">لا يوجد طلاب مسجلين حالياً.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="card-footer bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="text-muted small mb-2 mb-md-0">
                        عرض من {{ $students->firstItem() }} إلى {{ $students->lastItem() }} من إجمالي {{ $students->total() }} طالب
                    </div>
                    <div>
                        {{ $students->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('js')
    <style>
        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        .btn-outline-warning:hover { background-color: #ffc107 !important; color: #fff !important; }
        .btn-outline-danger:hover { background-color: #dc3545 !important; color: #fff !important; }
        .pagination { margin-bottom: 0; gap: 5px; }
        .page-link { border: none; border-radius: 8px !important; padding: 8px 16px; color: #666; }
        .page-item.active .page-link { background-color: #0d6efd; box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3); }
    </style>

    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: "سيتم نقل بيانات الطالب إلى سلة المهملات!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'نعم، احذف',
                cancelButtonText: 'تراجع',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('deleteForm' + id).submit();
                }
            });
        }
    </script>
@endpush
