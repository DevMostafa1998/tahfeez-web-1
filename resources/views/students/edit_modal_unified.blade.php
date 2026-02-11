<div class="modal fade" id="unifiedEditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;" dir="rtl">

            {{-- الهيدر --}}
            <div
                class="modal-header bg-warning py-2 border-0 d-flex flex-row-reverse justify-content-between align-items-center">
                <button type="button" class="btn-close m-0 shadow-none" data-bs-dismiss="modal" aria-label="Close"
                    style="font-size: 0.75rem;"></button>
                <h6 class="modal-title fw-bold m-0 text-dark small">
                    <i class="bi bi-pencil-square me-1"></i> تعديل ملف الطالب
                </h6>
            </div>

            <form action="" method="POST" id="editStudentForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_student_id">

                <div class="modal-body py-3 px-4" dir="rtl">

                    {{-- القسم الأول: المعلومات الأساسية --}}
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge rounded-pill bg-warning text-dark px-3 fw-bold small-xs">1. البيانات
                                الشخصية</span>
                            <hr class="flex-grow-1 me-2 my-0 opacity-10">
                        </div>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label
                                    class="label-style fw-bold mb-1 d-flex flex-row-reverse justify-content-end align-items-center">
                                    <span>الاسم رباعي</span>
                                    <i class="bi bi-person-bounding-box text-warning ms-2"></i>
                                </label>
                                <input type="text" name="full_name" id="edit_full_name"
                                    class="form-control form-control-sm border-0 bg-light rounded-2 text-start"
                                    required>
                            </div>
                            <div class="col-md-2">
                                <label
                                    class="label-style fw-bold mb-1 d-flex flex-row-reverse justify-content-end align-items-center">
                                    <span>رقم الهوية</span>
                                    <i class="bi bi-person-vcard text-warning ms-2"></i>
                                </label>
                                <input type="text" name="id_number" id="edit_id_number"
                                    class="form-control form-control-sm border-0 bg-light rounded-2 text-start"
                                    required>
                            </div>
                            <div class="col-md-2">
                                <label
                                    class="label-style fw-bold mb-1 d-flex flex-row-reverse justify-content-end align-items-center">
                                    <span>الميلاد</span>
                                    <i class="bi bi-calendar-check text-warning ms-2"></i>
                                </label>
                                <input type="date" name="date_of_birth" id="edit_date_of_birth"
                                    class="form-control form-control-sm border-0 bg-light rounded-2 text-start"
                                    required>
                            </div>
                            <div class="col-md-3">
                                <label
                                    class="label-style fw-bold mb-1 d-flex flex-row-reverse justify-content-end align-items-center">
                                    <span>مكان الميلاد</span>
                                    <i class="bi bi-geo-alt text-warning ms-2"></i>
                                </label>
                                <input type="text" name="birth_place" id="edit_birth_place"
                                    class="form-control form-control-sm border-0 bg-light rounded-2 text-start">
                            </div>
                            <div class="col-md-1">
                                <label
                                    class="label-style fw-bold mb-1 d-flex flex-row-reverse justify-content-end align-items-right">
                                    <span>الجنس</span>
                                    <i class="bi bi-gender-ambiguous text-warning ms-2"></i>
                                </label>
                                <select name="gender" id="edit_gender"
                                    class="form-select form-select-sm bg-light border-0">
                                    <option value="male">ذكر</option>
                                    <option value="female">أنثى</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- القسم الثاني: التواصل والعنوان --}}
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge rounded-pill bg-warning text-dark px-3 fw-bold small-xs">2. التواصل
                                والعنوان</span>
                            <hr class="flex-grow-1 me-2 my-0 opacity-10">
                        </div>
                        <div class="row g-2">
                            <div class="col-md-2">
                                <label
                                    class="label-style fw-bold mb-1 d-flex flex-row-reverse justify-content-end align-items-center">
                                    <span>الجوال</span>
                                    <i class="bi bi-phone-vibrate text-warning ms-2"></i>
                                </label>
                                <input type="text" name="phone_number" id="edit_phone_number"
                                    class="form-control form-control-sm border-0 bg-light rounded-2 text-start"
                                    required>
                            </div>
                            <div class="col-md-2">
                                <label
                                    class="label-style fw-bold mb-1 d-flex flex-row-reverse justify-content-end align-items-center">
                                    <span>واتساب</span>
                                    <i class="bi bi-whatsapp text-success ms-2"></i>
                                </label>
                                <input type="text" name="whatsapp_number" id="edit_whatsapp_number"
                                    class="form-control form-control-sm border-0 bg-light rounded-2 text-start">
                            </div>
                            <div class="col-md-5">
                                <label
                                    class="label-style fw-bold mb-1 d-flex flex-row-reverse justify-content-end align-items-center">
                                    <span>عنوان السكن</span>
                                    <i class="bi bi-map text-warning ms-2"></i>
                                </label>
                                <input type="text" name="address" id="edit_address"
                                    class="form-control form-control-sm border-0 bg-light rounded-2 text-start"
                                    required>
                            </div>
                            <div class="col-md-3">
                                <label
                                    class="label-style fw-bold mb-1 d-flex flex-row-reverse justify-content-end align-items-right">
                                    <span>حالة السكن</span>
                                    <i class="bi bi-house-check text-warning ms-2"></i>
                                </label>
                                <select name="is_displaced" id="edit_is_displaced"
                                    class="form-select form-select-sm bg-light border-0">
                                    <option value="0">مقيم</option>
                                    <option value="1">نازح</option>
                                </select>

                            </div>
                        </div>
                    </div>

                    {{-- القسم الثالث: التفاصيل الإدارية --}}
                    <div>
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge rounded-pill bg-warning text-dark px-3 fw-bold small-xs">3. التفاصيل
                                الإدارية</span>
                            <hr class="flex-grow-1 me-2 my-0 opacity-10">
                        </div>
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label
                                    class="label-style fw-bold mb-1 d-flex flex-row-reverse justify-content-end align-items-center">
                                    <span>اسم المركز</span>
                                    <i class="bi bi-building-gear text-warning ms-2"></i>
                                </label>
                                <input type="text" name="center_name" id="edit_center_name"
                                    class="form-control form-control-sm border-0 bg-light rounded-2 text-start">
                            </div>
                            <div class="col-md-3">
                                <label
                                    class="label-style fw-bold mb-1 d-flex flex-row-reverse justify-content-end align-items-center">
                                    <span>اسم المسجد</span>
                                    <i class="bi bi-moon-stars-fill text-warning ms-2"></i>
                                </label>
                                <input type="text" name="mosque_name" id="edit_mosque_name"
                                    class="form-control form-control-sm border-0 bg-light rounded-2 text-start">
                            </div>
                            <div class="col-md-6">
                                <label
                                    class="label-style fw-bold mb-1 d-flex flex-row-reverse justify-content-end align-items-center">
                                    <span>عنوان المسجد التفصيلي</span>
                                    <i class="bi bi-pin-map-fill text-warning ms-2"></i>
                                </label>
                                <input type="text" name="mosque_address" id="edit_mosque_address"
                                    class="form-control form-control-sm border-0 bg-light rounded-2 text-start">
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer border-0 py-2 bg-light d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-warning btn-sm px-5 fw-bold shadow-sm rounded-pill">
                        تحديث البيانات
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3 rounded-pill"
                        data-bs-dismiss="modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>
