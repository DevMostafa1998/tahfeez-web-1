@extends('layouts.app') {{-- استدعاء القالب الأساسي الذي يحتوي على الهيدر والسايدبار --}}

@section('title', 'إضافة مستخدم جديد')

@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0 fw-bold">إضافة مستخدم جديد</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">الرئيسية</a></li>
                        <li class="breadcrumb-item active" aria-current="page">إضافة مستخدم</li>
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
                            <h5 class="card-title fw-bold text-secondary mb-0">بيانات المستخدم الأساسية</h5>
                        </div>

                        <div class="card-body p-4">
                            <form action="#" method="POST">
                                @csrf
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted">الاسم رباعي</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i
                                                    class="bi bi-person text-primary"></i></span>
                                            <input type="text" name="FullName" class="form-control"
                                                placeholder="أدخل الاسم رباعي" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted">رقم الهوية</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i
                                                    class="bi bi-card-heading text-primary"></i></span>
                                            <input type="text" name="IdNumber" class="form-control"
                                                placeholder="أدخل رقم الهوية" inputmode="numeric"
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted">تاريخ الميلاد</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i
                                                    class="bi bi-calendar3 text-primary"></i></span>
                                            <input type="date" name="DateOfBirth" class="form-control" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted">رقم الهاتف</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i
                                                    class="bi bi-telephone text-primary"></i></span>
                                            <input type="tel" name="PhoneNumber" class="form-control"
                                                placeholder="05XXXXXXXX" inputmode="numeric"
                                                oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label fw-bold small text-muted">العنوان</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-light"><i
                                                    class="bi bi-geo-alt text-primary"></i></span>
                                            <input type="text" name="Address" class="form-control"
                                                placeholder="المدينة، الحي" required>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold small text-muted">تصنيف المستخدم</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light"><i
                                                            class="bi bi-layers text-primary"></i></span>
                                                    <select name="Category" class="form-select" required>
                                                        <option value="" selected disabled>اختر التصنيف...</option>
                                                        <option value="محفظ">محفظ</option>
                                                        <option value="مسؤول">مسؤول</option>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-bold small text-muted">نوع التصنيف</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-light"><i
                                                            class="bi bi-pencil-square text-primary"></i></span>
                                                    <input type="text" name="ClassificationType" class="form-control"
                                                        placeholder="ادخل نوع التصنيف " required>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-footer bg-white border-0 mt-4 p-0">
                                    <div class="d-flex justify-content-start gap-2">
                                        <button type="submit" class="btn btn-success px-5 fw-bold"
                                            style="background-color: #28a745; border:none;">
                                            <i class="bi bi-check-circle me-1"></i> حفظ البيانات
                                        </button>
                                        <button type="reset" class="btn btn-light px-4 border">إعادة تعيين</button>
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
