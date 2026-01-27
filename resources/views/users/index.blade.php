@extends('layouts.app')
@section('title', 'إدارة المستخدمين')

@section('content')
<div class="container-fluid p-4" dir="rtl">

    {{-- رسائل التنبيه والنجاح --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- رأس الصفحة --}}
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-primary fw-bold mb-0">
            <i class="bi bi-people-fill me-2"></i>إدارة المستخدمين
        </h1>
     <a href="{{ route('user.create') }}" class="btn btn-primary px-4 shadow-sm fw-bold rounded-pill">
    <i class="bi bi-plus-lg ms-1"></i> مستخدم جديد
</a>
    </div>

    {{-- جدول عرض البيانات --}}
    <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="py-3">#</th>
                        <th class="text-start ps-4">اسم المستخدم</th>
                        <th>رقم الهوية</th>
                        <th>رقم الجوال</th>
                        <th>العنوان</th>
                        <th>التصنيف</th>
                        <th>الصلاحية</th>
                        <th>عدد الدورات</th>
                        <th style="width: 120px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="text-muted fw-bold">
                            {{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}
                        </td>

                        <td class="text-start ps-4">
                            <div class="d-flex align-items-center">

                                <strong class="text-truncate" style="max-width: 150px;">{{ $user->full_name }}</strong>
                            </div>
                        </td>

                        <td>
                            <span class="badge bg-light text-dark border px-3 py-2 fs-8 fw-bold" style="letter-spacing: 1px;">
                                {{ $user->id_number }}
                            </span>
                        </td>

                        <td dir="ltr" class="text-end text-center">{{ $user->phone_number }}</td>

                        <td class="small text-muted">{{ Str::limit($user->address, 15) ?? 'غير محدد' }}</td>

                        <td>
                            <span class="badge rounded-pill bg-info bg-opacity-10 text-info px-3 border border-info">
                                {{ $user->category->name ?? '---' }}
                            </span>
                        </td>

                        <td>
                            <span class="badge rounded-pill {{ $user->is_admin ? 'bg-primary' : 'bg-success' }} px-3">
                                {{ $user->is_admin ? 'مسؤول' : 'محفظ' }}
                            </span>
                        </td>

                        {{-- عمود الدورات: يظهر فقط للمحفظين --}}
                        <td>
                            @if($user->is_admin)
                                <span class="text-muted fs-4" title="لا ينطبق على المسؤولين">--</span>
                            @else
                                <div class="d-flex justify-content-center align-items-center">
                                    <span class="badge bg-warning text-dark rounded-pill border border-warning shadow-sm px-3"
                                          title="عدد الدورات المسندة">
                                        {{ $user->courses->count() }} دورات
                                        <i class="bi bi-book ms-1"></i>
                                    </span>
                                </div>
                            @endif
                        </td>

                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                {{-- زر التعديل --}}
                                <button class="btn btn-sm btn-outline-warning rounded-circle action-btn edit-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editUserModal"
                                        data-user="{{ json_encode($user) }}"
                                        data-user-courses="{{ json_encode($user->courses->pluck('id')) }}"
                                        title="تعديل">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                {{-- زر الحذف --}}
                                <form action="{{ route('user.destroy', $user->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من نقل المستخدم لسلة المهملات؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle action-btn" title="حذف">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-muted py-5 fs-5">لا يوجد مستخدمين مضافين حالياً</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- الترقيم --}}
        <div class="card-footer bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div class="text-muted small mb-2 mb-md-0">
                    عرض من {{ $users->firstItem() }} إلى {{ $users->lastItem() }} من إجمالي {{ $users->total() }} مستخدم
                </div>
                <div class="pagination-container">
                    {{ $users->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ================= مودال التعديل ================= --}}
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; text-align: right;" dir="rtl">

                <div class="modal-header bg-warning text-dark border-0 py-3 d-flex flex-row-reverse justify-content-between align-items-center">
                    <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
                    <h5 class="modal-title fw-bold m-0">
                        <i class="bi bi-person-gear ms-2"></i>تعديل بيانات المستخدم
                    </h5>
                </div>

                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6 text-end">
                            <label class="form-label fw-bold small">الاسم رباعي <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" id="edit_full_name" class="form-control bg-light border-0" required>
                        </div>

                        <div class="col-md-6 text-end">
                            <label class="form-label fw-bold small">رقم الهوية <span class="text-danger">*</span></label>
                            <input type="text" name="id_number" id="edit_id_number" class="form-control bg-light border-0" required>
                        </div>

                        <div class="col-md-6 text-end">
                            <label class="form-label fw-bold small">رقم الجوال <span class="text-danger">*</span></label>
                            <input type="tel" name="phone_number" id="edit_phone_number" class="form-control bg-light border-0" required>
                        </div>

                        <div class="col-md-6 text-end">
                            <label class="form-label fw-bold small">العنوان</label>
                            <input type="text" name="address" id="edit_address" class="form-control bg-light border-0">
                        </div>

                        <div class="col-md-6 text-end">
                            <label class="form-label fw-bold small">الصلاحية</label>
                            <select name="is_admin" id="edit_is_admin" class="form-select bg-light border-0">
                                <option value="1">مسؤول</option>
                                <option value="0">محفظ</option>
                            </select>
                        </div>

                        <div class="col-md-6 text-end">
                            <label class="form-label fw-bold small">نوع التصنيف</label>
                            <select name="category_id" id="edit_category_id" class="form-select bg-light border-0">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- قسم الدورات:  --}}
                        <div class="col-12 mt-4" id="courses_section_container">
                            <div class="card border-warning mb-3">
                                <div class="card-header bg-warning bg-opacity-25 border-warning fw-bold text-dark">
                                    <i class="bi bi-book-half me-1"></i> الدورات المسندة للمحفظ
                                </div>
                                <div class="card-body bg-light">
                                    <p class="text-muted small mb-3">يرجى اختيار الدورات التي يقوم هذا المحفظ بتدريسها:</p>
                                    <div class="row g-2">
                                        @forelse($all_courses as $course)
                                            <div class="col-md-4 col-sm-6">
                                                <div class="form-check p-2 bg-white rounded border d-flex align-items-center">
                                                    <input class="form-check-input course-checkbox ms-2" style="float: none;" type="checkbox" name="courses[]" value="{{ $course->id }}" id="course_{{ $course->id }}">
                                                    <label class="form-check-label small fw-bold w-100 cursor-pointer" for="course_{{ $course->id }}">
                                                        {{ $course->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="col-12 text-center text-muted">لا توجد دورات مضافة للنظام</div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="text-muted my-3">

                        <div class="col-md-6 mt-2 text-end">
                            <label class="form-label fw-bold small">كلمة المرور الجديدة</label>
                            <input type="password" name="password" id="edit_password" class="form-control bg-light border-0" placeholder="اتركها فارغة لعدم التغيير">
                        </div>

                        <div class="col-md-6 mt-2 text-end">
                            <label class="form-label fw-bold small">تأكيد كلمة المرور</label>
                            <input type="password" name="password_confirmation" id="edit_password_confirmation" class="form-control bg-light border-0" placeholder="أعد كتابة كلمة المرور">
                            <div id="passwordMatchError" class="text-danger small mt-1 d-none">كلمات المرور غير متطابقة!</div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-0 p-3 bg-light d-flex justify-content-end">
                    <button type="submit" class="btn btn-warning px-5 fw-bold shadow-sm rounded-pill">حفظ التعديلات</button>
                    <button type="button" class="btn btn-secondary px-4 rounded-pill shadow-sm" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<style>
    .action-btn {
        display: inline-flex;
        justify-content: center;
        align-items: center;
        width: 32px;
        height: 32px;
        padding: 0;
        transition: all 0.2s ease-in-out;
    }
    .action-btn i {
        font-size: 1rem;
        line-height: 1;
    }
    .btn-outline-warning:hover { background-color: #ffc107 !important; color: #fff !important; }
    .btn-outline-danger:hover { background-color: #dc3545 !important; color: #fff !important; }

    .cursor-pointer { cursor: pointer; }
    .form-check-input:checked {
        background-color: #ffc107;
        border-color: #ffc107;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {

        // التعامل مع زر التعديل
        document.querySelectorAll('.edit-btn').forEach(button => {
            button.addEventListener('click', function() {
                const user = JSON.parse(this.dataset.user);
                const userCourses = JSON.parse(this.dataset.userCourses);

                const form = document.getElementById('editForm');
                form.action = `/user/${user.id}`;

                // تعبئة البيانات
                document.getElementById('edit_full_name').value = user.full_name;
                document.getElementById('edit_id_number').value = user.id_number;
                document.getElementById('edit_phone_number').value = user.phone_number;
                document.getElementById('edit_address').value = user.address;

                // تعيين الصلاحية
                const isAdminSelect = document.getElementById('edit_is_admin');
                isAdminSelect.value = user.is_admin ? "1" : "0";

                document.getElementById('edit_category_id').value = user.category_id;

                // التعامل مع الـ Checkboxes
                document.querySelectorAll('.course-checkbox').forEach(checkbox => {
                    checkbox.checked = userCourses.includes(parseInt(checkbox.value));
                });

                // استدعاء دالة إظهار/إخفاء الدورات بناءً على الصلاحية الحالية
                toggleCoursesSection(isAdminSelect.value);
            });
        });

        // مراقبة تغيير قائمة الصلاحية (مسؤول/محفظ) لإخفاء أو إظهار الدورات
        const isAdminSelect = document.getElementById('edit_is_admin');
        const coursesSection = document.getElementById('courses_section_container');

        isAdminSelect.addEventListener('change', function() {
            toggleCoursesSection(this.value);
        });

        function toggleCoursesSection(value) {
            // القيمة "1" تعني مسؤول، "0" تعني محفظ
            if (value === "1") {
                coursesSection.style.display = 'none'; // إخفاء للمسؤول
            } else {
                coursesSection.style.display = 'block'; // إظهار للمحفظ
            }
        }

        // التحقق من كلمة المرور
        document.getElementById('editForm').addEventListener('submit', function(e) {
            const password = document.getElementById('edit_password').value;
            const confirmPassword = document.getElementById('edit_password_confirmation').value;
            const errorElement = document.getElementById('passwordMatchError');

            if (password.length > 0 && password !== confirmPassword) {
                e.preventDefault();
                errorElement.classList.remove('d-none');
                document.getElementById('edit_password_confirmation').classList.add('is-invalid');
            } else {
                errorElement.classList.add('d-none');
                document.getElementById('edit_password_confirmation').classList.remove('is-invalid');
            }
        });

        document.getElementById('edit_password_confirmation').addEventListener('input', function() {
            document.getElementById('passwordMatchError').classList.add('d-none');
            this.classList.remove('is-invalid');
        });
    });
</script>
@endpush
