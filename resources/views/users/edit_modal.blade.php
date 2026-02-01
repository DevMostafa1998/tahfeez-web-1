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
                    <div class="section-title">
                        <i class="bi bi-info-circle-fill"></i> البيانات الأساسية والتواصل
                    </div>
                    <div class="row gx-3 mb-4">
                        <div class="col-md-2 text-start">
                            <label class="fw-bold small"><i class="bi bi-person-badge"></i> الاسم رباعي</label>
                            <input type="text" name="full_name" class="form-control bg-light border-0 py-2"
                                value="{{ $user->full_name }}" required>
                        </div>
                        <div class="col-md-2 text-start">
                            <label class="fw-bold small"><i class="bi bi-card-text"></i> رقم الهوية</label>
                            <input type="text" name="id_number" class="form-control bg-light border-0 py-2"
                                value="{{ $user->id_number }}" required>
                        </div>
                        <div class="col-md-2 text-start">
                            <label class="fw-bold small"><i class="bi bi-calendar3"></i> تاريخ الميلاد</label>
                            <input type="date" name="date_of_birth" class="form-control bg-light border-0 py-2"
                                value="{{ \Carbon\Carbon::parse($user->date_of_birth)->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-2 text-start">
                            <label class="fw-bold small"><i class="bi bi-phone"></i> رقم الجوال</label>
                            <input type="tel" name="phone_number" class="form-control bg-light border-0 py-2"
                                value="{{ $user->phone_number }}" required>
                        </div>
                        <div class="col-md-2 text-start">
                            <label class="fw-bold small"><i class="bi bi-geo-alt"></i> العنوان الحالي</label>
                            <input type="text" name="address" class="form-control bg-light border-0 py-2"
                                value="{{ $user->address }}">
                        </div>
                        {{-- نقلنا حالة السكن هنا لتكون متاحة للكل ولا تكسر التصميم --}}
                        <div class="col-md-2 text-start">
                            <label class="fw-bold small"><i class="bi bi-house-check"></i> حالة السكن</label>
                            <select name="is_displaced" class="form-select bg-light border-0 py-2">
                                <option value="0" {{ !$user->is_displaced ? 'selected' : '' }}>مقيم</option>
                                <option value="1" {{ $user->is_displaced ? 'selected' : '' }}>نازح</option>
                            </select>
                        </div>
                    </div>

                    {{-- القسم الثاني: البيانات المهنية والقرآنية --}}
                    @if ($user->is_admin == 0)
                        <div class="section-title text-info"
                            style="color: #0dcaf0 !important; border-color: #0dcaf033;">
                            <i class="bi bi-mortarboard-fill"></i> البيانات المهنية والقرآنية
                        </div>
                        <div class="row gx-3 mb-4">
                            <div class="col-md-2 text-start">
                                <label class="fw-bold small"><i class="bi bi-whatsapp"></i> رقم الواتساب</label>
                                <input type="tel" name="whatsapp_number" class="form-control bg-light border-0 py-2"
                                    value="{{ $user->whatsapp_number }}">
                            </div>
                            <div class="col-md-2 text-start">
                                <label class="fw-bold small"><i class="bi bi-mortarboard"></i> المؤهل العلمي</label>
                                <input type="text" name="qualification" class="form-control bg-light border-0 py-2"
                                    value="{{ $user->qualification }}">
                            </div>
                            <div class="col-md-2 text-start">
                                <label class="fw-bold small"><i class="bi bi-briefcase"></i> التخصص</label>
                                <input type="text" name="specialization" class="form-control bg-light border-0 py-2"
                                    value="{{ $user->specialization }}">
                            </div>
                            <div class="col-md-2 text-start">
                                <label class="fw-bold small"><i class="bi bi-book"></i> الأجزاء</label>
                                <input type="number" name="parts_memorized" class="form-control bg-light border-0 py-2"
                                    value="{{ $user->parts_memorized }}">
                            </div>
                            <div class="col-md-2 text-start">
                                <label class="fw-bold small"><i class="bi bi-house-door"></i> اسم المسجد</label>
                                <input type="text" name="mosque_name" class="form-control bg-light border-0 py-2"
                                    value="{{ $user->mosque_name }}">
                            </div>
                            <div class="col-md-2 text-start">
                                <label class="fw-bold small"><i class="bi bi-wallet2"></i> رقم المحفظة</label>
                                <input type="text" name="wallet_number"
                                    class="form-control bg-light border-0 py-2" value="{{ $user->wallet_number }}">
                            </div>
                            <div class="col-md-2 text-start mt-2">
                                <label class="fw-bold small"><i class="bi bi-map"></i> مكان الميلاد</label>
                                <input type="text" name="birth_place" class="form-control bg-light border-0 py-2"
                                    value="{{ $user->birth_place }}">
                            </div>
                        </div>
                    @endif

                    {{-- القسم الثالث: الحساب والأمان --}}
                    <div class="section-title text-success"
                        style="color: #198754 !important; border-color: #19875433;">
                        <i class="bi bi-shield-lock-fill"></i> إعدادات الحساب والأمان
                    </div>
                    <div class="row gx-3">
                        <div class="col-md-2 text-start">
                            <label class="fw-bold small"><i class="bi bi-person-lock"></i> الصلاحية</label>
                            <select name="is_admin" class="form-select bg-light border-0 py-2">
                                <option value="1" {{ $user->is_admin ? 'selected' : '' }}>مسؤول</option>
                                <option value="0" {{ !$user->is_admin ? 'selected' : '' }}>محفظ</option>
                            </select>
                        </div>
                        <div class="col-md-2 text-start">
                            <label class="fw-bold small"><i class="bi bi-tags"></i> التصنيف</label>
                            <select name="category_id" class="form-select bg-light border-0 py-2">
                                @foreach (\DB::table('categorie')->get() as $cat)
                                    <option value="{{ $cat->id }}"
                                        {{ $user->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 text-start">
                            <label class="fw-bold small"><i class="bi bi-key"></i> كلمة المرور الجديدة</label>
                            <input type="password" name="password" class="form-control bg-light border-0 py-2"
                                placeholder="اختياري">
                        </div>
                        <div class="col-md-4 text-start">
                            <label class="fw-bold small"><i class="bi bi-check2-circle"></i> تأكيد كلمة المرور</label>
                            <input type="password" name="password_confirmation"
                                class="form-control bg-light border-0 py-2" placeholder="تأكيد">
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
