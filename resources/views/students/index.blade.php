@extends('layouts.app')

@section('title', 'إدارة الطلاب')

@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/css/bootstrap.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.3.6/css/dataTables.bootstrap4.css">
    <link rel="stylesheet" href="{{ asset('assets/css/user_table.css') }}" />
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap4.min.css">
    <style>
        .page-header {
            border: none !important;
            background: none !important;
            box-shadow: none !important;
        }
        .header-btns-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 8px;
        }        .custom-btn-header {
            height: 38px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            font-size: 0.85rem !important;
            font-weight: bold !important;
            border-radius: 8px !important;
            padding: 0 15px !important;
            border-width: 2px !important;
            transition: all 0.3s ease;
            white-space: nowrap;
        }
        .action-btn {
            width: 34px !important;
            height: 34px !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            transition: all 0.2s;
            padding: 0 !important;
        }
        .action-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1) !important;
        }
        select.custom-select {
            padding-right: 5px !important;
            padding-left: 5px !important;
            background-image: none !important;
            text-align: center !important;
            text-align-last: center !important;
            width: 60px !important;
        }
        #studentsTable td:first-child {
            text-align: right !important;
            padding-right: 20px !important;
        }
        .dataTables_wrapper .row:first-child { display: flex !important; flex-direction: row !important; justify-content: space-between !important; align-items: center; width: 100%; margin: 0 0 1rem 0; padding: 0 15px; }
        .bg-blue-subtle { background-color: #e7f1ff !important; color: #0d6efd !important; }
        .bg-pink-subtle { background-color: #fff0f3 !important; color: #d63384 !important; }
        #studentsTable { width: 100% !important; margin: 0 !important; }
        .dataTables_filter { text-align: left !important; }
        .dataTables_length { text-align: right !important; }
        .btn-excel { background-color: #1d6f42 !important; color: white !important; border-radius: 8px !important; padding: 5px 15px !important; font-weight: bold !important; display: flex !important; align-items: center !important; gap: 5px !important; }

        @media (max-width: 768px) {
            }
            .page-header { flex-direction: column; align-items: flex-start !important; }
            .header-btns-wrapper { width: 100%; justify-content: center; flex-wrap: wrap; }
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
<div class="container-fluid" >
    <div class="page-header d-flex justify-content-between align-items-center mb-3 px-3">
        <div class="d-flex align-items-center gap-2">
            <div class="bg-white p-1 rounded-2 shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="bi bi-person-badge-fill fs-4 text-primary"></i>
            </div>
            <h1 class="page-title m-0 h4 fw-bold">إدارة الطلاب</h1>
        </div>

        <div class="header-btns-wrapper">
            <a href="{{ route('student.index') }}"
               class="btn {{ !request('status') ? 'btn-secondary' : 'btn-outline-secondary' }} custom-btn-header">
                <i class="bi bi-people-fill ms-1"></i> الطلاب الحاليين
            </a>

            <a href="{{ route('student.index', ['status' => 'trash']) }}"
               class="btn {{ request('status') == 'trash' ? 'btn-danger' : 'btn-outline-danger' }} custom-btn-header">
                <i class="bi bi-trash-fill ms-1"></i> الأرشيف
            </a>

            <div class="vr mx-1 text-muted opacity-25 d-none d-md-block" style="height: 25px;"></div>

            <a href="{{ route('student.create') }}"
               class="btn btn-primary custom-btn-header shadow-sm">
                <i class="bi bi-plus-lg ms-1"></i> طالب جديد
            </a>
        </div>
    </div>

    <div class="card card-table shadow-sm border-0 overflow-hidden">
        <div class="card-body p-0">
            <div class="p-3">
                <div class="table-responsive">
                    <table id="studentsTable" class="table table-striped table-bordered align-middle mb-0">
                        <thead class="bg-light text-secondary">
                            <tr>
                                <th class="text-center">اسم الطالب/ة</th>
                                <th class="text-center">رقم الهوية</th>
                                <th class="text-center">الجنس</th>
                                <th class="text-center">الحالة</th>
                                <th class="text-center">الدورات</th>
                                <th class="text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($students as $student)
                                <tr>
                                    <td class="text-right pr-4">
                                        <span class="fw-bold {{ request('status') == 'trash' ? 'text-danger' : '' }}">
                                             {{ $student->full_name }}
                                         </span>
                                    </td>
                                    <td class="text-center">
                                            <span class="badge bg-light text-dark border px-4 py-2 fw-bold fs-7 id-badge"
                                                style="display: inline-block; min-width: 110px;">
                                                {{ $student->id_number }}
                                            </span>
                                        </td>
                                    <td class="text-center">
                                        @if ($student->gender == 'male')
                                            <span class="badge bg-blue-subtle text-primary border px-3"><i class="bi bi-person-fill ms-1"></i> ذكر </span>
                                        @else
                                            <span class="badge bg-pink-subtle text-danger border px-3"><i class="bi bi-person ms-1"></i> أنثى </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if(request('status') == 'trash')
                                            <span class="badge bg-danger text-white rounded-pill px-3">محذوف</span>
                                        @else
                                            <span class="badge rounded-pill border {{ $student->is_displaced ? 'bg-warning-subtle text-dark' : 'bg-success-subtle text-success' }} px-3 py-1">
                                                {{ $student->is_displaced ? 'نازح' : 'مقيم' }}
                                            </span>
                                            <div class="d-flex justify-content-center gap-2">
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-warning text-dark rounded-pill px-3 py-1 shadow-sm" style="font-size: 0.75rem;">
                                            {{ $student->courses_count ?? 0 }} دورات
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            @if(request('status') == 'trash')
                                                <form action="{{ route('student.restore', $student->id) }}" method="POST" id="restoreForm{{ $student->id }}">
                                                    @csrf
                                                    <button type="button" onclick="confirmRestore({{ $student->id }})"
                                                        class="btn btn-sm btn-outline-success action-btn"
                                                        style="width: auto !important; border-radius: 15px !important; padding: 0 10px !important;">
                                                        <i class="bi bi-arrow-counterclockwise ms-1"></i> استعادة
                                                    </button>
                                                </form>
                                            @else
                                                <a href="{{ route('parents.index', ['id_number' => $student->id_number]) }}" class="btn btn-sm btn-outline-secondary rounded-circle action-btn" title="ولي الأمر"><i class="bi bi-person-vcard"></i></a>
                                                <button class="btn btn-sm btn-outline-info rounded-circle action-btn course-btn" data-student-id="{{ $student->id }}" data-student-name="{{ $student->full_name }}" data-student-courses="{{ isset($student->course_ids) ? json_encode(explode(',', $student->course_ids)) : '[]' }}" title="إدارة الدورات"><i class="bi bi-journal-plus"></i></button>
                                                <button class="btn btn-sm btn-outline-warning rounded-circle action-btn" data-toggle="modal" data-target="#editStudentModal{{ $student->id }}" title="تعديل"><i class="bi bi-pencil-square"></i></button>
                                                <form action="{{ route('student.destroy', $student->id) }}" method="POST" id="deleteForm{{ $student->id }}" class="d-inline">
                                                    @csrf @method('DELETE')
                                                    <button type="button" onclick="confirmDelete({{ $student->id }})" class="btn btn-sm btn-outline-danger rounded-circle action-btn" title="حذف"><i class="bi bi-trash3"></i></button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                                @if(request('status') != 'trash')
                                    @include('students.edit_modal', ['student' => $student])
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- مودال إدارة الدورات --}}
    <div class="modal fade" id="courseStudentModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <form id="courseForm" method="POST">
                @csrf @method('PUT')
                <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                    <div class="modal-header bg-info text-white border-0 py-3">
                        <h5 class="modal-title fw-bold"><i class="bi bi-book-half ms-2"></i>دورات الطالب: <span id="modal_student_name"></span></h5>
                        <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body p-4 text-start">
                        <input type="hidden" name="update_courses_only" value="1">
                        <div class="row g-2">
                            @foreach (\DB::table('courses')->whereIn('type', ['students', null])->orWhereNull('type')->get() as $course)
                                <div class="col-6 mb-2">
                                    <div class="p-2 bg-light rounded border d-flex align-items-center justify-content-start" style="cursor: pointer;">
                                        <input class="form-check-input course-checkbox m-0" type="checkbox" name="courses[]" value="{{ $course->id }}" id="student_course_{{ $course->id }}" style="position: relative; margin-left: 10px !important;">
                                        <label class="form-check-label fw-bold mb-0 flex-grow-1 cursor-pointer" for="student_course_{{ $course->id }}" style="text-align: right; padding-right: 10px;">{{ $course->name }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-info text-white px-5 fw-bold rounded-pill">حفظ التغييرات</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.6/js/dataTables.js"></script>
    <script src="https://cdn.datatables.net/2.3.6/js/dataTables.bootstrap4.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

    <script>
        function confirmDelete(id) {
            Swal.fire({ title: 'هل أنت متأكد؟', text: "سيتم حذف بيانات الطالب ونقلها للأرشيف!", icon: 'warning', showCancelButton: true, confirmButtonColor: '#dc3545', confirmButtonText: 'نعم، احذف', cancelButtonText: 'تراجع', reverseButtons: true
            }).then((result) => { if (result.isConfirmed) document.getElementById('deleteForm' + id).submit(); });
        }
        function confirmRestore(id) {
            Swal.fire({ title: 'استعادة الطالب؟', text: "سيتم إعادة تفعيل ملف الطالب مرة أخرى.", icon: 'question', showCancelButton: true, confirmButtonColor: '#198754', confirmButtonText: 'نعم، استعادة', cancelButtonText: 'إلغاء', reverseButtons: true
            }).then((result) => { if (result.isConfirmed) document.getElementById('restoreForm' + id).submit(); });
        }

        $(document).ready(function() {
            var d = new Date();
            var dateString = d.getFullYear() + '-' + (d.getMonth() + 1) + '-' + d.getDate();

            if (!$.fn.dataTable.isDataTable('#studentsTable')) {
                $('#studentsTable').DataTable({
                    "responsive": true,
                    "language": { "sProcessing": "جاري التحميل...", "sLengthMenu": "أظهر _MENU_ طلاب", "sSearch": "بحث سريع:", "sInfo": "عرض _START_ إلى _END_ من أصل _TOTAL_ طالب", "paginate": { "first": "«", "last": "»", "next": "›", "previous": "‹" }, "zeroRecords": "لا توجد بيانات" },
                    "dom": "<'row mb-3 align-items-center'<'col-md-4 text-right'l><'col-md-4 text-center'B><'col-md-4 text-left'f>>" + "<'row'<'col-sm-12' <'table-responsive' tr> >>" + "<'row mt-3'<'col-sm-12'p>>" + "<'row'<'col-sm-12 text-center'i>>",
                    "buttons": [{ extend: 'excelHtml5', text: '<i class="bi bi-file-earmark-excel-fill ms-1"></i> تصدير إكسل', className: 'btn btn-excel', title: 'قائمة الطلاب - ' + dateString, filename: 'تقرير_الطلاب_' + dateString, exportOptions: { columns: [0, 1, 2, 3, 4] } }]
                });
            }

            $(document).on('click', '.course-btn', function() {
                const studentId = $(this).data('student-id');
                const studentName = $(this).data('student-name');
                let studentCourses = $(this).data('student-courses');
                $('#courseForm').attr('action', "{{ url('student') }}/" + studentId);
                $('#modal_student_name').text(studentName);
                $('.course-checkbox').prop('checked', false);
                if (Array.isArray(studentCourses)) studentCourses.forEach(id => { $(`#student_course_${id}`).prop('checked', true); });
                $('#courseStudentModal').modal('show');
            });

            @if (session('success')) Swal.fire({ icon: 'success', title: 'تم بنجاح', text: "{{ session('success') }}", confirmButtonColor: '#0dcaf0', timer: 2500 }); @endif
            @if (session('error')) Swal.fire({ icon: 'error', title: 'خطأ', text: "{{ session('error') }}", confirmButtonColor: '#dc3545' }); @endif
        });
    </script>
@endpush
