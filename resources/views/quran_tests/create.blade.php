@extends('layouts.app')

@section('content')
    <div class="container mt-5" dir="rtl text-right">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">إضافة اختبار تسميع جديد</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('quran_tests.store') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">اختر الطالب</label>
                            <select name="studentId" class="form-select @error('studentId') is-invalid @enderror" required>
                                <option value="">اختر من القائمة...</option>
                                @isset($students)
                                    @foreach ($students as $student)
                                        <option value="{{ $student->id }}" class="text-dark" style="color: black !important;">
                                            {{ $student->full_name }}
                                        </option>
                                    @endforeach
                                @endisset
                            </select>
                            @error('studentId')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">تاريخ الاختبار</label>
                            <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">عدد الأجزاء المختبرة</label>
                            <input type="number" name="juz_count" class="form-control" min="1" max="30"
                                placeholder="مثلاً: 5" required>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">نوع الاختبار</label>
                            <select name="examType" class="form-select">
                                <option value="سرد">سرد</option>
                                <option value="اجزاء مجتمعه">أجزاء مجتمعة</option>
                            </select>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label class="form-label">النتيجة</label>
                            <select name="result_status" class="form-select text-center">
                                <option value="ناجح" class="text-success font-weight-bold">ناجح</option>
                                <option value="راسب" class="text-danger font-weight-bold">راسب</option>
                            </select>
                        </div>

                        <div class="col-12 mb-3">
                            <label class="form-label">ملاحظات إضافية</label>
                            <textarea name="note" class="form-control" rows="3" placeholder="اكتب أي ملاحظات عن أداء الطالب هنا..."></textarea>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary px-5">حفظ البيانات</button>
                        <a href="{{ route('quran_tests.index') }}" class="btn btn-outline-secondary">إلغاء</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
