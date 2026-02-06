<div class="modal fade" id="editStudentModal{{ $student->id }}" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 15px;" dir="rtl">

            {{-- هيدر نحيف جداً --}}
            <div
                class="modal-header bg-warning py-2 border-0 d-flex flex-row-reverse justify-content-between align-items-center">
                <button type="button" class="btn-close m-0 shadow-none" data-bs-dismiss="modal" aria-label="Close"
                    style="font-size: 0.75rem;"></button>
                <h6 class="modal-title fw-bold m-0 text-dark small">
                    <i class="bi bi-pencil-square me-1"></i> تعديل ملف الطالب
                </h6>
            </div>

            <form action="{{ route('student.update', $student->id) }}" method="POST"
                id="editStudentForm{{ $student->id }}">
                @csrf
                @method('PUT')

                <div class="modal-body py-3 px-4">

                    {{-- القسم الأول: المعلومات الأساسية --}}
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge rounded-pill bg-warning text-dark px-3 fw-bold small-xs">1. البيانات
                                الشخصية</span>
                            <hr class="flex-grow-1 ms-2 my-0 opacity-10">
                        </div>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="label-style fw-bold mb-1">
                                    <i class="bi bi-person-bounding-box text-warning me-1"></i>الاسم رباعي
                                </label>
                                <input type="text" name="full_name"
                                    class="form-control form-control-sm border-0 bg-light rounded-2"
                                    value="{{ $student->full_name }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="label-style fw-bold mb-1">
                                    <i class="bi bi-person-vcard text-warning me-1"></i>رقم الهوية
                                </label>
                                <input type="text" name="id_number"
                                    class="form-control form-control-sm border-0 bg-light rounded-2"
                                    value="{{ $student->id_number }}" required>
                            </div>
                            <div class="col-md-2">
                                <label class="label-style fw-bold mb-1">
                                    <i class="bi bi-calendar-check text-warning me-1"></i>الميلاد
                                </label>
                                <input type="date" name="date_of_birth"
                                    class="form-control form-control-sm border-0 bg-light rounded-2"
                                    value="{{ \Carbon\Carbon::parse($student->date_of_birth)->format('Y-m-d') }}"
                                    required>
                            </div>
                            <div class="col-md-3">
                                <label class="label-style fw-bold mb-1">
                                    <i class="bi bi-geo-alt text-warning me-1"></i>مكان الميلاد
                                </label>
                                <input type="text" name="birth_place"
                                    class="form-control form-control-sm border-0 bg-light rounded-2"
                                    value="{{ $student->birth_place }}">
                            </div>
                        </div>
                    </div>

                    {{-- القسم الثاني: التواصل والسكن --}}
                    <div class="mb-3">
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge rounded-pill bg-warning text-dark px-3 fw-bold small-xs">2. التواصل
                                والعنوان</span>
                            <hr class="flex-grow-1 ms-2 my-0 opacity-10">
                        </div>
                        <div class="row g-2">
                            <div class="col-md-2">
                                <label class="label-style fw-bold mb-1">
                                    <i class="bi bi-phone-vibrate text-warning me-1"></i>الجوال
                                </label>
                                <input type="text" name="phone_number"
                                    class="form-control form-control-sm border-0 bg-light rounded-2"
                                    value="{{ $student->phone_number }}" required>
                            </div>
                            <div class="col-md-2">
                                <label class="label-style fw-bold mb-1">
                                    <i class="bi bi-whatsapp text-success me-1"></i>واتساب
                                </label>
                                <input type="text" name="whatsapp_number"
                                    class="form-control form-control-sm border-0 bg-light rounded-2"
                                    value="{{ $student->whatsapp_number }}">
                            </div>
                            <div class="col-md-5">
                                <label class="label-style fw-bold mb-1">
                                    <i class="bi bi-map text-warning me-1"></i>عنوان السكن
                                </label>
                                <input type="text" name="address"
                                    class="form-control form-control-sm border-0 bg-light rounded-2"
                                    value="{{ $student->address }}" required>
                            </div>
                            <div class="col-md-3">
                                <label class="label-style fw-bold mb-1">
                                    <i class="bi bi-house-check text-warning me-1"></i>حالة السكن
                                </label>
                                <select name="is_displaced"
                                    class="form-select form-select-sm border-0 bg-light rounded-2 fw-bold">
                                    <option value="0" {{ !$student->is_displaced ? 'selected' : '' }}>مقيم
                                    </option>
                                    <option value="1" {{ $student->is_displaced ? 'selected' : '' }}>نازح</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- القسم الثالث: المسجد والمركز --}}
                    <div>
                        <div class="d-flex align-items-center mb-2">
                            <span class="badge rounded-pill bg-warning text-dark px-3 fw-bold small-xs">3. التفاصيل
                                الإدارية</span>
                            <hr class="flex-grow-1 ms-2 my-0 opacity-10">
                        </div>
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="label-style fw-bold mb-1">
                                    <i class="bi bi-building-gear text-warning me-1"></i>اسم المركز
                                </label>
                                <input type="text" name="center_name"
                                    class="form-control form-control-sm border-0 bg-light rounded-2"
                                    value="{{ $student->center_name }}">
                            </div>
                            <div class="col-md-3">
                                <label class="label-style fw-bold mb-1">
                                    <i class="bi bi-moon-stars-fill text-warning me-1"></i>اسم المسجد
                                </label>
                                <input type="text" name="mosque_name"
                                    class="form-control form-control-sm border-0 bg-light rounded-2"
                                    value="{{ $student->mosque_name }}">
                            </div>
                            <div class="col-md-6">
                                <label class="label-style fw-bold mb-1">
                                    <i class="bi bi-pin-map-fill text-warning me-1"></i>عنوان المسجد التفصيلي
                                </label>
                                <input type="text" name="mosque_address"
                                    class="form-control form-control-sm border-0 bg-light rounded-2"
                                    value="{{ $student->mosque_address }}">
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

{{-- <style>
    .label-style {
        font-size: 0.78rem;
        color: #4a4a4a;
        display: flex;
        align-items: center;
    }

    .small-xs {
        font-size: 0.7rem;
    }

    .form-control-sm,
    .form-select-sm {
        padding: 0.4rem 0.75rem;
        transition: all 0.2s ease-in-out;
    }

    .form-control-sm:focus,
    .form-select-sm:focus {
        background-color: #fff !important;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        border: 1px solid #ffc107 !important;
    }

    .modal-body hr {
        opacity: 0.1;
    }
</style> --}}
