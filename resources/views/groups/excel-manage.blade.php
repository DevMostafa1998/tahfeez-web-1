@extends('layouts.app') 

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">إدارة ملفات الإكسل - مجموعة: {{ $group->GroupName }}</h5>
                    <a href="{{ route('group.show', $group->id) }}" class="btn btn-sm btn-light">عودة للمجموعة</a>
                </div>
                <div class="card-body p-4 text-end" dir="rtl">

                    <div class="section-export mb-5 p-3 border rounded bg-light">
                        <h6 class="fw-bold mb-3"><i class="bi bi-download ms-2"></i> الخطوة الأولى: تنزيل النموذج</h6>
                        <p class="text-muted small">قم بتنزيل ملف الإكسل الذي يحتوي على أسماء الطلاب الحاليين في هذه المجموعة لتعبئة بيانات الحفظ لهم.</p>
                        <a href="{{ route('excel.export', $group->id) }}" class="btn btn-success px-4">
                             تنزيل نموذج الإكسل (.xlsx)
                        </a>
                    </div>

                    <hr>

                    <div class="section-import mt-5 p-3 border rounded bg-light">
                        <h6 class="fw-bold mb-3"><i class="bi bi-upload ms-2"></i> الخطوة الثانية: رفع الملف بعد التعبئة</h6>
                        <p class="text-muted small">بعد إدخال بيانات (اسم السورة، التاريخ، من آية، إلى آية)، قم برفع الملف هنا لتحديث قاعدة البيانات.</p>
                        
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert" style="background-color: #d4edda; border-color: #c3e6cb; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                                <strong> تم بنجاح!</strong> {{ session('success') }}
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="background-color: #f8d7da; border-color: #f5c6cb; color: #721c24; padding: 15px; margin-bottom: 20px; border-radius: 5px;">
                                <strong> خطأ:</strong> {{ session('error') }}
                            </div>
                        @endif

                        <form action="{{ route('excel.import') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="group_id" value="{{ $group->id }}">

                            <div class="mb-3">
                                <label class="form-label fw-bold">اختر الملف:</label>
                                <input type="file" name="excel_file" class="form-control" required accept=".xlsx, .xls">
                            </div>

                            <div class="d-flex align-items-start mb-4" style="gap: 10px;">
                                <input class="form-check-input" type="checkbox" name="auto_export" id="autoExport" value="1" 
                                    style="width: 20px; height: 20px; cursor: pointer; margin: 0; flex-shrink: 0; border-color: #adb5bd;">
                                
                                <label class="form-check-label text-primary fw-bold" for="autoExport" 
                                    style="cursor: pointer; padding-top: 3px; line-height: 1.2; margin: 0;">
                                    تنزيل ملف إكسل جديد ومحدث تلقائياً بعد الاستيراد
                                </label>
                            </div>

                            <button type="submit" class="btn btn-warning fw-bold px-5">
                                بدء عملية الاستيراد الآن
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

@if(session('auto_download'))
<script>
    document.addEventListener("DOMContentLoaded", function() {
        setTimeout(function() {
            window.location.href = "{{ session('auto_download') }}";
        }, 1500);
    });
</script>
@endif
<script>
document.addEventListener("DOMContentLoaded", function() {
    const autoExportCheckbox = document.getElementById("autoExport");

    const savedStatus = localStorage.getItem("autoExportStatus");

    if (savedStatus === "true") {
        autoExportCheckbox.checked = true;
    } else if (savedStatus === "false") {
        autoExportCheckbox.checked = false;
    } 

    autoExportCheckbox.addEventListener("change", function() {
        localStorage.setItem("autoExportStatus", autoExportCheckbox.checked);
    });
});
</script>
@endsection