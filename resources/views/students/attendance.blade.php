@extends('layouts.app')

@section('content')
<div class="container-fluid p-3" dir="rtl">

    {{-- منطقة الفلترة الموحدة --}}
    <div class="card shadow-sm border-0 mb-3" style="border-radius: 15px;">
        <div class="card-body p-3">
            <form action="{{ route('attendance.index') }}" method="GET" id="filterForm" class="row g-2 align-items-end">

                <div class="col-md-2">
                    <label class="small fw-bold text-dark mb-2">تاريخ السجل</label>
                    <div class="input-group">
                        <input type="date" name="date" class="form-control border-primary shadow-none custom-date-picker"
                            value="{{ $date }}"
                            style="border-radius: 8px; cursor: pointer;">
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="small fw-bold text-dark mb-1">رقم الهوية</label>
                    <input type="text" name="search_id" class="form-control form-control-sm border-primary shadow-none"
                           placeholder="بحث..." value="{{ request('search_id') }}" style="border-radius: 8px; height: 35px; font-size: 0.85rem;">
                </div>

                @if(auth()->user()->is_admin)

                <div class="col-md-3">
                    <label class="small fw-bold text-dark mb-1">المحفظ</label>
                    <select name="teacher_id" class="form-select form-select-sm border-primary shadow-none" style="border-radius: 8px; height: 35px; font-size: 0.85rem;">
                        <option value="">كل المحفظين</option>
                        @foreach($teachers as $teacher)
                            <option value="{{ $teacher->id }}" {{ request('teacher_id') == $teacher->id ? 'selected' : '' }}>{{ $teacher->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                <div class="col-md-3">
                    <label class="small fw-bold text-dark mb-1">المجموعة</label>
                    <select name="group_id" class="form-select form-select-sm border-primary shadow-none" style="border-radius: 8px; height: 35px; font-size: 0.85rem;">
                        <option value="">كل المجموعات</option>
                        @foreach($groups as $group)
                            <option value="{{ $group->id }}" {{ request('group_id') == $group->id ? 'selected' : '' }}>{{ $group->GroupName }}</option>
                        @endforeach
                    </select>
                </div>

       <div class="col-12 text-end">
    {{-- <label class="small fw-bold text-transparent d-none d-md-block mb-1">&nbsp;</label> --}}
    <div class="d-flex gap-1 justify-content-end">
        <button type="submit" class="btn btn-primary btn-sm fw-bold shadow-sm" style="border-radius: 6px; height: 32px; font-size: 0.8rem;">
            <i class="bi bi-arrow-clockwise ms-2"></i> تحديث
        </button>

        <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary btn-sm fw-bold shadow-sm" style="border-radius: 6px; height: 32px; font-size: 0.8rem; display: flex; align-items: center; justify-content: center;">
            <i class="bi bi-arrow-counterclockwise ms-2"></i> إعادة تعيين
        </a>
    </div>
</div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
        <div class="card-header bg-white py-3 border-bottom">
            <div class="d-flex align-items-center justify-content-between px-3">
                <div>
                    <h1 class="h5 fw-bold text-primary mb-1">
                        <i class="bi bi-person-check-fill ms-2"></i>حضور الطلاب
                    </h1>
                    <p class="text-muted small mb-0">إدارة سجلات الحضور والغياب لليوم المحدد</p>
                </div>
                <button type="button" onclick="selectAllPresent()" class="btn btn-outline-success btn-sm fw-bold px-3 border-2" style="border-radius: 8px;">
                    <i class="bi bi-check-all ms-1"></i> تحديد الكل حاضر
                </button>
            </div>
        </div>

        <form action="{{ route('attendance.store') }}" method="POST" id="attendanceForm">
            @csrf
            <input type="hidden" name="attendance_date" value="{{ $date }}">

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0 text-center" style="border-color: #f0f0f0;">
                    <thead class="bg-light text-secondary small">
                        <tr>
                            <th class="ps-3 text" style="width: 20%;">اسم الطالب</th>
                            <th style="width: 15%;">المحفظ</th>
                            <th style="width: 15%;">المجموعة</th>
                            <th style="width: 20%;">الحالة اليومية</th>
                            <th style="width: 30%;">ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                        <tr class="student-row">
                            <td class="text-end ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm ms-3 bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px; font-size: 0.8rem;">
                                        {{ mb_substr($student->full_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <span class="fw-bold d-block text-dark small">{{ $student->full_name }}</span>
                                        <small class="text-muted" style="font-size: 0.7rem;">{{ $student->id_number }}</small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="small fw-bold text-primary">{{ $student->teacher_name }}</span></td>
                            <td>
                                <div class="d-flex flex-column align-items-center">
                                    <span class="badge border fw-bold px-3 py-2"
                                        style="background-color: #f8f9fa; color: #495057; border-color: #dee2e6 !important; border-radius: 6px; font-size: 0.75rem;">
                                        <i class="bi bi-people-fill text-primary ms-1"></i>
                                        {{ $student->group_name }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group shadow-none" role="group">
                                    <input type="radio" class="btn-check" name="students[{{ $student->id }}][status]" id="p_{{ $student->id }}" value="حاضر" {{ $student->today_status == 'حاضر' ? 'checked' : '' }}>
                                    <label class="btn btn-sm btn-outline-success px-3" for="p_{{ $student->id }}" style="font-size: 0.75rem;">حاضر</label>

                                    <input type="radio" class="btn-check" name="students[{{ $student->id }}][status]" id="a_{{ $student->id }}" value="غائب" {{ $student->today_status == 'غائب' ? 'checked' : '' }}>
                                    <label class="btn btn-sm btn-outline-danger px-3" for="a_{{ $student->id }}" style="font-size: 0.75rem;">غائب</label>

                                    <input type="radio" class="btn-check" name="students[{{ $student->id }}][status]" id="e_{{ $student->id }}" value="مستأذن" {{ $student->today_status == 'مستأذن' ? 'checked' : '' }}>
                                    <label class="btn btn-sm btn-outline-warning px-3" for="e_{{ $student->id }}" style="font-size: 0.75rem;">مستأذن</label>
                                </div>
                            </td>
                                <td>
                                    <input type="text"
                                        name="students[{{ $student->id }}][notes]"
                                        dir="rtl"
                                        class="form-control form-control-sm bg-light border-0 shadow-none"
                                        placeholder="ملاحظة..."
                                        value="{{ $student->today_notes ?? '' }}"
                                        style="height: 30px; font-size: 0.8rem; text-align: right; direction: rtl;">
                                </td>
                            </tr>
                        @empty
                        <tr><td colspan="5" class="py-5 text-muted">لا توجد بيانات مطابقة للبحث.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white py-3 border-0 text-start px-4">
                <button type="submit" id="saveBtn" class="btn btn-primary px-5 fw-bold shadow-sm" style="border-radius: 10px; height: 40px;">
                    حفظ السجل
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .btn-group .btn { border-color: #e0e0e0; font-weight: 600; min-width: 65px; }
    .btn-check:checked + .btn-outline-success { background-color: #198754 !important; color: white !important; }
    .btn-check:checked + .btn-outline-danger { background-color: #dc3545 !important; color: white !important; }
    .btn-check:checked + .btn-outline-warning { background-color: #ffc107 !important; color: black !important; }

    .student-row:hover { background-color: #f8f9fa; }

    .custom-date-picker::-webkit-calendar-picker-indicator {
        cursor: pointer;
        filter: invert(40%) sepia(90%) saturate(2000%) hue-rotate(200deg);
    }
    .custom-date-picker::-webkit-calendar-picker-indicator {
        display: block;
        background-color: transparent;
        cursor: pointer;
        padding: 0.2rem;
        filter: invert(40%) sepia(90%) saturate(2000%) hue-rotate(200deg);
    }

    .custom-date-picker {
        text-align: right;
        display: flex;
        flex-direction: row-reverse;
    }
</style>

<script>
    function selectAllPresent() {
        document.querySelectorAll('input[value="حاضر"]').forEach(r => r.checked = true);
    }
</script>
@endsection
