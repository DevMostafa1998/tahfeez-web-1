@extends('layouts.app')
@section('title', 'إدارة المستخدمين')

@section('content')
    <div class="container-fluid p-4" dir="rtl">

        @if (session('success'))
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
                        @foreach ($users as $user)
                            <tr>
                                <td class="text-muted fw-bold">
                                    {{ ($users->currentPage() - 1) * $users->perPage() + $loop->iteration }}
                                </td>

                                <td class="text-start ps-4">
                                    <strong>{{ $user->full_name }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-3 py-2 fs-8 fw-bold"
                                        style="letter-spacing: 1px;">
                                        {{ $user->id_number }}
                                    </span>
                                </td>
                                <td>{{ $user->phone_number }}</td>
                                <td class="small">{{ $user->address ?? 'غير محدد' }}</td>

                                <td>
                                    @php $catName = \DB::table('categorie')->where('id', $user->category_id)->value('name'); @endphp
                                    <span class="badge rounded-pill bg-dark px-3">{{ $catName }}</span>
                                </td>
                                <td>
                                    <span
                                        class="badge rounded-pill {{ $user->is_admin ? 'bg-primary' : 'bg-success' }} px-3">
                                        {{ $user->is_admin ? 'مسؤول' : 'محفظ' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group gap-2 justify-content-center">
                                        <button class="btn btn-sm btn-outline-warning rounded-circle action-btn"
                                            data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}"
                                            {{-- استخدام ID المستخدم هنا --}} style="width: 32px; height: 32px; padding: 0;">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>

                                        <form action="{{ route('user.destroy', $user->id) }}" method="POST"
                                            onsubmit="return confirm('هل أنت متأكد من نقل المستخدم لسلة المهملات؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="btn btn-sm btn-outline-danger rounded-circle action-btn"
                                                style="width: 32px; height: 32px; padding: 0;">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @include('users.edit_modal', ['user' => $user])
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer bg-white border-0 py-3">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="text-muted small mb-2 mb-md-0">
                        عرض من {{ $users->firstItem() }} إلى {{ $users->lastItem() }} من إجمالي {{ $users->total() }}
                        مستخدم
                    </div>
                    <div class="pagination-container">
                        {{ $users->links('pagination::bootstrap-5') }}

                    </div>
                </div>
            </div>
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

        .btn-outline-warning:hover {
            background-color: #ffc107 !important;
            color: #fff !important;
        }

        .btn-outline-danger:hover {
            background-color: #dc3545 !important;
            color: #fff !important;
        }

        .pagination {
            margin-bottom: 0;
            gap: 5px;
        }

        .page-link {
            border: none;
            border-radius: 8px !important;
            color: #666;
            padding: 8px 16px;
        }

        .page-item.active .page-link {
            background-color: #0d6efd;
            box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3);
        }
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
        document.getElementById('editForm').addEventListener('submit', function(e) {
            const password = document.getElementById('edit_password').value;
            const confirmPassword = document.getElementById('edit_password_confirmation').value;
            const errorElement = document.getElementById('passwordMatchError');

            // إذا بدأ المستخدم في كتابة كلمة مرور جديدة
            if (password.length > 0) {
                // التحقق من التطابق أو إذا كان الحقل الثاني فارغاً
                if (password !== confirmPassword) {
                    e.preventDefault(); // منع إرسال النموذج (يبقى المودال مفتوحاً)
                    errorElement.classList.remove('d-none'); // إظهار رسالة الخطأ
                    document.getElementById('edit_password_confirmation').classList.add('is-invalid');
                } else {
                    errorElement.classList.add('d-none');
                    document.getElementById('edit_password_confirmation').classList.remove('is-invalid');
                }
            }
        });

        document.getElementById('edit_password_confirmation').addEventListener('input', function() {
            document.getElementById('passwordMatchError').classList.add('d-none');
            this.classList.remove('is-invalid');
        });
    </script>
@endpush
