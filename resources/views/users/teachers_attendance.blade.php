@extends('layouts.app')

@section('content')
<div class="container-fluid p-4" dir="rtl">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 text-end" role="alert" style="border-radius: 10px;">
            <i class="bi bi-check-circle-fill ms-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" style="float: left;"></button>
        </div>
    @endif

    {{-- تنبيه الخطأ  باللون البرتقالي --}}
    <div id="validation-error" class="alert alert-warning border-0 shadow-sm mb-4 text-end d-none" style="border-radius: 10px; color: #856404; background-color: #fff3cd;">
        <i class="bi bi-exclamation-circle-fill ms-2"></i>
        <span>تنبيه: يرجى تحديد حالة الحضور لجميع المحفظين قبل إتمام عملية الحفظ.</span>
    </div>

    <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
        <div class="card-header bg-white py-4 border-bottom">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 px-3">
                <div class="text-end">
                    <h1 class="h4 fw-bold text-primary mb-1 d-flex align-items-center">
                        <i class="bi bi-calendar-check ms-2" style="font-size: 1.8rem;"></i>
                        تحضير المحفظين
                    </h1>
                    <p class="text-muted small mb-0">اختر التاريخ وسيتم تحديث السجل تلقائياً</p>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <button type="button" onclick="selectAllPresent()" class="btn btn-outline-success btn-sm fw-bold px-3 border-2" style="border-radius: 8px;">
                        <i class="bi bi-check-all ms-1"></i> تحديد الكل حاضر
                    </button>

                    <form action="{{ route('teachers.attendance') }}" method="GET" id="dateFilterForm" class="d-flex align-items-center gap-2 bg-light p-2 rounded-3 border shadow-sm">
                        <label class="small fw-bold text-dark mb-0 ms-1">عرض سجل التاريح :</label>
                        <input type="date" name="date" class="form-control form-control-sm border-primary shadow-none"
                               value="{{ $date ?? date('Y-m-d') }}"
                               onchange="this.form.submit()"
                               style="width: 160px; border-radius: 6px; cursor: pointer;">


                    </form>
                </div>
            </div>
        </div>

        <div class="bg-primary bg-opacity-10 py-2 px-4 text-primary fw-bold small border-bottom text-end shadow-sm">
             <i class="bi bi-info-circle ms-1"></i>
             أنت الآن تستعرض سجلات تاريخ: <span class="badge bg-primary ms-1">{{ $date ?? date('Y-m-d') }}</span>
        </div>

        <form action="{{ route('teachers.attendance.store') }}" method="POST" id="attendanceForm">
            @csrf
            <input type="hidden" name="attendance_date" value="{{ $date ?? date('Y-m-d') }}">

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0 text-center" style="border-color: #f0f0f0;">
                    <thead class="bg-light text-secondary small text-uppercase">
                        <tr>
                            <th class="ps-3 text" style="width: 25%;">اسم المحفظ</th>
                            <th style="width: 15%;">رقم الهوية</th>
                            <th style="width: 25%;">الحالة اليومية</th>
                            <th style="width: 35%;">ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teachers as $teacher)
                        <tr class="teacher-row" id="row_{{ $teacher->id }}">
                            <td class="text-end ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm ms-3 bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px;">
                                        <i class="bi bi-person"></i>
                                    </div>
                                    <div>
                                        <span class="fw-bold d-block text-dark small">{{ $teacher->full_name }}</span>
                                        <span class="text-muted" style="font-size: 0.7rem;">{{ $teacher->phone_number }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center justify-content-center">
                                    <div class="px-2 py-1 bg-light border rounded-pill d-inline-flex align-items-center shadow-sm" style="border-color: #e3e6f0 !important;">
                                        <i class="bi bi-card-heading text-secondary ms-2" style="font-size: 0.85rem;"></i>
                                        <span class="fw-bold text-dark font-monospace" style="font-size: 0.8rem; letter-spacing: 0.5px;">
                                            {{ $teacher->id_number }}
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="btn-group shadow-none" role="group">
                                    <input type="radio" class="btn-check status-radio" name="teachers[{{ $teacher->id }}][status]" id="p_{{ $teacher->id }}" value="حاضر" {{ $teacher->today_status == 'حاضر' ? 'checked' : '' }}>
                                    <label class="btn btn-sm btn-outline-success px-3 py-2" for="p_{{ $teacher->id }}">حاضر</label>

                                    <input type="radio" class="btn-check status-radio" name="teachers[{{ $teacher->id }}][status]" id="a_{{ $teacher->id }}" value="غائب" {{ $teacher->today_status == 'غائب' ? 'checked' : '' }}>
                                    <label class="btn btn-sm btn-outline-danger px-3 py-2" for="a_{{ $teacher->id }}">غائب</label>

                                    <input type="radio" class="btn-check status-radio" name="teachers[{{ $teacher->id }}][status]" id="e_{{ $teacher->id }}" value="مستأذن" {{ $teacher->today_status == 'مستأذن' ? 'checked' : '' }}>
                                    <label class="btn btn-sm btn-outline-warning px-3 py-2" for="e_{{ $teacher->id }}">مستأذن</label>
                                </div>
                            </td>
                            <td>
                            <input type="text"
                                name="teachers[{{ $teacher->id }}][notes]"
                                dir="rtl"
                                class="form-control form-control-sm bg-light border-0 shadow-none"
                                placeholder="أضف ملاحظة..."
                                value="{{ $teacher->today_notes ?? '' }}"
                                style="height: 30px; font-size: 0.85rem; border-radius: 6px; text-align: right; direction: rtl;">                            </td>
                            </tr>
                        @empty
                        <tr><td colspan="4" class="py-5 text-muted text-center">لا يوجد بيانات لعرضها.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white py-4 border-0 text-start px-4">
                <button type="submit" id="saveBtn" class="btn btn-primary px-5 fw-bold shadow-sm py-2" style="border-radius: 10px;">
                    <span class="spinner-border spinner-border-sm d-none ms-2" role="status"></span>
                    <i class="bi bi-save2 ms-2 icon-save"></i> حفظ سجل يوم {{ $date ?? date('Y-m-d') }}
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    //  وظيفة تحديد الكل حاضر
    function selectAllPresent() {
        const radios = document.querySelectorAll('input[value="حاضر"]');
        radios.forEach(radio => radio.checked = true);
        // إخفاء التنبيه عند التصحيح
        document.getElementById('validation-error').classList.add('d-none');
        document.querySelectorAll('.teacher-row').forEach(row => row.style.backgroundColor = '');
    }

    // 2. التحقق عند الضغط على زر الحفظ
    document.getElementById('attendanceForm').onsubmit = function(e) {
        let allSelected = true;
        const rows = document.querySelectorAll('.teacher-row');

        rows.forEach(row => {
            const radios = row.querySelectorAll('.status-radio');
            const isChecked = Array.from(radios).some(r => r.checked);

            if (!isChecked) {
                allSelected = false;
                row.style.backgroundColor = '#fff3cd'; //
            } else {
                row.style.backgroundColor = '';
            }
        });

        if (!allSelected) {
            e.preventDefault();
            const errorDiv = document.getElementById('validation-error');
            errorDiv.classList.remove('d-none');
            window.scrollTo({ top: 0, behavior: 'smooth' });
            return false;
        }

        // حالة التحميل عند نجاح التحقق
        const btn = document.getElementById('saveBtn');
        btn.disabled = true;
        btn.querySelector('.spinner-border').classList.remove('d-none');
        btn.querySelector('.icon-save').classList.add('d-none');
    };
</script>

<style>
    .btn-group .btn { border-color: #e0e0e0; font-weight: 600; min-width: 75px; }
    .btn-check:checked + .btn-outline-success { background-color: #198754 !important; color: white !important; }
    .btn-check:checked + .btn-outline-danger { background-color: #dc3545 !important; color: white !important; }
    .btn-check:checked + .btn-outline-warning { background-color: #ffc107 !important; color: black !important; }

    .teacher-row { transition: all 0.3s ease; }

    .btn:focus, .form-control:focus { box-shadow: none !important; }

    input[type="date"]::-webkit-calendar-picker-indicator {
        cursor: pointer;
        filter: hue-rotate(180deg) brightness(0.8);
    }
</style>
@endsection
