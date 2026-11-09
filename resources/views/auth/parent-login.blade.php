<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>المتابعة الأبوية | نظام التحفيظ</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght=400;600;700;800&display=swap" rel="stylesheet">
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
                        <button type="submit" class="btn btn-login text-white mb-2">
                            عرض النتائج <i class="bi bi-search ms-2"></i>
                        </button>

                        <div class="row g-2 mb-2">
                            <div class="col-6">
                                <button type="button" class="btn btn-sm btn-secondary w-100 py-2 fs-7"
                                    onclick="window.location.href='{{ url('/student_public') }}'">
                                    <i class="bi bi-person-plus"></i> طالب جديد
                                </button>
                            </div>
                            <div class="col-6">
                                <button type="button" class="btn btn-sm btn-warning text-dark w-100 py-2 fs-7 fw-bold"
                                    data-bs-toggle="modal" data-bs-target="#verifyStudentModal">
                                    <i class="bi bi-pencil-square"></i> تعديل بيانات الطالب
                                </button>
                            </div>
                        </div>

                        <a href="{{ route('login') }}" class="btn btn-link btn-sm text-muted text-decoration-none">
                            <i class="bi bi-arrow-right"></i> العودة لتسجيل الدخول الرئيسية
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- مودال التحقق الحالي --}}
    <div class="modal fade" id="verifyStudentModal" data-bs-backdrop="static" tabindex="-1"
        aria-labelledby="verifyStudentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-bold" id="verifyStudentModalLabel"><i
                            class="bi bi-shield-lock-fill me-2"></i> التحقق من الأمن والخصوصية</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="verificationForm" onsubmit="event.preventDefault(); verifyStudentData();">
                    @csrf
                    <div class="modal-body p-4">
                        <div id="verifyError" class="alert alert-danger animate__animated animate__shakeX d-none"></div>
                        <p class="text-muted small mb-4">يرجى إدخال رقم هوية الطالب بشكل دقيق لفتح صلاحية تعديل البيانات
                            :</p>

                        <div class="mb-3">
                            <label class="form-label fw-bold small text-dark">رقم هوية الطالب</label>
                            <input type="text" id="verify_student_id" class="form-control" placeholder="9 أرقام"
                                maxlength="9" oninput="this.value = this.value.replace(/[^0-9]/g, '')" required>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-secondary btn-sm px-4 rounded-pill"
                            data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" id="btnVerifySubmit"
                            class="btn btn-warning btn-sm px-4 rounded-pill fw-bold text-dark">
                            <span id="verifySpinner" class="spinner-border spinner-border-sm d-none me-1" role="status"
                                aria-hidden="true"></span>
                            تحقق ومتابعة <i class="bi bi-arrow-left-short"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @php
        $categories = \DB::table('categorie')->get();
        $groups = \App\Models\Group::all();
        $student_courses = \App\Models\Course::where(function ($q) {
            $q->where('type', 'students')->orWhereNull('type');
        })->get();
    @endphp

    {{-- استدعاء وتضمين المودال الموحد الخاص بك --}}
    @include('students.edit_modal_unified')

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function redirectToReport() {
            const idNumber = document.getElementById('student_id_input').value;
            if (idNumber) {
                window.location.href = "{{ url('/parents') }}/" + idNumber;
            }
        }

        function verifyStudentData() {
            const studentId = document.getElementById('verify_student_id').value;

            const errorDiv = document.getElementById('verifyError');
            const spinner = document.getElementById('verifySpinner');
            const btnSubmit = document.getElementById('btnVerifySubmit');

            errorDiv.classList.add('d-none');
            spinner.classList.remove('d-none');
            btnSubmit.disabled = true;

            fetch("{{ route('parent.verify-student') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    },
                    body: JSON.stringify({
                        student_id: studentId
                        // تم إزالة father_id و date_of_birth بنجاح
                    })
                })
                .then(response => response.json())
                .then(data => {
                    spinner.classList.add('d-none');
                    btnSubmit.disabled = false;

                    if (data.success) {
                        const verifyModal = bootstrap.Modal.getInstance(document.getElementById('verifyStudentModal'));
                        if (verifyModal) verifyModal.hide();

                        const student = data.student;

                        document.getElementById('editStudentForm').action = "/student_public/" + student.id;
                        document.getElementById('edit_student_id').value = student.id;
                        document.getElementById('edit_full_name').value = student.full_name || student.name;
                        document.getElementById('edit_id_number').value = student.id_number;
                        document.getElementById('edit_father_id').value = student.father_id;

                        if (student.date_of_birth) {
                            let dob = student.date_of_birth.split('T')[0];
                            document.getElementById('edit_date_of_birth').value = dob;
                        }

                        document.getElementById('edit_birth_place').value = student.birth_place || '';
                        document.getElementById('edit_gender').value = student.gender || 'male';
                        document.getElementById('edit_categorie_id').value = student.categorie_id || '';
                        document.getElementById('edit_phone_number').value = student.phone_number || student.phone ||
                        '';
                        document.getElementById('edit_whatsapp_number').value = student.whatsapp_number || '';
                        document.getElementById('edit_address').value = student.address || '';
                        document.getElementById('edit_is_displaced').value = student.is_displaced || 0;

                        const unifiedModal = new bootstrap.Modal(document.getElementById('unifiedEditModal'));
                        unifiedModal.show();

                    } else {
                        errorDiv.textContent = data.message || "الرقم المدخل غير صحيح، يرجى التحقق مجدداً.";
                        errorDiv.classList.remove('d-none');
                    }
                })
                .catch(error => {
                    spinner.classList.add('d-none');
                    btnSubmit.disabled = false;
                    errorDiv.textContent = "حدث خطأ غير متوقع في الخادم، يرجى المحاولة لاحقاً.";
                    errorDiv.classList.remove('d-none');
                    console.error(error);
                });
        }
    </script>
</body>

</html>
