@extends('layouts.app')

@section('title', 'إدارة المستخدمين')

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/css/user_table.css') }}" />
@endpush

@section('content')
    <div class="container-fluid p-4">

        <div class="page-header d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-white p-2 rounded-4 shadow-sm">
                    <i class="bi bi-people-fill fs-3 text-primary"></i>
                </div>
                <div>
                    <h1 class="page-title m-0 h3">إدارة المستخدمين</h1>
                </div>
            </div>

            <a href="{{ route('user.create') }}" class="btn btn-primary d-flex align-items-center gap-2 px-4 py-2 rounded-3">
                <i class="bi bi-plus-lg"></i>
                <span>مستخدم جديد</span>
            </a>
        </div>

        <div class="card card-table">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th>اسم المستخدم</th>
                                <th>رقم الهوية</th>
                                <th>رقم الجوال</th>
                                <th>تاريخ الميلاد</th>
                                <th>العنوان</th>
                                <th>التصنيف</th>
                                <th>النوع</th>
                            </tr>
                        </thead>
                        <tbody>
                            {{-- صف المستخدم الأول --}}
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-circle bg-primary text-white d-flex align-items-center justify-content-center rounded-circle"
                                            style="width: 40px; height: 40px;">ي</div>
                                        <span class="fw-bold">يوسف محمد أحمد</span>
                                    </div>
                                </td>
                                <td>407474748</td>
                                <td>0592200300</td>
                                <td class="text-secondary">1992-03-12</td>
                                <td>غزة، حي النصر</td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3">
                                        <i class="bi bi-shield-check"></i> مسؤول
                                    </span>
                                </td>
                                <td><span class="badge bg-dark">إدارة عليا</span></td>
                            </tr>

                            {{-- صف المستخدم الثاني --}}
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-circle bg-success text-white d-flex align-items-center justify-content-center rounded-circle"
                                            style="width: 40px; height: 40px;">خ</div>
                                        <span class="fw-bold">خالد عبد الله</span>
                                    </div>
                                </td>
                                <td>912547714</td>
                                <td>0599100200</td>
                                <td class="text-secondary">1988-11-05</td>
                                <td>خانيونس، البلد</td>
                                <td>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3">
                                        <i class="bi bi-book"></i> محفظ
                                    </span>
                                </td>
                                <td><span class="badge bg-dark">حلقات قرآنية</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        console.log("تم تحميل صفحة إدارة المستخدمين بنجاح");
    </script>
@endpush
