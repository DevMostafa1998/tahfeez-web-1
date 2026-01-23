@extends('layouts.app')
@section('title', 'إدارة المستخدمين')

@section('content')
<div class="container-fluid p-4" dir="rtl">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-primary fw-bold mb-0">إدارة المستخدمين</h1>
        <a href="{{ route('user.create') }}" class="btn btn-primary px-4 shadow-sm fw-bold">
            <i class="bi bi-plus-lg ms-1"></i> مستخدم جديد
        </a>
    </div>

    <div class="card shadow-sm border-0" style="border-radius: 15px; overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th class="py-3" style="width: 50px;">#</th>
                        <th class="text-start ps-4">اسم المستخدم</th>
                        <th>رقم الهوية</th>
                        <th>رقم الجوال</th>
                        <th>العنوان</th>
                        <th>التصنيف</th>
                        <th>الحالة</th>
                        <th style="width: 120px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td class="text-muted fw-bold">
                            {{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}
                        </td>

                        <td class="text-start ps-4">
                            <strong>{{ $user->full_name }}</strong>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $user->id_number }}</span></td>
                        <td>{{ $user->phone_number }}</td>
                        <td class="small">{{ $user->address ?? 'غير محدد' }}</td>

                        <td>
                            @php $catName = \DB::table('categorie')->where('id', $user->category_id)->value('name'); @endphp
                            <span class="badge rounded-pill bg-dark px-3">{{ $catName }}</span>
                        </td>
                        <td>
                            <span class="badge rounded-pill {{ $user->is_admin ? 'bg-primary' : 'bg-success' }} px-3">
                                {{ $user->is_admin ? 'مسؤول' : 'محفظ' }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group gap-2 justify-content-center">
                                <button class="btn btn-sm btn-outline-warning rounded-circle action-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editUserModal"
                                        data-user="{{ json_encode($user) }}"
                                        style="width: 32px; height: 32px; padding: 0;">
                                    <i class="bi bi-pencil-square"></i>
                                </button>

                                <form action="{{ route('user.destroy', $user->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من نقل المستخدم لسلة المهملات؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle action-btn"
                                            style="width: 32px; height: 32px; padding: 0;">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

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

{{-- مودال التعديل --}}
<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-header bg-warning text-dark border-0 py-3">
                    <h5 class="modal-title fw-bold ms-auto"><i class="bi bi-person-gear me-2"></i>تعديل بيانات المستخدم</h5>
                    <button type="button" class="btn-close ms-0" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 text-end" dir="rtl">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">الاسم رباعي</label>
                            <input type="text" name="full_name" id="edit_full_name" class="form-control bg-light border-0 shadow-none" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">رقم الهوية</label>
                            <input type="text" name="id_number" id="edit_id_number" class="form-control bg-light border-0 shadow-none" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">رقم الجوال</label>
                            <input type="tel" name="phone_number" id="edit_phone_number" class="form-control bg-light border-0 shadow-none" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">العنوان</label>
                            <input type="text" name="address" id="edit_address" class="form-control bg-light border-0 shadow-none">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">الصلاحية</label>
                            <select name="is_admin" id="edit_is_admin" class="form-select bg-light border-0 shadow-none">
                                <option value="1">مسؤول</option>
                                <option value="0">محفظ</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small">نوع التصنيف</label>
                            <select name="category_id" id="edit_category_id" class="form-select bg-light border-0 shadow-none">
                                @foreach(\DB::table('categorie')->get() as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 mt-3">
                            <label class="form-label fw-bold small">تغيير كلمة المرور <span class="text-muted fw-normal">(اختياري)</span></label>
                            <input type="password" name="password" id="edit_password" class="form-control bg-light border-0 shadow-none" placeholder="اتركها فارغة لعدم التغيير">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-3 bg-light">
                    <button type="button" class="btn btn-secondary px-4 rounded-pill shadow-sm" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-warning px-5 fw-bold shadow-sm rounded-pill">حفظ التعديلات</button>
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
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    .btn-outline-warning:hover { background-color: #ffc107 !important; color: #fff !important; }
    .btn-outline-danger:hover { background-color: #dc3545 !important; color: #fff !important; }

    .pagination { margin-bottom: 0; gap: 5px; }
    .page-link {
        border: none;
        border-radius: 8px !important;
        color: #666;
        padding: 8px 16px;
    }
    .page-item.active .page-link { background-color: #0d6efd; box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3); }
</style>

<script>
    document.querySelectorAll('.edit-btn, button[data-bs-target="#editUserModal"]').forEach(button => {
        button.addEventListener('click', function() {
            const user = JSON.parse(this.dataset.user);
            const form = document.getElementById('editForm');
            form.action = `/user/${user.id}`;

            document.getElementById('edit_full_name').value = user.full_name;
            document.getElementById('edit_id_number').value = user.id_number;
            document.getElementById('edit_phone_number').value = user.phone_number;
            document.getElementById('edit_address').value = user.address;
            document.getElementById('edit_is_admin').value = user.is_admin ? "1" : "0";
            document.getElementById('edit_category_id').value = user.category_id;
        });
    });
</script>
@endpush
