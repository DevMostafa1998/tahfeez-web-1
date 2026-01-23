@extends('layouts.app')

@section('content')
<div class="container-fluid p-3" dir="rtl">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-3 text-end" role="alert" style="border-radius: 10px;">
            <i class="bi bi-check-circle-fill ms-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" style="float: left;"></button>
        </div>
    @endif

    <div id="validation-error" class="alert alert-warning border-0 shadow-sm mb-3 text-end d-none" style="border-radius: 10px; color: #856404; background-color: #fff3cd;">
        <i class="bi bi-exclamation-circle-fill ms-2"></i>
        <span>تنبيه: يرجى تحديد حالة الحضور لجميع الطلاب قبل إتمام عملية الحفظ.</span>
    </div>

    <div class="card shadow-sm border-0 mb-3" style="border-radius: 15px;">
        <div class="card-body p-3">
            <form action="{{ route('attendance.index') }}" method="GET" id="filterForm" class="row g-2 align-items-end">
                <div class="col-md-2">
                    <label class="small fw-bold text-dark mb-2">تاريخ السجل</label>
                    <input type="date" name="date" class="form-control border-primary shadow-none custom-date-picker"
                        value="{{ $date }}" style="border-radius: 8px; cursor: pointer;">
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
        <div class="card-header bg-white py-3 border-bottom text-end">
            <div class="d-flex align-items-center justify-content-between px-3">
                <div>
                    <h1 class="h5 fw-bold text-primary mb-1">
                        <i class="bi bi-person-check-fill ms-2"></i>حضور الطلاب
                    </h1>
                    <p class="text-muted small mb-0">سجل يوم: <span class="badge bg-primary">{{ $date }}</span></p>
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
                            <th class="ps-3" style="width: 25%;">اسم الطالب</th>
                            <th style="width: 15%;">المحفظ</th>
                            <th style="width: 15%;">المجموعة</th>
                            <th style="width: 20%;">الحالة اليومية</th>
                            <th style="width: 25%;">ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                        <tr class="student-row" id="row_{{ $student->id }}">
                            <td class="text-end ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm ms-3 bg-info bg-opacity-10 text-info rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 35px; height: 35px; font-size: 0.9rem;">
                                        {{ mb_substr($student->full_name, 0, 1) }}
                                    </div>
                                    <div>
                                        <span class="fw-bold d-block text-dark" style="font-size: 1rem;">{{ $student->full_name }}</span>
                                        <small class="text-muted" style="font-size: 0.75rem;">{{ $student->id_number }}</small>
                                    </div>
                                </div>
                            </td>
                            <td><span class="fw-bold text-primary" style="font-size: 0.95rem;">{{ $student->teacher_name }}</span></td>
                            <td>
                                <span class="badge border fw-bold px-3 py-2" style="background-color: #f8f9fa; color: #495057; border-radius: 6px; font-size: 0.9rem;">
                                    <i class="bi bi-people-fill text-primary ms-1"></i> {{ $student->group_name }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group shadow-none" role="group">
                                    <input type="radio" class="btn-check status-radio" name="students[{{ $student->id }}][status]" id="p_{{ $student->id }}" value="حاضر" {{ $student->today_status == 'حاضر' ? 'checked' : '' }}>
                                    <label class="btn btn-sm btn-outline-success px-3" for="p_{{ $student->id }}" style="font-size: 0.75rem;">حاضر</label>

                                    <input type="radio" class="btn-check status-radio" name="students[{{ $student->id }}][status]" id="a_{{ $student->id }}" value="غائب" {{ $student->today_status == 'غائب' ? 'checked' : '' }}>
                                    <label class="btn btn-sm btn-outline-danger px-3" for="a_{{ $student->id }}" style="font-size: 0.75rem;">غائب</label>

                                    <input type="radio" class="btn-check status-radio" name="students[{{ $student->id }}][status]" id="e_{{ $student->id }}" value="مستأذن" {{ $student->today_status == 'مستأذن' ? 'checked' : '' }}>
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

            <div class="card-footer bg-white py-3 border-0 text-end px-4">
                <button type="submit" id="saveBtn" class="btn btn-primary px-5 fw-bold shadow-sm" style="border-radius: 10px; height: 45px;">
                    <span class="spinner-border spinner-border-sm d-none ms-2" role="status"></span>
                    <i class="bi bi-save2 ms-2 icon-save"></i> حفظ السجل
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

    .student-row { transition: background-color 0.3s ease; }

    .custom-date-picker { text-align: right; display: flex; flex-direction: row-reverse; }
</style>

<script>
    function selectAllPresent() {
        document.querySelectorAll('input[value="حاضر"]').forEach(r => r.checked = true);
        document.getElementById('validation-error').classList.add('d-none');
        document.querySelectorAll('.student-row').forEach(row => row.style.backgroundColor = '');
    }

    document.getElementById('attendanceForm').onsubmit = function(e) {
        let allSelected = true;
        const rows = document.querySelectorAll('.student-row');

        rows.forEach(row => {
            const radios = row.querySelectorAll('.status-radio');
            const isChecked = Array.from(radios).some(r => r.checked);

            if (!isChecked) {
                allSelected = false;
                row.style.backgroundColor = '#fff3cd';
            } else {
                row.style.backgroundColor = '';
            }
        });

        if (!allSelected) {
            e.preventDefault(); // منع إرسال النموذج
            const errorDiv = document.getElementById('validation-error');
            errorDiv.classList.remove('d-none'); // إظهار التنبيه
            window.scrollTo({ top: 0, behavior: 'smooth' }); // الصعود لأعلى لرؤية التنبيه
            return false;
        }

        // حالة التحميل (Spinner) في حال كان كل شيء تمام
        const btn = document.getElementById('saveBtn');
        btn.disabled = true;
        btn.querySelector('.spinner-border').classList.remove('d-none');
        btn.querySelector('.icon-save').classList.add('d-none');
    };
</script>
@endsection
