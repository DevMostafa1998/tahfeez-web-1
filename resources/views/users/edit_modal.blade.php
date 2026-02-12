<style>
    /* تكبير عرض المودل ليأخذ أغلب مساحة الشاشة أفقياً */
    .modal-xl-custom {
        max-width: 75% !important;
    }

    /* عناوين الأقسام لتنظيم الحقول */
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

    .compact-body .col-md-2,
    .compact-body .col-md-3,
    .compact-body .col-md-4 {
        margin-bottom: 10px;
    }

    .compact-body label {
        font-size: 0.8rem;
        margin-bottom: 4px;
        color: #555;
        display: flex;
        align-items: center;
    }

    /* تنسيق الأيقونة بجانب التسمية */
    .compact-body label i {
        margin-left: 5px;
        /* مسافة بين الأيقونة والنص في RTL */
        color: #ffc107;
        /* لون الأيقونات ليناسب طابع المودل */
    }
</style>

<div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-xl-custom">
        <form action="{{ route('user.update', $user->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content border-0 shadow-lg" style="border-radius: 15px; text-align: right;" dir="rtl">

                {{-- Header --}}
                <div
                    class="modal-header bg-warning text-dark border-0 py-2 d-flex flex-row-reverse justify-content-between align-items-center">
                    <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h6 class="modal-title fw-bold m-0">
                        <i class="bi bi-person-gear me-2"></i>تعديل بيانات المستخدم: {{ $user->full_name }}
                    </h6>
                </div>

                <div class="modal-body compact-body bg-white">

                    {{-- القسم الأول: المعلومات الشخصية --}}
                    <div class="d-flex align-items-center mb-2">
                        <span class="badge rounded-pill bg-warning text-dark px-3 fw-bold small-xs">
                            1. البيانات الأساسية والتواصل</span>
                        <hr class="flex-grow-1 me-2 my-0 opacity-10">
                    </div>
                    <div class="row gx-3 mb-4">
                        <div class="col-md-2 text-start">
                            <label class="fw-bold small"><i class="bi bi-person-badge text-warning"></i> الاسم
                                رباعي</label>
                            <input type="text" name="full_name" class="form-control bg-light border-0 py-2"
                                value="{{ $user->full_name }}" required>
                        </div>
                        <div class="col-md-2 text-start">
                            <label class="fw-bold small"><i class="bi bi-card-text text-warning"></i> رقم الهوية</label>
                            <input type="text" name="id_number" class="form-control bg-light border-0 py-2"
                                value="{{ $user->id_number }}" required>
                        </div>
                        <div class="col-md-2 text-start">
                            <label class="fw-bold small"><i class="bi bi-calendar3 text-warning"></i> تاريخ
                                الميلاد</label>
                            <input type="date" name="date_of_birth" class="form-control bg-light border-0 py-2"
                                value="{{ \Carbon\Carbon::parse($user->date_of_birth)->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-2 text-start">
                            <label class="fw-bold small"><i class="bi bi-phone text-warning"></i> رقم الجوال</label>
                            <input type="tel" name="phone_number" class="form-control bg-light border-0 py-2"
                                value="{{ $user->phone_number }}" required>
                        </div>
                        <div class="col-md-2 text-start">
                            <label class="fw-bold small"><i class="bi bi-geo-alt text-warning"></i> العنوان
                                الحالي</label>
                            <input type="text" name="address" class="form-control bg-light border-0 py-2"
                                value="{{ $user->address }}">
                        </div>
                        {{-- نقلنا حالة السكن هنا لتكون متاحة للكل ولا تكسر التصميم --}}
                        <div class="col-md-2 text-start">
                            <label class="fw-bold small"><i class="bi bi-house-check text-warning"></i> حالة
                                السكن</label>
                            <select name="is_displaced" class="form-select bg-light border-0 py-2">
                                <option value="0" {{ !$user->is_displaced ? 'selected' : '' }}>مقيم</option>
                                <option value="1" {{ $user->is_displaced ? 'selected' : '' }}>نازح</option>
                            </select>
                        </div>
                    </div>

                    {{-- القسم الثاني: البيانات المهنية والقرآنية --}}
                    @if ($user->is_admin == 0)
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge rounded-pill bg-warning text-dark px-3 fw-bold small-xs">
                                2. البيانات المهنية والقرآنية</span>
                            <hr class="flex-grow-1 me-2 my-0 opacity-10">
                        </div>
                        <div class="row gx-3 mb-4">
                            <div class="col-md-2 text-start">
                                <label class="fw-bold small"><i class="bi bi-whatsapp text-warning"></i> رقم
                                    الواتساب</label>
                                <input type="tel" name="whatsapp_number" class="form-control bg-light border-0 py-2"
                                    value="{{ $user->whatsapp_number }}">
                            </div>
                            <div class="col-md-2 text-start">
                                <label class="fw-bold small"><i class="bi bi-mortarboard text-warning"></i> المؤهل
                                    العلمي</label>
                                <input type="text" name="qualification" class="form-control bg-light border-0 py-2"
                                    value="{{ $user->qualification }}">
                            </div>
                            <div class="col-md-2 text-start">
                                <label class="fw-bold small"><i class="bi bi-briefcase text-warning"></i> التخصص</label>
                                <input type="text" name="specialization" class="form-control bg-light border-0 py-2"
                                    value="{{ $user->specialization }}">
                            </div>
                            <div class="col-md-2 text-start">
                                <label class="fw-bold small"><i class="bi bi-book text-warning"></i> الأجزاء</label>
                                <input type="number" name="parts_memorized" class="form-control bg-light border-0 py-2"
                                    value="{{ $user->parts_memorized }}">
                            </div>
                            <div class="col-md-2 text-start">
                                <label class="fw-bold small"><i class="bi bi-house-door text-warning"></i> اسم
                                    المسجد</label>
                                <input type="text" name="mosque_name" class="form-control bg-light border-0 py-2"
                                    value="{{ $user->mosque_name }}">
                            </div>
                            <div class="col-md-2 text-start">
                                <label class="fw-bold small"><i class="bi bi-wallet2 text-warning"></i> رقم
                                    المحفظة</label>
                                <input type="text" name="wallet_number"
                                    class="form-control bg-light border-0 py-2" value="{{ $user->wallet_number }}">
                            </div>
                            <div class="col-md-2 text-start mt-2">
                                <label class="fw-bold small"><i class="bi bi-map text-warning"></i> مكان
                                    الميلاد</label>
                                <input type="text" name="birth_place" class="form-control bg-light border-0 py-2"
                                    value="{{ $user->birth_place }}">
                            </div>
                            <div class="col-md-2 text-start">
                                <label class="fw-bold small"><i class="bi bi-map text-warning"></i> الجنس</label>

                                <select name="gender" class="form-select bg-light border-0 py-2">
                                    <option value="male" {{ $user->gender == 'male' ? 'selected' : '' }}>ذكر
                                    </option>
                                    <option value="female" {{ $user->gender == 'female' ? 'selected' : '' }}>أنثى
                                    </option>
                                </select>
                            </div>
                        </div>
                    @endif

                    {{-- القسم الثالث: الحساب والأمان --}}
                <div class="d-flex align-items-center mb-2">
                    <span class="badge rounded-pill bg-warning text-dark px-3 fw-bold small-xs">
                        3. إعدادات الحساب والأمان
                    </span>
                    <hr class="flex-grow-1 me-2 my-0 opacity-10">
                </div>

                <div class="row gx-3">
                    <div class="col-md-3 text-start mb-2">
                        <label class="fw-bold small">
                            <i class="bi bi-person-lock text-warning"></i> الصلاحية
                        </label>
                        <select name="is_admin" id="userType{{ $user->id }}" class="form-select bg-light border-0 py-2"
                                onchange="toggleAdminCheckbox({{ $user->id }})">
                            <option value="1" {{ $user->is_admin == 1 ? 'selected' : '' }}>مسؤول (أدمن)</option>
                            <option value="0" {{ $user->is_admin == 0 ? 'selected' : '' }}>محفظ</option>
                        </select>
                    </div>

                    <div class="col-md-3 text-start mb-2" id="adminPrivilegeDiv{{ $user->id }}"
                        style="{{ $user->is_admin == 1 ? 'display: none;' : '' }}">

                        <label class="fw-bold small text-muted">
                            <i class="bi bi-shield-plus text-warning"></i> صلاحيات إضافية
                        </label>

                        <div class="form-control bg-light border-0 d-flex justify-content-between align-items-center py-2"
                            style="border-radius: 8px; height: 38px;">

                            <label class="form-check-label small fw-bold text-dark mb-0" for="is_admin_rouls{{ $user->id }}">
                                منح صلاحيات مشرف
                            </label>

                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input" type="checkbox" name="is_admin_rouls" value="1"
                                    id="is_admin_rouls{{ $user->id }}"
                                    style="cursor: pointer; width: 2.5em;"
                                    {{ $user->is_admin_rouls == 1 ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3 text-start">
                        <label class="fw-bold small"><i class="bi bi-tags text-warning"></i> التصنيف</label>
                        <select name="category_id" class="form-select bg-light border-0 py-2">
                            @foreach (\DB::table('categorie')->get() as $cat)
                                <option value="{{ $cat->id }}" {{ $user->category_id == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 text-start">
                        <label class="fw-bold small"><i class="bi bi-key text-warning"></i> كلمة المرور</label>
                        <input type="password" name="password" class="form-control bg-light border-0 py-2" placeholder="اختياري">
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
    function toggleAdminCheckbox(userId) {
        const userTypeSelect = document.getElementById('userType' + userId);
        const privilegeDiv = document.getElementById('adminPrivilegeDiv' + userId);
        const checkboxInput = document.getElementById('is_admin_rouls' + userId);
        if (userTypeSelect && privilegeDiv) {
            if (userTypeSelect.value == '1') {
                privilegeDiv.style.display = 'none';
                if(checkboxInput) checkboxInput.checked = false;
            } else {
                privilegeDiv.style.display = 'block';
            }
        }
    }
</script>
