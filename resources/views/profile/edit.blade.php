@extends('layouts.app')

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .viewport-center {
            min-height: calc(100vh - 70px);
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8fafc;
            padding: 20px;
            font-family: 'Cairo', sans-serif;
            /* يفضل استخدام خط عربي */
        }

        .main-card {
            width: 100%;
            max-width: 950px;
            background: #ffffff;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            overflow: hidden;
            direction: rtl;
            /* تفعيل الاتجاه من اليمين لليسار */
            text-align: right;
        }

        .glass-header {
            background: #fcfcfd;
            padding: 20px 30px;
            border-bottom: 1px solid #f1f5f9;
        }

        .glass-header h5 {
            margin: 0;
            font-weight: 800;
            color: #1e293b;
        }

        /* إصلاح مظهر الأيقونة والحقل */
        .input-group {
            flex-direction: row-reverse;
            /* وضع الأيقونة يسار النص في الـ RTL */
        }

        .form-control-modern {
            border-radius: 0 10px 10px 0 !important;
            /* حواف مستديرة من اليمين */
            border: 1px solid #e2e8f0;
            padding: 10px 15px;
            text-align: right;
        }

        .input-group-text {
            border-radius: 10px 0 0 10px !important;
            /* حواف مستديرة من اليسار للأيقونة */
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-right: none;
            /* إزالة الخط الفاصل المزدوج */
            color: #64748b;
            min-width: 45px;
            justify-content: center;
        }

        .read-only-box {
            background: #f1f5f9;
            border-radius: 12px;
            padding: 15px;
            border: 1px solid #e2e8f0;
        }

        .section-divider {
            border: 0;
            height: 1px;
            background-image: linear-gradient(to left, rgba(0, 0, 0, 0), rgba(226, 232, 240, 1), rgba(0, 0, 0, 0));
            margin: 30px 0;
            opacity: 0.6;
        }

        .section-title {
            font-size: 0.85rem;
            font-weight: 800;
            color: #475569;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary-custom {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 10px;
            font-weight: 600;
            width: 100%;
        }

        .btn-dark-custom {
            background: #1e293b;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 10px;
            font-weight: 600;
            width: 100%;
        }

        .password-footer {
            background: #f8fafc;
            padding: 25px 35px;
            border-top: 1px solid #e2e8f0;
        }
    </style>

    <div class="viewport-center">
        <div class="main-card">
            <div class="glass-header">
                <h5><i class="fas fa-user-circle ms-2 text-primary"></i> إعدادات الحساب الشخصي</h5>
            </div>

            <div class="p-4">
                <form method="POST" class="profile-form" action="{{ route('profile.update', auth()->user()->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="section-title text-muted">
                        <i class="fas fa-database ml-1"></i> بيانات رسمية
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">رقم الهوية</label>
                            <div class="input-group">
                                <input type="text" name="id_number" class="form-control form-control-modern"
                                    value="{{ old('id_number', $user->id_number) }}"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                                <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">تاريخ الميلاد</label>
                            <div class="input-group">
                                <input type="date" name="date_of_birth" class="form-control form-control-modern"
                                    value="{{ old('date_of_birth', \Carbon\Carbon::parse($user->date_of_birth)->format('Y-m-d')) }}"
                                    required>
                                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">الفئة الوظيفية</label>
                            <div class="read-only-box" style="padding: 8px 15px; background: #f1f5f9;">
                                <span class="fw-bold text-muted">{{ $categoryName ?? 'محفظ' }}</span>
                            </div>
                        </div>
                    </div>
                    <hr>
                    <div class="section-title text-primary">
                        <i class="fas fa-sync-alt ml-1"></i> تحديث بيانات التواصل
                    </div>

                    <div class="row g-3 align-items-end">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">الاسم الكامل</label>
                            <div class="input-group">
                                <input type="text" name="full_name" class="form-control form-control-modern"
                                    value="{{ $user->full_name }}"
                                    oninput="this.value = this.value.replace(/[0-9!@#$%^&*()_+={}\[\]:;<>,.?\/|\\]/g, '')">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">رقم الهاتف</label>
                            <div class="input-group">
                                <input type="text" name="phone_number" class="form-control form-control-modern"
                                    value="{{ $user->phone_number }}"
                                    oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label small fw-bold">العنوان السكني</label>
                            <div class="input-group">
                                <input type="text" name="address" class="form-control form-control-modern"
                                    value="{{ $user->address }}">
                                <span class="input-group-text"><i class="fas fa-map-marker-alt"></i></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn-primary-custom">
                                <i class="fas fa-save ms-1"></i> حفظ البيانات
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="password-footer">
                <div class="section-title text-danger">
                    <i class="fas fa-shield-alt ml-1"></i> أمان الحساب (تغيير كلمة السر)
                </div>

                <form method="POST" class="profile-form" action="{{ route('profile.update', auth()->user()->id) }}">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="password" name="password" class="form-control form-control-modern"
                                    placeholder="كلمة المرور الجديدة" autocomplete="new-password">
                                <span class="input-group-text"><i class="fas fa-key"></i></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="password" name="password_confirmation" class="form-control form-control-modern"
                                    placeholder="تأكيد كلمة المرور">
                                <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn-dark-custom">
                                <i class="fas fa-lock ms-1"></i> تحديث كلمة السر
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        $('.profile-form').on('submit', function(e) {
            e.preventDefault(); // منع الصفحة من التحديث

            let form = $(this);
            let formData = form.serialize();
            let submitBtn = form.find('button[type="submit"]');

            // تعطيل الزر مؤقتاً
            submitBtn.prop('disabled', true).html(
                '<i class="fas fa-spinner fa-spin"></i> جاري الحفظ...');

            $.ajax({
                url: form.attr('action'),
                method: 'POST',
                data: formData,
                success: function(response) {
                    Swal.fire({
                        icon: 'success',
                        title: 'تم بنجاح',
                        text: response.message,
                        confirmButtonText: 'ممتاز',
                        timer: 3000
                    });
                    submitBtn.prop('disabled', false).html(
                        '<i class="fas fa-save ms-1"></i> حفظ البيانات');
                    if (form.find('input[name="password"]').length > 0) form.trigger(
                        'reset'); // تفريغ حقول كلمة السر
                },
                error: function(xhr) {
                    let errorMsg = 'عذراً، حدث خطأ ما';
                    if (xhr.status === 422) {
                        // استخراج أول خطأ من قائمة الأخطاء
                        let errors = xhr.responseJSON.errors;
                        errorMsg = Object.values(errors)[0][0];
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ في التحديث',
                        text: errorMsg,
                        confirmButtonText: 'حاول مجدداً'
                    });
                    submitBtn.prop('disabled', false).html('حفظ البيانات');
                }
            });
        });
    });
</script>
