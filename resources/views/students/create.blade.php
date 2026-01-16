@extends('layouts.app')

@section('title', 'إضافة طالب جديد')

@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0 fw-bold">إضافة طالب جديد</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">الرئيسية</a></li>
                        <li class="breadcrumb-item active" aria-current="page">إضافة طالب</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-outline card-primary shadow-sm"
                        style="border-radius: 15px; border-top: 4px solid #007bff;">
                        <div class="card-header bg-white py-3">
                            <h5 class="card-title fw-bold text-secondary mb-0">بيانات الطالب الأساسية</h5>
                        </div>

                        <div class="card-body p-4">
                            <form action="{{ route('student.store') }}" method="POST">
                                @csrf
                                <div class="row g-4">
                                    {{-- الاسم رباعي --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted">الاسم رباعي</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i
                                                    class="bi bi-person text-primary"></i></span>
                                            <input type="text" name="full_name" class="form-control"
                                                placeholder="أدخل الاسم رباعي" required>
                                        </div>
                                    </div>

                                    {{-- رقم الهوية --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted">رقم الهوية</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i
                                                    class="bi bi-card-heading text-primary"></i></span>
                                            <input type="text" name="id_number" class="form-control"
                                                placeholder="أدخل رقم الهوية" maxlength="9" inputmode="numeric"
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                                        </div>
                                    </div>

                                    {{-- تاريخ الميلاد --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted">تاريخ الميلاد</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i
                                                    class="bi bi-calendar3 text-primary"></i></span>
                                            <input type="date" name="date_of_birth" class="form-control" required>
                                        </div>
                                    </div>

                                    {{-- رقم  الهاتف --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted">رقم الهاتف</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i
                                                    class="bi bi-telephone text-primary"></i></span>
                                            <input type="tel" name="phone_number" class="form-control"
                                                placeholder="05XXXXXXXX" maxlength="10" inputmode="numeric"
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                                        </div>
                                    </div>

                                    {{-- العنوان --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted">العنوان</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i
                                                    class="bi bi-geo-alt text-primary"></i></span>
                                            <input type="text" name="address" class="form-control"
                                                placeholder="المدينة، الحي، الشارع" required>
                                        </div>
                                    </div>

                                    {{-- حالة السكن (نازح أم مقيم) --}}
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted">حالة السكن</label>
                                        <div class="input-group">
                                            {{-- الأيقونة الجانبية (مثل الصورة المرفقة) --}}
                                            <span class="input-group-text bg-light">
                                                <i class="bi bi-house-door text-primary"></i>
                                            </span>

                                            {{-- القائمة المنسدلة --}}
                                            <select name="is_displaced" class="form-select" required>
                                                <option value="" selected disabled>اختر الحالة...</option>
                                                <option value="0">مقيم</option>
                                                <option value="1">نازح</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer bg-white border-0 mt-5 p-0">
                                    <div class="d-flex justify-content-start gap-2">
                                        <button type="submit" class="btn btn-success px-5 fw-bold"
                                            style="background-color: #28a745; border:none;">
                                            <i class="bi bi-person-plus-fill me-1"></i> إضافة الطالب
                                        </button>
                                        <a href="{{ url()->previous() }}" class="btn btn-light px-4 border">إلغاء</a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
