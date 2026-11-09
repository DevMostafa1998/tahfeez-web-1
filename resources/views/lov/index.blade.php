@extends('layouts.app')

@section('title', 'إدارة القوائم المنسدلة الموحدة (LOV)')

@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.6/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        .action-btn {
            width: 32px !important; height: 32px !important;
            display: inline-flex !important; align-items: center !important; justify-content: center !important;
            border-radius: 50% !important; font-size: 0.9rem !important; transition: 0.2s; vertical-align: middle;
        }
        #lovTable { width: 100% !important; margin: 0 !important; }
    </style>
@endpush

@section('content')
<div class="container-fluid p-4" dir="rtl text-right">
    {{-- الهيدر العلوي --}}
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center" style="gap: 15px;">
            <div class="bg-white p-2 rounded border shadow-sm">
                <i class="bi bi-tags-fill h3 text-primary m-0"></i>
            </div>
            <h1 class="page-title m-0 h4 font-weight-bold">إدارة القوائم المنسدلة الموحدة (LOV)</h1>
        </div>

        <button type="button" class="btn btn-success btn-sm d-flex align-items-center px-3 py-2 rounded shadow-sm" style="gap: 8px;" data-toggle="modal" data-target="#addLovModal">
            <i class="bi bi-plus-lg"></i><span>إضافة خيار وقائمة جديدة</span>
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success text-right">{{ session('success') }}</div>
    @endif

    {{-- بطاقة الجدول الرئيسي --}}
    <div class="card shadow-sm border-0 overflow-hidden">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table id="lovTable" class="table table-striped table-bordered align-middle mb-0">
                    <thead class="bg-light text-secondary">
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">المركز التابع له</th>
                            <th class="text-center">رمز القائمة (Type)</th>
                            <th class="text-center">القيمة المظهرية (Description)</th>
                            <th class="text-center">الحالة (IsValid)</th>
                            <th class="text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($lovs as $index => $item)
                            <tr>
                                <td class="text-center text-muted">{{ $index + 1 }}</td>
                                <td class="text-center">
                                    @if($item->center_id && $item->center)
                                        <span class="badge badge-info px-2 py-1">{{ $item->center->name }}</span>
                                    @else
                                        <span class="badge badge-secondary px-2 py-1">عام (جميع المراكز)</span>
                                    @endif
                                </td>
                                <td class="text-center font-weight-bold text-primary">{{ $item->type }}</td>
                                <td class="text-right px-3 font-weight-bold">{{ $item->description }}</td>
                                <td class="text-center">
                                    @if($item->is_valid)
                                        <span class="badge badge-success px-3 py-1.5 badge-pill">نشط / فعال</span>
                                    @else
                                        <span class="badge badge-secondary px-3 py-1.5 badge-pill">معطل</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center align-items-center" style="gap: 8px;">
                                        <button type="button" class="btn btn-sm btn-outline-primary action-btn edit-lov-btn"
                                            data-toggle="modal" data-target="#editLovModal"
                                            data-id="{{ $item->id }}"
                                            data-type="{{ $item->type }}"
                                            data-description="{{ $item->description }}"
                                            data-center_id="{{ $item->center_id }}"
                                            data-valid="{{ $item->is_valid }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger action-btn delete-lov-btn"
                                            data-id="{{ $item->id }}" data-desc="{{ $item->description }}">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- مودال الإضافة --}}
