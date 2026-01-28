@extends('layouts.app')
@section('title', 'إدارة المستخدمين')

@section('content')
    <div class="container-fluid p-4" dir="rtl">

        @if (session('success'))
            <script>
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                Toast.fire({
                    icon: 'success',
                    title: "{{ session('success') }}"
                });
            </script>
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
                                            class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button"
                                                class="btn btn-sm btn-outline-danger rounded-circle action-btn delete-btn"
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
        document.addEventListener('DOMContentLoaded', function() {

            // 1. معالجة التأكيد لعمليات (الحذف والتعديل)
            const setupConfirmation = (selector, config) => {
                document.querySelectorAll(selector).forEach(element => {
                    // نستخدم 'submit' للنماذج و 'click' للأزرار
                    const eventType = element.tagName === 'FORM' ? 'submit' : 'click';

                    element.addEventListener(eventType, function(e) {
                        if (this.dataset.confirmed === "true")
                            return; // منع التكرار بعد التأكيد

                        e.preventDefault();
                        const form = this.tagName === 'FORM' ? this : this.closest('form');

                        Swal.fire({
                            title: config.title,
                            text: config.text,
                            icon: config.icon,
                            showCancelButton: true,
                            confirmButtonColor: config.confirmColor,
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: config.confirmText,
                            cancelButtonText: 'تراجع',
                            reverseButtons: true,
                            customClass: {
                                confirmButton: 'rounded-pill px-4',
                                cancelButton: 'rounded-pill px-4'
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // التحقق من كلمة المرور فقط في حال كانت عملية تعديل
                                if (selector === '.update-form') {
                                    const pass = form.querySelector(
                                        'input[name="password"]').value;
                                    const conf = form.querySelector(
                                        'input[name="password_confirmation"]').value;
                                    if (pass.length > 0 && pass !== conf) {
                                        Swal.fire('خطأ!', 'كلمة المرور غير متطابقة',
                                            'error');
                                        return;
                                    }
                                }

                                this.dataset.confirmed = "true";
                                form.submit();
                            }
                        });
                    });
                });
            };

            // تشغيل تأكيد الحذف
            setupConfirmation('.delete-btn', {
                title: 'هل أنت متأكد؟',
                text: "سيتم حذف هذا المستخدم نهائياً!",
                icon: 'warning',
                confirmColor: '#dc3545',
                confirmText: 'نعم، احذف!'
            });

            // تشغيل تأكيد التعديل
            setupConfirmation('.update-form', {
                title: 'تأكيد التعديل',
                text: "هل أنت متأكد من حفظ التغييرات الجديدة؟",
                icon: 'question',
                confirmColor: '#ffc107',
                confirmText: 'نعم، احفظ'
            });

            // 2. رسائل النجاح (Toast)
            @if (session('success'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: "{{ session('success') }}",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif
        });
    </script>
@endpush
