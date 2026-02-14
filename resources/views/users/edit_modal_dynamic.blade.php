<style>
    .modal-xl-custom {
        max-width: 75% !important;
    }

    .section-title {
        font-size: 0.85rem;
        font-weight: bold;
        color: #e6b400;
        border-bottom: 1px solid #eee;
        padding-bottom: 5px;
        margin-bottom: 15px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .compact-body {
        padding: 1.5rem !important;
    }

    .compact-body label {
        font-size: 0.8rem;
        margin-bottom: 4px;
        color: #555;
        display: flex;
        align-items: center;
    }

    .compact-body label i {
        margin-left: 5px;
        color: #ffc107;
    }
</style>

<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-xl-custom">
        <form action="" method="POST" id="editUserForm" onsubmit="return validatePassword(event)">
            @csrf
            @method('PUT')
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; text-align: right;" dir="rtl">

                {{-- Header --}}
                <div
                    class="modal-header bg-warning text-dark border-0 py-2 d-flex flex-row-reverse justify-content-between align-items-center">
                    <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h6 class="modal-title fw-bold m-0">
                        <i class="bi bi-person-gear me-2"></i> تعديل بيانات المستخدم: <span
                            id="display_user_name"></span>
                    </h6>
                </div>

                <div class="modal-body compact-body bg-white">

                    {{-- القسم الأول: المعلومات الشخصية --}}
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge rounded-pill bg-warning text-dark px-3 fw-bold small-xs">1. البيانات الأساسية
                            والتواصل</span>
                        <hr class="flex-grow-1 me-2 my-0 opacity-10">
                    </div>
                    <div class="row gx-3 mb-4">
                        <div class="col-md-2 text-start">
                            <label class="fw-bold small"><i class="bi bi-person-badge text-warning"></i> الاسم
                                رباعي</label>
                            <input type="text" name="full_name" id="edit_full_name"
                                class="form-control bg-light border-0 py-2" required>
                        </div>
                        <div class="col-md-2 text-start">
                            <label class="fw-bold small"><i class="bi bi-card-text text-warning"></i> رقم الهوية</label>
                            <input type="text" name="id_number" id="edit_id_number"
                                class="form-control bg-light border-0 py-2" required>
                        </div>
                        <div class="col-md-2 text-start">
                            <label class="fw-bold small"><i class="bi bi-calendar3 text-warning"></i> تاريخ
                                الميلاد</label>
                            <input type="date" name="date_of_birth" id="edit_date_of_birth"
                                class="form-control bg-light border-0 py-2" required>
                        </div>
                        <div class="col-md-2 text-start">
                            <label class="fw-bold small"><i class="bi bi-phone text-warning"></i> رقم الجوال</label>
                            <input type="tel" name="phone_number" id="edit_phone_number"
                                class="form-control bg-light border-0 py-2" required>
                        </div>
                        <div class="col-md-2 text-start">
                            <label class="fw-bold small"><i class="bi bi-geo-alt text-warning"></i> العنوان
                                الحالي</label>
                            <input type="text" name="address" id="edit_address"
                                class="form-control bg-light border-0 py-2">
                        </div>
                        <div class="col-md-2 text-start">
                            <label class="fw-bold small"><i class="bi bi-house-check text-warning"></i> حالة
                                السكن</label>
                            <select name="is_displaced" id="edit_is_displaced"
                                class="form-select bg-light border-0 py-2">
                                <option value="0">مقيم</option>
                                <option value="1">نازح</option>
                            </select>
                        </div>
                    </div>
                    <div id="professional_section_wrapper"> {{-- القسم الثاني: البيانات المهنية (تظهر للجميع أو يتم التحكم بها عبر JS) --}}
                        <div id="professional_section">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge rounded-pill bg-warning text-dark px-3 fw-bold small-xs">2. البيانات
                                    المهنية والقرآنية</span>
                                <hr class="flex-grow-1 me-2 my-0 opacity-10">
                            </div>
                            <div class="row gx-3 mb-4">
                                <div class="col-md-2 text-start">
                                    <label class="fw-bold small"><i class="bi bi-whatsapp text-warning"></i> رقم
                                        الواتساب</label>
                                    <input type="tel" name="whatsapp_number" id="edit_whatsapp_number"
                                        class="form-control bg-light border-0 py-2">
                                </div>
                                <div class="col-md-2 text-start">
                                    <label class="fw-bold small"><i class="bi bi-mortarboard text-warning"></i> المؤهل
                                        العلمي</label>
                                    <input type="text" name="qualification" id="edit_qualification"
                                        class="form-control bg-light border-0 py-2">
                                </div>
                                <div class="col-md-2 text-start">
                                    <label class="fw-bold small"><i class="bi bi-briefcase text-warning"></i>
                                        التخصص</label>
                                    <input type="text" name="specialization" id="edit_specialization"
                                        class="form-control bg-light border-0 py-2">
                                </div>
                                <div class="col-md-2 text-start">
                                    <label class="fw-bold small"><i class="bi bi-book text-warning"></i> الأجزاء</label>
                                    <input type="number" name="parts_memorized" id="edit_parts_memorized"
                                        class="form-control bg-light border-0 py-2">
                                </div>
                                <div class="col-md-2 text-start">
                                    <label class="fw-bold small"><i class="bi bi-house-door text-warning"></i> اسم
                                        المسجد</label>
                                    <input type="text" name="mosque_name" id="edit_mosque_name"
                                        class="form-control bg-light border-0 py-2">
                                </div>
                                <div class="col-md-2 text-start">
                                    <label class="fw-bold small"><i class="bi bi-wallet2 text-warning"></i> رقم
                                        المحفظة</label>
                                    <input type="text" name="wallet_number" id="edit_wallet_number"
                                        class="form-control bg-light border-0 py-2">
                                </div>
                                <div class="col-md-2 text-start mt-2">
                                    <label class="fw-bold small"><i class="bi bi-map text-warning"></i> مكان
                                        الميلاد</label>
                                    <input type="text" name="birth_place" id="edit_birth_place"
                                        class="form-control bg-light border-0 py-2">
                                </div>
                                <div class="col-md-2 text-start mt-2">
                                    <label class="fw-bold small"><i class="bi bi-gender-ambiguous text-warning"></i>
                                        الجنس</label>
                                    <select name="gender" id="edit_gender"
                                        class="form-select bg-light border-0 py-2">
                                        <option value="male">ذكر</option>
                                        <option value="female">أنثى</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    {{-- القسم الثالث: الحساب والأمان --}}
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge rounded-pill bg-warning text-dark px-3 fw-bold small-xs">3. إعدادات الحساب
                            والأمان</span>
                        <hr class="flex-grow-1 me-2 my-0 opacity-10">
                    </div>

                    <div class="row gx-2 mb-4 align-items-start">
                        <div class="col">
                            <label class="fw-bold small"><i class="bi bi-person-lock text-warning"></i>
                                الصلاحية</label>
                            <select name="is_admin" id="edit_is_admin" class="form-select bg-light border-0 py-2"
                                onchange="toggleAdminCheckbox()">
                                <option value="1">مسؤول (أدمن)</option>
                                <option value="0">محفظ</option>
                            </select>
                        </div>

                        <div class="col-md-3" id="adminPrivilegeDiv">
                            <label class="form-label small fw-bold text-secondary mb-1">
                                <i class="bi bi-shield-check text-warning me-1"></i>صلاحيات إضافية
                            </label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" role="switch" name="is_admin_rouls"
                                    value="1" id="edit_is_admin_rouls">
                                <label class="form-check-label fw-bold text-dark" for="edit_is_admin_rouls">منح
                                    صلاحيات الأدمن كاملة</label>
                            </div>
                        </div>

                        <div class="col">
                            <label class="fw-bold small"><i class="bi bi-tags text-warning"></i> التصنيف</label>
                            <select name="category_id" id="edit_category_id"
                                class="form-select bg-light border-0 py-2">
                                @foreach (\DB::table('categorie')->get() as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col">
                            <label class="fw-bold small"><i class="bi bi-key text-warning"></i> كلمة المرور</label>
                            <input type="password" name="password" id="edit_password"
                                class="form-control bg-light border-0 py-2" placeholder="اختياري"
                                oninput="checkPasswordMatch()"autocomplete="new-password">
                        </div>

                        <div class="col">
                            <label class="fw-bold small"><i class="bi bi-key-fill text-warning"></i> تأكيد
                                المرور</label>
                            <input type="password" name="password_confirmation" id="edit_password_confirmation"
                                class="form-control bg-light border-0 py-2" oninput="checkPasswordMatch()"
                                placeholder="اختياري">
                            <div id="passwordError" class="text-danger fw-bold mt-1"
                                style="display: none; font-size: 0.75rem;">
                                <i class="bi bi-exclamation-circle"></i> <span id="errorText"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 p-3 bg-light d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-warning px-5 fw-bold shadow-sm rounded-pill">
                        <i class="bi bi-save2 me-2"></i> حفظ التعديلات
                    </button>
                    <button type="button" class="btn btn-outline-secondary px-4 rounded-pill"
                        data-bs-dismiss="modal">إلغاء</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // وظيفة لإظهار/إخفاء خيار "صلاحيات إضافية"
    function toggleAdminCheckbox() {
        const isAdmin = document.getElementById('edit_is_admin').value;
        const privilegeDiv = document.getElementById('adminPrivilegeDiv');
        const checkboxInput = document.getElementById('edit_is_admin_rouls');

        if (isAdmin == '1') {
            privilegeDiv.style.display = 'none';
            checkboxInput.checked = false;
        } else {
            privilegeDiv.style.display = 'block';
        }
    }

    // التحقق الفوري من تطابق كلمة المرور
    function checkPasswordMatch() {
        const pass = document.getElementById('edit_password').value;
        const confirmPass = document.getElementById('edit_password_confirmation').value;
        const errorDiv = document.getElementById('passwordError');
        const errorText = document.getElementById('errorText');

        if (confirmPass !== "" && pass !== confirmPass) {
            errorText.innerText = "كلمة المرور غير متطابقة";
            errorDiv.style.display = 'block';
        } else if (pass.length > 0 && pass.length < 6) {
            errorText.innerText = "يجب أن تكون 6 خانات على الأقل";
            errorDiv.style.display = 'block';
        } else {
            errorDiv.style.display = 'none';
        }
    }

    // التحقق النهائي قبل الإرسال
    function validatePassword(event) {
        const pass = document.getElementById('edit_password').value;
        const confirmPass = document.getElementById('edit_password_confirmation').value;

        if (pass.length > 0 && pass.length < 6) {
            event.preventDefault();
            alert("كلمة المرور قصيرة جداً");
            return false;
        }
        if (pass !== confirmPass) {
            event.preventDefault();
            alert("كلمات المرور غير متطابقة");
            return false;
        }
        return true;
    }
</script>