<div class="modal fade" id="addLovModal" tabindex="-1" role="dialog" aria-hidden="true" dir="rtl text-right">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form action="{{ route('lov.store') }}" method="POST" class="modal-content border-0 shadow-lg text-right">
            @csrf
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title font-weight-bold"><i class="bi bi-plus-circle mr-2"></i> إضافة خيار جديد</h5>
                <button type="button" class="close text-white ml-0 mr-auto" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                {{-- القائمة المنسدلة للمراكز --}}
                <div class="form-group">
                    <label class="font-weight-bold">المركز التابع له</label>
                    <select name="center_id" class="form-control">
                        <option value="" {{ empty($selectedCenterId) ? 'selected' : '' }}>عام (متاح لجميع المراكز)</option>
                        @foreach($centers as $center)
                            <option value="{{ $center->id }}" {{ (isset($selectedCenterId) && $selectedCenterId == $center->id) ? 'selected' : '' }}>
                                {{ $center->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="font-weight-bold">نوع/رمز القائمة (Type) <span class="text-danger">*</span></label>
                    <input type="text" name="type" class="form-control" placeholder="مثال: TestType أو StudentStatus" required autocomplete="off">
                    <small class="text-muted">هذا الرمز الموحد الذي ستستدعيه برمجياً في المنسدلة.</small>
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">الاسم/الوصف المظهري (Description) <span class="text-danger">*</span></label>
                    <input type="text" name="description" class="form-control" placeholder="مثال: تسميع، مراجعة، ممتاز..." required autocomplete="off">
                </div>
                <div class="form-group form-check pt-2">
                    <input type="checkbox" name="is_valid" class="form-check-input shadow-none" id="add_is_valid" value="1" checked>
                    <label class="form-check-label font-weight-bold text-success mr-4" for="add_is_valid">تفعيل الخيار فوراً للاستخدام</label>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="submit" class="btn btn-success font-weight-bold px-4">حفظ وإضافة</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
            </div>
        </form>
    </div>
</div>

{{-- مودال التعديل --}}
<div class="modal fade" id="editLovModal" tabindex="-1" role="dialog" aria-hidden="true" dir="rtl text-right">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form id="editLovForm" method="POST" class="modal-content border-0 shadow-lg text-right">
            @csrf
            @method('PUT')
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title font-weight-bold"><i class="bi bi-pencil-square mr-2"></i> تعديل بيانات الخيار</h5>
                <button type="button" class="close text-white ml-0 mr-auto" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4">
                <div class="form-group">
                    <label class="font-weight-bold">المركز التابع له</label>
                    <select name="center_id" id="edit_center_id" class="form-control">
                        <option value="">عام (متاح لجميع المراكز)</option>
                        @foreach($centers as $center)
                            <option value="{{ $center->id }}">{{ $center->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="font-weight-bold">نوع/رمز القائمة (Type) <span class="text-danger">*</span></label>
                    <input type="text" name="type" id="edit_type" class="form-control" required autocomplete="off">
                </div>
                <div class="form-group">
                    <label class="font-weight-bold">الاسم/الوصف المظهري (Description) <span class="text-danger">*</span></label>
                    <input type="text" name="description" id="edit_description" class="form-control" required autocomplete="off">
                </div>
                <div class="form-group form-check pt-2">
                    <input type="checkbox" name="is_valid" class="form-check-input shadow-none" id="edit_is_valid" value="1">
                    <label class="form-check-label font-weight-bold text-primary mr-4" for="edit_is_valid">هذا الخيار نشط وصالح للاستخدام</label>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="submit" class="btn btn-primary font-weight-bold px-4">تحديث البيانات</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.6/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.6/js/dataTables.bootstrap4.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            var table = $('#lovTable').DataTable({
                "responsive": true,
                "language": {
                    "sSearch": "البحث السريع:",
                    "sLengthMenu": "إظهار _MENU_ سجلات",
                    "sInfo": "عرض _TOTAL_ خيار قائمة",
                    "paginate": { "next": "›", "previous": "‹" }
                }
            });

            // تعبئة المودال عند التعديل
            $(document).on('click', '.edit-lov-btn', function() {
                const id = $(this).data('id');
                const centerId = $(this).data('center_id') || ""; // إسناد القيمة أو فارغ إذا كانت null

                $('#edit_type').val($(this).data('type'));
                $('#edit_description').val($(this).data('description'));
                $('#edit_center_id').val(centerId);
                $('#edit_is_valid').prop('checked', $(this).data('valid') == 1);

                $('#editLovForm').attr('action', `/lov/${id}`);
            });

            // منطق الحذف AJAX
            $(document).on('click', '.delete-lov-btn', function() {
                const id = $(this).data('id');
                const desc = $(this).data('desc');
                Swal.fire({
                    title: 'هل أنت متأكد؟',
                    text: `سيتم حذف الخيار "${desc}" من القوائم نهائياً!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'حذف',
                    cancelButtonText: 'إلغاء'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `/lov/${id}`,
                            type: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                            success: function() {
                                Swal.fire('تم الحذف!', '', 'success').then(() => location.reload());
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
