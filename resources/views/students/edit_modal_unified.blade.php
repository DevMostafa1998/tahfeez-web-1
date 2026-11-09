<div class="modal fade" id="unifiedEditModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;" dir="rtl">

            {{-- الهيدر --}}
            <div class="modal-header bg-warning py-2 border-0 d-flex flex-row-reverse justify-content-between align-items-center">
                <button type="button" class="btn-close m-0 shadow-none" data-bs-dismiss="modal" aria-label="Close" style="font-size: 0.75rem;"></button>
                <h6 class="modal-title fw-bold m-0 text-dark small">
                    <i class="bi bi-pencil-square me-1"></i> تعديل ملف الطالب
                </h6>
            </div>

            <form action="" method="POST" id="editStudentForm">
                @csrf
                @method('PUT')
                <input type="hidden" id="edit_student_id" name="id">

                <div class="modal-body py-3 px-4" dir="rtl">

                    {{-- القسم الأول: البيانات الشخصية --}}
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge rounded-pill bg-warning text-dark px-3 fw-bold small-xs">1. البيانات الشخصية</span>
                            <hr class="flex-grow-1 me-2 my-0 opacity-10">
                        </div>
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="label-style fw-bold mb-1 d-flex flex-row-reverse justify-content-end align-items-center">
                                    <span>الاسم رباعي</span>
                                    <i class="bi bi-person-bounding-box text-warning ms-2"></i>
                                </label>
                                <input type="text" name="full_name" id="edit_full_name" class="form-control form-control-sm border-0 bg-light rounded-2 text-start" required>
                            </div>

                            <div class="col-md-2">
                                <label class="label-style fw-bold mb-1 d-flex flex-row-reverse justify-content-end align-items-center">
                                    <span>هوية الطالب</span>
                                    <i class="bi bi-person-vcard text-warning ms-2"></i>
                                </label>
                                <input type="text" name="id_number" id="edit_id_number" class="form-control form-control-sm border-0 bg-light rounded-2 text-start" maxlength="9" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="9 أرقام" required>
                            </div>

                            <div class="col-md-2">
                                <label class="label-style fw-bold mb-1 d-flex flex-row-reverse justify-content-end align-items-center">
                                    <span>هوية الأب</span>
                                    <i class="bi bi-person-vcard-fill text-warning ms-2"></i>
                                </label>
                                <input type="text" name="father_id" id="edit_father_id" class="form-control form-control-sm border-0 bg-light rounded-2 text-start" maxlength="9" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="9 أرقام">
                            </div>

                            <div class="col-md-2">
                                <label class="label-style fw-bold mb-1 d-flex flex-row-reverse justify-content-end align-items-center">
                                    <span>الميلاد</span>
                                    <i class="bi bi-calendar-check text-warning ms-2"></i>
                                </label>
                                <input type="date" name="date_of_birth" id="edit_date_of_birth" class="form-control form-control-sm border-0 bg-light rounded-2 text-start" required>
                            </div>

                            <div class="col-md-2">
                                <label class="label-style fw-bold mb-1 d-flex flex-row-reverse justify-content-end align-items-center">
                                    <span>مكان الميلاد</span>
                                    <i class="bi bi-geo-alt text-warning ms-2"></i>
                                </label>
                                <input type="text" name="birth_place" id="edit_birth_place" class="form-control form-control-sm border-0 bg-light rounded-2 text-start">
                            </div>

                            <div class="col-md-1">
                                <label class="label-style fw-bold mb-1 d-flex flex-row-reverse justify-content-end align-items-center">
                                    <span>الجنس</span>
                                    <i class="bi bi-gender-ambiguous text-warning ms-2"></i>
                                </label>
                                <select name="gender" id="edit_gender" class="form-select form-select-sm bg-light border-0">
                                    <option value="male">ذكر</option>
                                    <option value="female">أنثى</option>
                                </select>
                            </div>

                            <div class="col-md-3">
                                <label class="label-style fw-bold mb-1 d-flex flex-row-reverse justify-content-end align-items-center">
                                    <span>نوع التصنيف</span>
                                    <i class="bi bi-tags-fill text-warning ms-2"></i>
                                </label>
                               <select name="categorie_id" id="edit_categorie_id" class="form-select form-select-sm border-0 bg-light rounded-2 text-start fw-bold" required>
                                <option value="" selected disabled>اختر التصنيف</option>
                                @php
                                    $categoryList = $categories ?? \Illuminate\Support\Facades\DB::table('categorie')->get();
                                @endphp
                                @foreach ($categoryList as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            </div>
                        </div>
                    </div>

                    {{-- القسم الثاني: التواصل والعنوان --}}
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge rounded-pill bg-warning text-dark px-3 fw-bold small-xs">2. التواصل والعنوان</span>
                            <hr class="flex-grow-1 me-2 my-0 opacity-10">
                        </div>
                        <div class="row g-2">
                            <div class="col-md-2">
                                <label class="label-style fw-bold mb-1 d-flex flex-row-reverse justify-content-end align-items-center">
                                    <span>الجوال</span>
                                    <i class="bi bi-phone-vibrate text-warning ms-2"></i>
                                </label>
                                <input type="text" name="phone_number" id="edit_phone_number" class="form-control form-control-sm border-0 bg-light rounded-2 text-start" required>
                            </div>
                            <div class="col-md-2">
                                <label class="label-style fw-bold mb-1 d-flex flex-row-reverse justify-content-end align-items-center">
                                    <span>واتساب</span>
                                    <i class="bi bi-whatsapp text-success ms-2"></i>
                                </label>
                                <input type="text" name="whatsapp_number" id="edit_whatsapp_number" class="form-control form-control-sm border-0 bg-light rounded-2 text-start">
                            </div>
                            <div class="col-md-5">
                                <label class="label-style fw-bold mb-1 d-flex flex-row-reverse justify-content-end align-items-center">
                                    <span>عنوان السكن</span>
                                    <i class="bi bi-map text-warning ms-2"></i>
                                </label>
                                <input type="text" name="address" id="edit_address" class="form-control form-control-sm border-0 bg-light rounded-2 text-start" required>
                            </div>
                            <div class="col-md-3">
                                <label class="label-style fw-bold mb-1 d-flex flex-row-reverse justify-content-end align-items-center">
                                    <span>حالة السكن</span>
                                    <i class="bi bi-house-check text-warning ms-2"></i>
                                </label>
                                <select name="is_displaced" id="edit_is_displaced" class="form-select form-select-sm bg-light border-0">
                                    <option value="0">مقيم</option>
                                    <option value="1">نازح</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- القسم الثالث: التفاصيل الإدارية --}}
                    <div>
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge rounded-pill bg-warning text-dark px-3 fw-bold small-xs">3. التفاصيل الإدارية</span>
                            <hr class="flex-grow-1 me-2 my-0 opacity-10">
                        </div>
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="label-style fw-bold mb-1 d-flex flex-row-reverse justify-content-end align-items-center">
                                    <span>اسم المركز</span>
                                    <i class="bi bi-building-gear text-warning ms-2"></i>
                                </label>
                             <select name="center_id" id="edit_center_id" class="form-select form-select-sm bg-light border-0">
                                <option value="">غير محدد</option>
                                @php
                                    $centerList = $centers ?? \Illuminate\Support\Facades\DB::table('centers')->get();
                                @endphp
                                @foreach ($centerList as $center)
                                    <option value="{{ $center->id }}">{{ $center->name }}</option>
                                @endforeach
                            </select>
                            </div>
                            <div class="col-md-3">
                                <label class="label-style fw-bold mb-1 d-flex flex-row-reverse justify-content-end align-items-center">
                                    <span>اسم المسجد</span>
                                    <i class="bi bi-moon-stars-fill text-warning ms-2"></i>
                                </label>
                               <select name="mosque_name" id="edit_mosque_name" class="form-select form-select-sm border-0 bg-light rounded-2 text-start">
                                    <option value="">غير محدد</option>
                                    @php
                                        $mosqueList = $mosques ?? \App\Models\Lov::getOptions('mosque_list');
                                    @endphp
                                    @foreach ($mosqueList as $mosque)
                                        <option value="{{ $mosque->description }}">{{ $mosque->description }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="label-style fw-bold mb-1 d-flex flex-row-reverse justify-content-end align-items-center">
                                    <span>عنوان المسجد التفصيلي</span>
                                    <i class="bi bi-pin-map-fill text-warning ms-2"></i>
                                </label>
                                <input type="text" name="mosque_address" id="edit_mosque_address"
                                    class="form-control form-control-sm border-0 bg-light rounded-2 text-start"
                                    placeholder="أدخل عنوان المسجد بالتفصيل">
                            </div>
                        </div>
                    </div>

                </div>

                <div class="modal-footer border-0 py-2 bg-light d-flex justify-content-end gap-2">
                    <button type="submit" id="btnSubmitUnifiedForm" class="btn btn-warning btn-sm px-5 fw-bold shadow-sm rounded-pill">
                        تحديث البيانات
                    </button>
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3 rounded-pill" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editForm = document.getElementById('editStudentForm');

    if (editForm) {
        editForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const btnSubmit = document.getElementById('btnSubmitUnifiedForm');
            if (btnSubmit) btnSubmit.disabled = true;

            // 1. جلب معرف الطالب (ID) بطريقة آمنة
            let studentId = document.getElementById('edit_student_id') ? document.getElementById('edit_student_id').value.trim() : '';

            if (!studentId) {
                const urlSegments = window.location.pathname.split('/');
                const lastSegment = urlSegments[urlSegments.length - 1];
                if (!isNaN(lastSegment) && lastSegment !== '') {
                    studentId = lastSegment;
                }
            }

            let actionUrl = this.getAttribute('action');

            if (!actionUrl || actionUrl.trim() === '' || actionUrl === '#') {
                if (studentId) {
                    actionUrl = `/student_public/${studentId}`;
                } else {
                    if (btnSubmit) btnSubmit.disabled = false;
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'لم يتم العثور على معرف الطالب، يرجى إعادة فتح نافذة التعديل.',
                        confirmButtonText: 'موافق'
                    });
                    return;
                }
            }

            // 2. تجهيز البيانات وإضافة طريقة الـ PUT صراحة
            const formData = new FormData(this);
            if (!formData.has('_method')) {
                formData.append('_method', 'PUT');
            }

            // 3. إرسال الطلب عبر Fetch
            fetch(actionUrl, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(async response => {
                let resData = null;
                try {
                    resData = await response.json();
                } catch (err) {
                    resData = null;
                }

                if (btnSubmit) btnSubmit.disabled = false;

                if (response.ok || (resData && resData.success === true)) {

                    try {
                        const unifiedModalEl = document.getElementById('unifiedEditModal');
                        if (unifiedModalEl) {
                            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                                const modalInstance = bootstrap.Modal.getInstance(unifiedModalEl) || new bootstrap.Modal(unifiedModalEl);
                                modalInstance.hide();
                            } else if (typeof window.jQuery !== 'undefined') {
                                window.jQuery(unifiedModalEl).modal('hide');
                            }
                        }
                    } catch (modalErr) {
                        console.warn('Modal close warning:', modalErr);
                    }

                    // إظهار رسالة النجاح الخضراء
                    Swal.fire({
                        icon: 'success',
                        title: 'تم التحديث بنجاح!',
                        text: (resData && resData.message) ? resData.message : 'تم تحديث بيانات الطالب بنجاح.',
                        confirmButtonText: 'حسناً',
                        customClass: { confirmButton: 'btn btn-success px-4 rounded-pill' }
                    }).then(() => {
                        window.location.reload();
                    });

                } else {
                    // معالجة أخطاء التحقق (Validation Errors)
                    let errorMessage = 'حدث خطأ أثناء حفظ البيانات، يرجى مراجعة المدخلات.';

                    if (resData && resData.errors) {
                        errorMessage = Object.values(resData.errors).flat().join('<br>');
                    } else if (resData && resData.message) {
                        errorMessage = resData.message;
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'تعذر حفظ البيانات',
                        html: `<div class="text-center text-danger" style="font-size:0.9rem; line-height:1.6;">${errorMessage}</div>`,
                        confirmButtonText: 'موافق',
                        confirmButtonColor: '#dc3545'
                    });
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                if (btnSubmit) btnSubmit.disabled = false;

                Swal.fire({
                    icon: 'error',
                    title: 'خطأ غير متوقع',
                    text: 'حدث خطأ بالخادم أثناء معالجة طلبكم، يرجى المحاولة لاحقاً.',
                    confirmButtonText: 'موافق'
                });
            });
        });
    }
});
</script>
