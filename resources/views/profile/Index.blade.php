@extends('layouts.app')

@section('content')
    @push('css')
        <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
        <style>
            :root {
                --primary-emerald: #0d685d;
                --soft-bg: #f8fafb;
            }

            body {
                background-color: var(--soft-bg);
                font-family: 'Tajawal', sans-serif !important;
            }

            .profile-header {
                background: linear-gradient(135deg, #0d685d 0%, #1a9384 100%);
                height: 200px;
                border-radius: 0 0 50px 50px;
                margin-bottom: 20px;
            }

            .main-profile-card {
                margin-top: -100px;
                background: #fff;
                border-radius: 30px;
                box-shadow: 0 20px 40px rgba(0, 0, 0, 0.05);
                padding: 30px;
            }

            .instructor-avatar {
                width: 160px;
                height: 160px;
                border: 8px solid #fff;
                border-radius: 35px;
                object-fit: cover;
                box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
            }

            .info-label {
                color: #888;
                font-size: 0.85rem;
                display: block;
            }

            .info-value {
                font-weight: 700;
                color: #2d3436;
                margin-bottom: 15px;
                display: block;
            }

            .section-header h5 {
                color: var(--primary-emerald);
                font-weight: 800;
                margin: 0;
            }

            .line {
                flex-grow: 1;
                height: 2px;
                background: #eee;
                margin-right: 10px;
            }

            .mosque-box {
                background: #f0f7f6;
                border-right: 4px solid var(--primary-emerald);
                padding: 15px;
                border-radius: 10px;
            }

            .admin-badge {
                background: #fee2e2;
                color: #dc2626;
                border: 1px solid #fecaca;
            }
        </style>
    @endpush

    <div class="profile-header"></div>

    <div class="container mb-5" dir="rtl">
        <div class="row justify-content-center">
            <div class="col-lg-11">

                <div class="card main-profile-card mb-4 border-0">
                    <div class="row align-items-center text-center text-md-start">

                        <div class="col-md-auto mb-4 mb-md-0">
                            <div class="position-relative d-inline-block">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($teacher->full_name) }}&background={{ $teacher->is_admin ? 'dc2626' : '0d685d' }}&color=fff&size=200"
                                    class="instructor-avatar">
                            </div>
                        </div>

                        <div class="col-md ps-md-4 text-start">
                            <span class="badge {{ $teacher->is_admin ? 'admin-badge' : 'bg-warning text-dark' }} mb-2 px-3">
                                {{ $teacher->is_admin ? 'مدير النظام' : 'محفظ' }}
                            </span>
                            <h1 class="fw-bold mb-1">{{ $teacher->full_name }}</h1>

                            <p class="text-muted fs-5 mb-3">
                                <i
                                    class="fas {{ $teacher->is_admin ? 'fa-user-shield text-danger' : 'fa-certificate text-success' }} me-1"></i>
                                {{ $teacher->is_admin ? 'إدارة الصلاحيات والتحكم' : $teacher->specialization ?? 'تخصص عام' }}
                            </p>

                            <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-4 mt-2">
                                <div><span class="info-label">رقم الجوال</span><span class="info-value"
                                        dir="ltr">{{ $teacher->phone_number }}</span></div>

                                @if ($teacher->is_admin)
                                    <div><span class="info-label">رقم الهوية</span><span
                                            class="info-value">{{ $teacher->id_number }}</span></div>
                                    <div><span class="info-label">مكان الميلاد</span><span
                                            class="info-value">{{ $teacher->birth_place ?? '-' }}</span></div>
                                @endif

                                <div><span class="info-label">الحالة</span>{!! $teacher->is_displaced
                                    ? '<span class="text-warning fw-bold">نازح</span>'
                                    : '<span class="text-success fw-bold">مقيم</span>' !!}</div>

                                <div>
                                    <span class="info-label">تاريخ الميلاد</span>
                                    <span
                                        class="info-value">{{ $teacher->date_of_birth ? \Carbon\Carbon::parse($teacher->date_of_birth)->format('Y-m-d') : '-' }}</span>
                                </div>

                                @if (!$teacher->is_admin)
                                    <div><span class="info-label">عدد الأجزاء</span><span
                                            class="info-value text-primary">{{ $teacher->parts_memorized ?? '0' }}
                                            جزء</span></div>
                                @endif

                                <div><span class="info-label">العنوان</span><span
                                        class="info-value">{{ $teacher->address ?? '-' }}</span></div>

                                @if ($teacher->is_admin)
                                    <div>
                                        <span class="info-label">التصنيف</span>
                                        <span
                                            class="info-value text-primary">{{ $teacher->category->name ?? 'غير مصنف' }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-auto mt-4 mt-md-0 d-flex flex-column gap-2 no-print">
                            @php
                                $currentUser = auth()->user();
                            @endphp
                            @if ($teacher->full_name !== 'admin')
                                @if ($teacher->is_admin == 1 || ($currentUser->is_admin == 0 && $currentUser->id == $teacher->id))
                                    <a href="{{ route('teachers.edit', $teacher->id) }}"
                                        class="btn btn-outline-primary btn-lg rounded-pill px-5 py-2 shadow-sm">
                                        تعديل البيانات <i class="fas fa-edit me-2"></i>
                                    </a>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>

                @if (!$teacher->is_admin)
                    <div class="row g-4">
                        <div class="col-lg-4">
                            <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 25px;">
                                <div class="d-flex align-items-center mb-4">
                                    <h5 class="fw-bold text-primary">البيانات الرسمية</h5>
                                    <div class="line"></div>
                                </div>

                                <div class="mb-4 mosque-box d-flex align-items-center justify-content-between">
                                    <small class="text-muted">المسجد التابع له:</small>
                                    <span class="fw-bold text-dark">{{ $teacher->mosque_name ?? 'غير محدد' }}</span>
                                </div>

                                <ul class="list-unstyled text-end">
                                    <li class="mb-3 d-flex justify-content-between border-bottom pb-2">
                                        <span class="text-muted">رقم الهوية:</span><span
                                            class="fw-bold">{{ $teacher->id_number }}</span>
                                    </li>
                                    <li class="mb-3 d-flex justify-content-between border-bottom pb-2">
                                        <span class="text-muted">مكان الميلاد:</span><span
                                            class="fw-bold">{{ $teacher->birth_place ?? '-' }}</span>
                                    </li>
                                    <li class="mb-3 d-flex justify-content-between border-bottom pb-2">
                                        <span class="text-muted">المؤهل العلمي:</span><span
                                            class="fw-bold text-success">{{ $teacher->qualification ?? '-' }}</span>
                                    </li>
                                    <li class="mb-3 d-flex justify-content-between border-bottom pb-2">
                                        <span class="text-muted">التصنيف:</span><span
                                            class="fw-bold text-primary">{{ $teacher->category->name ?? 'غير مصنف' }}</span>
                                    </li>
                                    <li class="mb-3 d-flex justify-content-between border-bottom pb-2">
                                        <span class="text-muted">رقم المحفظة:</span><span
                                            class="fw-bold">{{ $teacher->wallet_number ?? '-' }}</span>
                                    </li>
                                    <li class="mb-3 d-flex justify-content-between border-bottom pb-2">
                                        <span class="text-muted">رقم الواتساب:</span><span class="fw-bold"
                                            dir="ltr">{{ $teacher->whatsapp_number ?? '-' }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="col-lg-8">
                            <div class="card border-0 shadow-sm p-4 h-100" style="border-radius: 25px;">
                                <div class="d-flex align-items-center mb-3">
                                    <h5 class="fw-bold text-primary">الدورات المسندة</h5>
                                    <div class="line"></div>
                                </div>
                                <div class="row g-3 mb-5">
                                    @forelse($teacher->courses as $course)
                                        <div class="col-md-6">
                                            <div class="p-3 border rounded-4 bg-light">
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <h6 class="fw-bold m-0">{{ $course->name }}</h6>
                                                    <span class="badge bg-success">نشطة</span>
                                                </div>
                                                <small class="text-muted">
                                                    تاريخ الإسناد:
                                                    {{ $course->pivot->created_at ? $course->pivot->created_at->format('Y-m-d') : '-' }}
                                                </small>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-12 text-center text-muted">لا توجد دورات مسندة</div>
                                    @endforelse
                                </div>

                                <div class="d-flex align-items-center mb-3">
                                    <h5 class="fw-bold text-primary">مجموعات الطلاب</h5>
                                    <div class="line"></div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle text-center border">
                                        <thead class="table-light">
                                            <tr>
                                                <th>اسم المجموعة</th>
                                                <th>رقم المجموعة</th>
                                                <th>عدد الطلاب</th>
                                                <th>تاريخ الإنشاء</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($teacher->groups as $group)
                                                <tr>
                                                    <td class="fw-bold text-primary">{{ $group->GroupName }}</td>
                                                    <td><span class="badge bg-secondary">#{{ $group->id }}</span></td>
                                                    <td>
                                                        <span class="fw-bold text-dark">{{ $group->students_count ?? 0 }}
                                                            طالب</span>
                                                    </td>
                                                    <td>{{ $group->creation_at ? \Carbon\Carbon::parse($group->creation_at)->format('Y-m-d') : '-' }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-muted py-4">لا توجد مجموعات مسجلة حالياً
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection
