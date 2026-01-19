@extends('layouts.app')

@section('content')
<div class="container-fluid p-4" dir="rtl">
    {{-- رسائل التنبيه --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4 text-end" role="alert" style="border-radius: 10px;">
            <i class="bi bi-check-circle-fill ms-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" style="float: left;"></button>
        </div>
    @endif

    <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">

      <div class="card-header bg-white py-4 border-bottom" dir="rtl">
    <div class="d-flex align-items-center flex-wrap gap-4 px-3">

        <div class="text-end">
            <h1 class="h4 fw-bold text-primary mb-1 d-flex align-items-center">
                <i class="bi bi-calendar2-check ms-2" style="font-size: 1.8rem;"></i>
                تحضير المحفظين
            </h1>
            <p class="text-muted small mb-0">يمكنك اختيار التاريخ لعرض أو تعديل سجلات الحضور</p>
        </div>

        <form action="{{ route('teachers.attendance') }}" method="GET" id="dateFilterForm"
              class="d-flex align-items-center gap-2 bg-light p-2 rounded-3 border ms-auto-none">

            <label class="small fw-bold text-dark mb-0 ms-2">عرض سجل تاريخ:</label>
            <input type="date" name="date" id="attendance_date_input"
                   class="form-control form-control-sm border-primary shadow-sm"
                   value="{{ $date ?? date('Y-m-d') }}"
                   onchange="this.form.submit()"
                   style="width: 170px; border-radius: 8px; cursor: pointer;">

            <noscript><button type="submit" class="btn btn-sm btn-primary">عرض</button></noscript>
        </form>

    </div>
</div>

        {{-- شريط معلومات التاريخ المعروض --}}
        <div class="bg-primary bg-opacity-10 py-2 px-4 text-primary fw-bold small border-bottom text-end shadow-sm">
             <i class="bi bi-info-circle ms-1"></i>
             أنت الآن تستعرض وتعدل سجلات تاريخ: <span class="badge bg-primary ms-1">{{ $date ?? date('Y-m-d') }}</span>
        </div>

        <form action="{{ route('teachers.attendance.store') }}" method="POST">
            @csrf
            {{-- حقل مخفي لإرسال التاريخ المختار مع الفورم عند الحفظ --}}
            <input type="hidden" name="attendance_date" value="{{ $date ?? date('Y-m-d') }}">

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle mb-0 text-center" style="border-color: #f0f0f0;">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="ps-3" style="width: 25%;">اسم المحفظ</th>
                            <th style="width: 15%;">رقم الهوية</th>
                            <th style="width: 25%;">الحالة اليومية</th>
                            <th style="width: 35%;">ملاحظات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($teachers as $teacher)
                        <tr>
                            <td class="text-end ps-4">
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm ms-3 bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px; min-width: 40px;">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                    <div>
                                        <span class="fw-bold d-block text-dark small">{{ $teacher->full_name }}</span>
                                        <span class="text-muted" style="font-size: 0.7rem;">{{ $teacher->phone_number }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border fw-normal">{{ $teacher->id_number }}</span>
                            </td>
                            <td>
                                <div class="btn-group shadow-none" role="group">
                                                {{-- خيار حاضر --}}
                                    <input type="radio" class="btn-check" name="teachers[{{ $teacher->id }}][status]"
                                           id="p_{{ $teacher->id }}" value="حاضر"
                                           {{ $teacher->today_status == 'حاضر' ? 'checked' : '' }} required>
                                    <label class="btn btn-sm btn-outline-success px-3 py-2" for="p_{{ $teacher->id }}">حاضر</label>

                                             {{-- خيار غائب --}}
                                    <input type="radio" class="btn-check" name="teachers[{{ $teacher->id }}][status]"
                                           id="a_{{ $teacher->id }}" value="غائب"
                                           {{ $teacher->today_status == 'غائب' ? 'checked' : '' }}>
                                    <label class="btn btn-sm btn-outline-danger px-3 py-2" for="a_{{ $teacher->id }}">غائب</label>

                                      {{-- خيار مستأذن --}}
                                    <input type="radio" class="btn-check" name="teachers[{{ $teacher->id }}][status]"
                                           id="e_{{ $teacher->id }}" value="مستأذن"
                                           {{ $teacher->today_status == 'مستأذن' ? 'checked' : '' }}>
                                    <label class="btn btn-sm btn-outline-warning px-3 py-2" for="e_{{ $teacher->id }}">مستأذن</label>
                                </div>
                            </td>
                            <td>
                                <div class="px-2">
                                    <input type="text" name="teachers[{{ $teacher->id }}][notes]"
                                           class="form-control form-control-sm bg-light border-0 shadow-none rounded-3"
                                           placeholder="أضف ملاحظة..." value="{{ $teacher->today_notes ?? '' }}"
                                           style="text-align: right; font-size: 0.85rem;">
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-5 text-muted text-center">
                                <i class="bi bi-exclamation-triangle ms-2"></i> لا يوجد محفظين لعرضهم.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white py-4 border-0 text-start px-4">
                <button type="submit" class="btn btn-primary px-5 fw-bold shadow-sm py-2" style="border-radius: 10px;">
                    <i class="bi bi-save2 ms-2"></i> حفظ سجل يوم {{ $date ?? date('Y-m-d') }}
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .table-bordered > :not(caption) > * > * {
        border-width: 1px 0;
        border-color: #f5f5f5;
    }

    .btn-group .btn {
        border-color: #e0e0e0;
        font-weight: 600;
        min-width: 70px;
    }

    /* ألوان حالات الحضور عند التفعيل */
    .btn-check:checked + .btn-outline-success { background-color: #198754 !important; color: white !important; border-color: #198754 !important; }
    .btn-check:checked + .btn-outline-danger { background-color: #dc3545 !important; color: white !important; border-color: #dc3545 !important; }
    .btn-check:checked + .btn-outline-warning { background-color: #ffc107 !important; color: #000 !important; border-color: #ffc107 !important; }

    .form-control:focus {
        background-color: #fff !important;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1);
        border: 1px solid #0d6efd !important;
    }

    tbody tr:hover { background-color: #fdfdfd !important; }

    input[type="date"]::-webkit-calendar-picker-indicator {
        cursor: pointer;
        filter: invert(0.5) sepia(1) saturate(5) hue-rotate(175deg);
    }
</style>
@endsection
