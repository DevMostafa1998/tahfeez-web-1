<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المتابعة الأبوية | نظام التحفيظ</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/css/parent_login.css') }}">


</head>

<body>
    <div class="login-container animate-float">
        <div class="login-card">
            <div class="card-header-custom">
                <div class="logo-wrapper animate__animated animate__zoomIn">
                    <img src="{{ asset('assets/img/logo.jpeg') }}" alt="الشعار">
                </div>

                <h4 class="fw-800 mb-1">المتابعة الأبوية</h4>
                <p class="small opacity-75 mb-0">أدخل رقم هوية الطالب لمتابعة تقدمه</p>
            </div>

            <div class="card-body p-4 pt-4">
                @if ($errors->has('message'))
                    <div class="alert alert-danger">
                        {{ $errors->first('message') }}
                    </div>
                @endif
                <form id="parentLoginForm" onsubmit="event.preventDefault(); redirectToReport();">
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark small">رقم هوية الطالب</label>
                        <input type="text" id="student_id_input" class="form-control"
                            placeholder="أدخل رقم الهوية المكون من 9 أرقام" inputmode="numeric"
                            oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-login text-white">
                            عرض النتائج <i class="bi bi-search ms-2"></i>
                        </button>
                        <a href="{{ route('login') }}" class="btn btn-link btn-sm text-muted text-decoration-none">
                            <i class="bi bi-arrow-right"></i> العودة لتسجيل الدخول الرئيسية
                        </a>


                    </div>
                </form>
            </div>

            <script>
                function redirectToReport() {
                    const idNumber = document.getElementById('student_id_input').value;
                    if (idNumber) {
                        window.location.href = "{{ url('/parents') }}/" + idNumber;
                    }
                }
            </script>
