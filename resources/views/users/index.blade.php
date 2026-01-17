@extends('layouts.app')
@section('title', 'إدارة المستخدمين')

@section('content')
<div class="container-fluid p-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 text-primary fw-bold">إدارة المستخدمين</h1>
        <a href="{{ route('user.create') }}" class="btn btn-primary px-4 shadow-sm">
            <i class="bi bi-plus-lg"></i> مستخدم جديد
        </a>
    </div>

    <div class="card shadow-sm border-0" style="border-radius: 15px;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center">
                <thead class="bg-light text-secondary">
                    <tr>
                        <th>اسم المستخدم</th>
                        <th>رقم الهوية </th>
                        <th>رقم الجوال</th>
                        <th>التصنيف</th>
                        <th>الحالة</th>
                        <th>الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td class="text-start ps-4">
                            <strong>{{ $user->full_name }}</strong>
                        </td>
                        <td>{{ $user->id_number }}</td>
                        <td>{{ $user->phone_number }}</td>
                        <td>
                            @php $catName = \DB::table('categorie')->where('id', $user->category_id)->value('name'); @endphp
                            <span class="badge bg-dark">{{ $catName }}</span>
                        </td>
                        <td>
                            <span class="badge {{ $user->is_admin ? 'bg-primary' : 'bg-success' }}">
                                {{ $user->is_admin ? 'مسؤول' : 'محفظ' }}
                            </span>
                        </td>
                        <td>
                            <div class="btn-group gap-2">
                                <button class="btn btn-sm btn-outline-warning rounded-circle edit-btn"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editUserModal"
                                        data-user="{{ json_encode($user) }}">
                                    <i class="bi bi-pencil-square"></i>
                                </button>


                                <form action="{{ route('user.destroy', $user->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من نقل المستخدم لسلة المهملات؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle">
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
    </div>
</div>

<div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form id="editForm" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-content" style="border-radius: 15px;">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title fw-bold">تعديل بيانات المستخدم</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
               <div class="modal-body p-4" dir="rtl">
                <div class="row g-4">

                    <div class="col-md-4 text-end">
                        <label class="form-label fw-bold small text-muted d-block" style="text-align: right;">الاسم رباعي</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-person text-primary"></i></span>
                            <input type="text" name="full_name" id="edit_full_name" class="form-control"
                                style="direction: rtl !important; text-align: right !important;"
                                placeholder="أدخل الاسم رباعي" required>
                        </div>
                    </div>

                    <div class="col-md-4 text-end">
                        <label class="form-label fw-bold small text-muted d-block" style="text-align: right;">رقم الهوية</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-card-heading text-primary"></i></span>
                            <input type="text" name="id_number" id="edit_id_number" class="form-control"
                                style="direction: rtl !important; text-align: right !important;"
                                placeholder="أدخل رقم الهوية" required>
                        </div>
                    </div>

                    <div class="col-md-4 text-end">
                        <label class="form-label fw-bold small text-muted d-block" style="text-align: right;">رقم الجوال</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-telephone text-primary"></i></span>
                            <input type="tel" name="phone_number" id="edit_phone_number" class="form-control"
                                style="direction: rtl !important; text-align: right !important;"
                                placeholder="05XXXXXXXX" required>
                        </div>
                    </div>

                    <div class="col-md-4 text-end">
                        <label class="form-label fw-bold small text-muted d-block" style="text-align: right;">العنوان</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-geo-alt text-primary"></i></span>
                            <input type="text" name="address" id="edit_address" class="form-control"
                                style="direction: rtl !important; text-align: right !important;"
                                placeholder="المدينة، الحي" required>
                        </div>
                    </div>

                    <div class="col-md-4 text-end">
                        <label class="form-label fw-bold small text-muted d-block" style="text-align: right;">الصلاحية</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-layers text-primary"></i></span>
                            <select name="is_admin" id="edit_is_admin" class="form-select" style="direction: rtl !important; text-align: right !important;">
                                <option value="1">مسؤول</option>
                                <option value="0">محفظ</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4 text-end">
                        <label class="form-label fw-bold small text-muted d-block" style="text-align: right;">نوع التصنيف</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-tags text-primary"></i></span>
                            <select name="category_id" id="edit_category_id" class="form-select" style="direction: rtl !important; text-align: right !important;">
                                @foreach(\DB::table('categorie')->get() as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-4 text-end">
                        <label class="form-label fw-bold small text-muted d-block" style="text-align: right;">كلمة المرور الجديدة</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="bi bi-lock text-primary"></i></span>
                            <input type="password" name="password" id="edit_password" class="form-control"
                                style="direction: rtl !important; text-align: right !important;"
                                placeholder="اتركها فارغة لعدم التغيير">
                        </div>
                    </div>


                </div>
            </div>
                <div class="modal-footer border-0 p-3">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-warning px-5 fw-bold shadow-sm">حفظ التغييرات</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.querySelectorAll('.edit-btn').forEach(button => {
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
