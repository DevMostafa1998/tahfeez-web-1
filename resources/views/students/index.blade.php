@extends('layouts.app')

@section('title', 'إدارة الطلاب')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/user_table.css') }}" />
    <style>
        .badge-status {
            border-radius: 50rem;
            padding: 0.5em 1em;
        }

        .btn-action {
            padding: 0.25rem 0.5rem;
            border-radius: 8px;
            transition: all 0.2s;
        }

        .btn-action:hover {
            background-color: #f8f9fa;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid p-4" dir="rtl">
        <div class="page-header d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-white p-2 rounded-3 shadow-sm">
                    <i class="bi bi-person-badge-fill fs-3 text-primary"></i>
                </div>
                <div>
                    <h1 class="page-title m-0 h3">إدارة الطلاب</h1>
                </div>
            </div>

            <a href="{{ route('student.create') }}"
                class="btn btn-primary d-flex align-items-center gap-2 px-4 py-2 rounded-3">
                <i class="bi bi-plus-lg"></i>
                <span>طالب جديد</span>
            </a>
        </div>

        <div class="card card-table overflow-hidden">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">اسم الطالب رباعي</th>
                                <th class="text-center">رقم الهوية</th>
                                <th class="text-center">رقم الجوال</th>
                                <th class="text-center">تاريخ الميلاد</th>
                                <th class="text-center">العنوان</th>
                                <th class="text-center">حالة السكن</th>
                                <th class="text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $student)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-circle bg-primary text-white d-flex align-items-center justify-content-center rounded-circle"
                                                style="width: 40px; height: 40px; min-width: 40px;">
                                                {{ mb_substr($student->full_name, 0, 1) }}
                                            </div>
                                            <span class="fw-bold">{{ $student->full_name }}</span>
                                        </div>
                                    </td>
                                    <td class="text-center">{{ $student->id_number }}</td>
                                    <td class="text-center">{{ $student->phone_number }}</td>
                                    <td class="text-center text-secondary">{{ $student->date_of_birth->format('Y-m-d') }}
                                    </td>
                                    <td class="text-center text-sm">{{ $student->address }}</td>
                                    <td class="text-center">
                                        @if ($student->is_displaced)
                                            <span
                                                class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle badge-status">
                                                نازح
                                            </span>
                                        @else
                                            <span
                                                class="badge bg-success-subtle text-success border border-success-subtle badge-status">
                                                مقيم
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <a href="{{ route('student.edit', $student->id) }}"
                                                class="btn btn-action text-primary" title="تعديل">
                                                <i class="bi bi-pencil-square fs-5"></i>
                                            </a>
                                            <form action="{{ route('student.destroy', $student->id) }}" method="POST"
                                                class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-action text-danger" title="حذف">
                                                    <i class="bi bi-trash3 fs-5"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">لا يوجد طلاب مسجلين حالياً.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
