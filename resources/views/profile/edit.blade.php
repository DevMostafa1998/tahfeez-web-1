@extends('layouts.app')

@section('content')
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;500;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="{{ asset('assets/css/profile-edit.css') }}">

    <div class="premium-container">
        <div class="ultra-card">
            <div class="hero-sidebar">

                <div class="avatar-container">
                    <div class="avatar-glow">
                        @if ($user->is_admin == 1)
                            <i class="fas fa-user-shield"></i>
                        @else
                            <i class="fas fa-user-tie"></i>
                        @endif
                    </div>

                    <h3 class="fw-bold mb-0">{{ $user->full_name }}</h3>

                    {{-- عرض الرتبة (مسؤول أو محفظ مع الفئة) --}}
                    <div class="rank-wrapper">
                        @if ($user->is_admin == 1)
                            <div class="rank-badge admin-rank">
                                <i class="fas fa-star ms-1"></i> مسؤول النظام
                            </div>
                        @else
                            <div class="rank-badge teacher-rank">
                                <i class="fas fa-book-reader ms-1"></i> محفظ
                            </div>
                            {{-- عرض الفئة الخاصة بالمحفظ فقط --}}
                            @if (isset($categoryName))
                                <div class="category-tag">
                                    <i class="fas fa-award ms-1"></i> {{ $categoryName }}
                                </div>
                            @endif
                        @endif
                    </div>
                </div>

                <div class="nav-list">
                    <div class="nav-item-fancy active" onclick="openSection('official', this)">
                        <i class="fas fa-fingerprint"></i> البيانات الرسمية
                    </div>

                    {{-- عرض زر القسم التعليمي فقط إذا كان محفظ --}}
                    @if ($user->is_admin == 0)
                        <div class="nav-item-fancy" onclick="openSection('education', this)">
                            <i class="fas fa-graduation-cap"></i> التعليم والقرآن
                        </div>
                    @endif

                    <div class="nav-item-fancy" onclick="openSection('social', this)">
                        <i class="fas fa-share-nodes"></i> التواصل والمعيشة
                    </div>
                    <div class="nav-item-fancy" onclick="openSection('privacy', this)">
                        <i class="fas fa-key"></i> الحماية والخصوصية
                    </div>
                </div>

            </div>

            <div class="form-display">
                <form action="{{ route('profile.update', $user->id) }}" method="POST" id="fancy-ajax-form">
                    @csrf
                    @method('PUT')

                    <div id="official" class="content-tab active">
                        <h2 class="fw-bold mb-4">بيانات الهوية</h2>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="fancy-input-group">
                                    <label>رقم الهوية</label>
                                    <input type="text" name="id_number" class="input-box" value="{{ $user->id_number }}"
                                        required>
                                    <i class="fas fa-id-card input-icon"></i>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="fancy-input-group">
                                    <label>تاريخ الميلاد</label>
                                    <input type="date" name="date_of_birth" class="input-box"
                                        value="{{ \Carbon\Carbon::parse($user->date_of_birth)->format('Y-m-d') }}" required>
                                    <i class="fas fa-calendar-alt input-icon"></i>
                                </div>
                            </div>
                            @if ($user->is_admin == 0)
                                <div class="col-md-12">
                                    <div class="fancy-input-group">
                                        <label>مكان الميلاد</label>
                                        <input type="text" name="birth_place" class="input-box"
                                            value="{{ $user->birth_place }}">
                                        <i class="fas fa-baby input-icon"></i>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    @if ($user->is_admin == 0)
                        <div id="education" class="content-tab">
                            <h2 class="fw-bold mb-4">المؤهلات والقرآن الكريم</h2>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="fancy-input-group">
                                        <label>المؤهل العلمي</label>
                                        <input type="text" name="qualification" class="input-box"
                                            value="{{ $user->qualification }}">
                                        <i class="fas fa-user-graduate input-icon"></i>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="fancy-input-group">
                                        <label>التخصص</label>
                                        <input type="text" name="specialization" class="input-box"
                                            value="{{ $user->specialization }}">
                                        <i class="fas fa-book input-icon"></i>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="fancy-input-group">
                                        <label>أجزاء الحفظ</label>
                                        <input type="number" name="parts_memorized" class="input-box"
                                            value="{{ $user->parts_memorized }}">
                                        <i class="fas fa-quran input-icon"></i>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="fancy-input-group">
                                        <label>اسم المسجد</label>
                                        <input type="text" name="mosque_name" class="input-box"
                                            value="{{ $user->mosque_name }}">
                                        <i class="fas fa-mosque input-icon"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div id="social" class="content-tab">
                        <h2 class="fw-bold mb-4">معلومات الاتصال</h2>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="fancy-input-group">
                                    <label>الاسم الكامل</label>
                                    <input type="text" name="full_name" class="input-box"
                                        value="{{ $user->full_name }}" required>
                                    <i class="fas fa-signature input-icon"></i>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="fancy-input-group">
                                    <label>الجوال</label>
                                    <input type="text" name="phone_number" class="input-box"
                                        value="{{ $user->phone_number }}" required>
                                    <i class="fas fa-phone-volume input-icon"></i>
                                </div>
                            </div>

                            {{-- حقول إضافية للمحفظ فقط في قسم التواصل --}}
                            @if ($user->is_admin == 0)
                                <div class="col-md-6">
                                    <div class="fancy-input-group">
                                        <label>واتساب</label>
                                        <input type="text" name="whatsapp_number" class="input-box"
                                            value="{{ $user->whatsapp_number }}">
                                        <i class="fab fa-whatsapp input-icon"></i>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="fancy-input-group">
                                        <label>رقم المحفظة الإلكترونية</label>
                                        <input type="text" name="wallet_number" class="input-box"
                                            value="{{ $user->wallet_number }}">
                                        <i class="fas fa-wallet input-icon"></i>
                                    </div>
                                </div>
                            @endif

                            <div class="col-md-6">
                                <div class="fancy-input-group">
                                    <label>العنوان</label>
                                    <input type="text" name="address" class="input-box" value="{{ $user->address }}"
                                        required>
                                    <i class="fas fa-map-marked-alt input-icon"></i>
                                </div>
                            </div>

                            @if ($user->is_admin == 0)
                                <div class="col-md-6">
                                    <div class="fancy-input-group">
                                        <label>هل أنت نازح؟</label>
                                        <select name="is_displaced" class="input-box">
                                            <option value="0" {{ $user->is_displaced == 0 ? 'selected' : '' }}>لا
                                            </option>
                                            <option value="1" {{ $user->is_displaced == 1 ? 'selected' : '' }}>نعم
                                            </option>
                                        </select>
                                        <i class="fas fa-house-damage input-icon"></i>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div id="privacy" class="content-tab">
                        <h2 class="fw-bold mb-4">تأمين الحساب</h2>
                        <div class="fancy-input-group">
                            <label>كلمة المرور الجديدة</label>
                            <input type="password" name="password" class="input-box"
                                placeholder="تركها فارغة يبقي القديمة">
                            <i class="fas fa-shield-virus input-icon"></i>
                        </div>
                        <div class="fancy-input-group">
                            <label>تأكيد كلمة المرور</label>
                            <input type="password" name="password_confirmation" class="input-box"
                                placeholder="أعد كتابة الرمز">
                            <i class="fas fa-shield-check input-icon"></i>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="glow-button" id="main-submit">
                            <i class="fas fa-magic ms-2"></i> تحديث الملف الشخصي الآن
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function openSection(tabId, element) {
            $('.content-tab').removeClass('active');
            $('.nav-item-fancy').removeClass('active');
            $('#' + tabId).addClass('active');
            $(element).addClass('active');
        }

        $(document).ready(function() {
            $('#fancy-ajax-form').on('submit', function(e) {
                e.preventDefault();
                let btn = $('#main-submit');
                let form = $(this);
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> جاري المزامنة...');
                $.ajax({
                    url: form.attr('action'),
                    method: 'POST',
                    data: form.serialize(),
                    success: function(response) {
                        Swal.fire({
                            icon: 'success',
                            title: 'تم التحديث',
                            text: response.message
                        });
                        btn.prop('disabled', false).html(
                            '<i class="fas fa-magic ms-2"></i> تحديث الملف الشخصي الآن');
                    },
                    error: function(xhr) {
                        Swal.fire({
                            icon: 'error',
                            title: 'فشل التحديث',
                            text: 'خطأ في البيانات'
                        });
                        btn.prop('disabled', false).html(
                            '<i class="fas fa-magic ms-2"></i> تحديث الملف الشخصي الآن');
                    }
                });
            });
        });
    </script>
@endsection
