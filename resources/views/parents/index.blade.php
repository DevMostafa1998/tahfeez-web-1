@extends('layouts.app')

@section('content')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقرير المتابعة القرآني - {{ $student->full_name }}</title>

    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: #f1f5f9;
        }

        .bg-gradient-custom {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        }

        .premium-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .premium-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .progress-circle {
            transition: stroke-dashoffset 1.5s ease-in-out;
        }

        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>

    <div class="bg-gradient-custom text-slate-800 min-h-screen pb-10" dir="rtl">

        <header class="max-w-7xl mx-auto px-4 sm:px-6 py-8 animate__animated animate__fadeIn">
            <div
                class="flex flex-col md:flex-row justify-between items-center gap-6 bg-white p-6 rounded-[2rem] shadow-sm border border-slate-200">
                <div class="flex items-center gap-5 w-full md:w-auto">
                    <div class="relative group cursor-pointer">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($student->full_name) }}&background=0f766e&color=fff&size=200&font-size=0.35"
                            class="w-20 h-20 rounded-2xl shadow-lg group-hover:rotate-3 transition-transform duration-300">
                        <div
                            class="absolute -bottom-1 -right-1 bg-emerald-500 w-5 h-5 rounded-full border-2 border-white animate-pulse">
                        </div>
                    </div>
                    <div>
                        <h1 class="text-2xl font-black text-slate-800">{{ $student->full_name }}</h1>
                        <div class="flex flex-wrap gap-2 mt-2 text-xs font-bold">
                            <span class="bg-amber-100 text-amber-700 px-3 py-1 rounded-full flex items-center gap-2">
                                <i class="fas fa-mosque"></i> {{ $student->center_name ?? 'المركز العام' }}
                            </span>
                            <span class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full flex items-center gap-2">
                                <i class="fas fa-users"></i>
                                {{ $student->groups->first()->GroupName ?? 'غير ملتحق بحلقة' }}
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col items-end">
                    <p class="text-[10px] text-slate-400 font-bold mb-1 uppercase tracking-wider">تاريخ التقرير</p>
                    <div
                        class="bg-slate-50 px-4 py-2 rounded-xl border border-slate-200 font-bold text-slate-600 flex items-center gap-2 text-sm">
                        <i class="far fa-calendar-check text-emerald-500"></i>
                        {{ \Carbon\Carbon::now()->locale('ar')->translatedFormat('l، j F Y') }}
                    </div>
                </div>
            </div>
        </header>

        <main class="max-w-7xl mx-auto px-4 sm:px-6 space-y-8">

            <section
                class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 border border-white animate__animated animate__fadeInDown">
                <div class="flex items-center justify-between mb-8 pb-4 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div
                            class="w-10 h-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                            <i class="fas fa-chart-pie text-xl"></i>
                        </div>
                        <h3 class="text-xl font-black text-slate-800">مؤشر الإنجاز العام</h3>
                    </div>
                    @if ($percentage >= 100)
                        <span
                            class="bg-emerald-100 text-emerald-700 px-4 py-1.5 rounded-full font-bold text-sm flex items-center gap-2">
                            <i class="fas fa-check-circle"></i> أتم الحفظ
                        </span>
                    @else
                        <span
                            class="bg-amber-100 text-amber-700 px-4 py-1.5 rounded-full font-bold text-sm flex items-center gap-2">
                            <i class="fas fa-running"></i> جاري الحفظ
                        </span>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
                    <div class="md:col-span-3 flex flex-col items-center justify-center">
                        <div class="relative w-44 h-44">
                            @php
                                $circumference = 2 * 3.14159 * 58;
                                $dashOffset = $circumference - ($percentage / 100) * $circumference;
                                $strokeColor =
                                    $percentage == 100 ? '#10b981' : ($percentage > 50 ? '#3b82f6' : '#f59e0b');
                            @endphp
                            <svg class="w-full h-full transform -rotate-90">
                                <circle cx="88" cy="88" r="58" stroke="#f1f5f9" stroke-width="12"
                                    fill="none" />
                                <circle cx="88" cy="88" r="58" stroke="{{ $strokeColor }}" stroke-width="12"
                                    fill="none" stroke-dasharray="{{ $circumference }}"
                                    stroke-dashoffset="{{ $dashOffset }}" stroke-linecap="round"
                                    class="progress-circle shadow-lg drop-shadow-lg" />
                            </svg>
                            <div class="absolute inset-0 flex flex-col items-center justify-center">
                                <span class="text-4xl font-black text-slate-800">{{ round($percentage) }}<span
                                        class="text-lg">%</span></span>
                                <span class="text-xs text-slate-400 font-bold mt-1">من المصحف</span>
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-5 space-y-6">
                        <div>
                            <div class="flex justify-between mb-2 text-sm font-bold">
                                <span class="text-slate-500">عدد الأجزاء المكتملة</span>
                                <span class="text-emerald-600 bg-emerald-50 px-2 rounded">{{ $completedPartsCount }} من 30
                                    جزء</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-5 overflow-hidden shadow-inner">
                                <div class="bg-gradient-to-r from-emerald-500 to-teal-400 h-full rounded-full transition-all duration-1000 relative"
                                    style="width: {{ ($completedPartsCount / 30) * 100 }}%">
                                    <div class="absolute top-0 right-0 bottom-0 w-full bg-white/20 animate-pulse"></div>
                                </div>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1 mr-1">
                                * يتم احتساب الجزء كمكتمل فقط بعد اجتياز اختباره بنجاح.
                            </p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div
                                class="p-4 bg-blue-50 rounded-2xl border border-blue-100 hover:bg-blue-100 transition-colors">
                                <p class="text-[10px] text-blue-600 font-bold mb-1 uppercase tracking-wide">آخر سورة تم
                                    تسميعها</p>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-book-open text-blue-400 opacity-50"></i>
                                    <div>
                                        <p class="font-black text-slate-800 text-lg leading-none">
                                            {{ $lastMemo->sura_name ?? '---' }}</p>
                                        @if (isset($lastMemo->date))
                                            <span
                                                class="text-[9px] text-slate-400">{{ \Carbon\Carbon::parse($lastMemo->date)->diffForHumans() }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div
                                class="p-4 bg-purple-50 rounded-2xl border border-purple-100 hover:bg-purple-100 transition-colors">
                                <p class="text-[10px] text-purple-600 font-bold mb-1 uppercase tracking-wide">توقف عند الآية
                                </p>
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-list-ol text-purple-400 opacity-50"></i>
                                    <p class="font-black text-slate-800 text-lg">{{ $lastMemo->verses_to ?? '---' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-4 h-full">
                        <div
                            class="h-full bg-amber-50 rounded-3xl p-6 border border-amber-100 relative overflow-hidden group">
                            <div
                                class="absolute -right-6 -top-6 w-24 h-24 bg-amber-200 rounded-full opacity-20 group-hover:scale-150 transition-transform duration-500">
                            </div>

                            <div class="flex items-center justify-between mb-4 relative z-10">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-comment-medical text-amber-500 text-lg"></i>
                                    <h4 class="font-bold text-slate-800 text-sm">ملاحظة المحفظ/ة</h4>
                                </div>
                                @if ($lastMemo)
                                    <span
                                        class="text-[10px] bg-white px-2 py-1 rounded-lg text-slate-400 font-mono shadow-sm">
                                        {{ \Carbon\Carbon::parse($lastMemo->date)->format('Y-m-d') }}
                                    </span>
                                @endif
                            </div>

                            <div class="relative z-10">
                                <p class="text-sm text-slate-700 leading-relaxed font-semibold italic">
                                    "{{ $lastMemo->note ?? 'لا توجد ملاحظات مسجلة للحفظ الأخير، واصل التقدم والمثابرة.' }}"
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section
                class="bg-white rounded-[2.5rem] p-8 shadow-lg shadow-slate-100 premium-card animate__animated animate__fadeInUp"
                style="animation-delay: 0.1s">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-1.5 h-8 bg-indigo-500 rounded-full"></div>
                    <h3 class="text-xl font-black text-slate-800">تفاصيل حفظ السور</h3>
                    <span class="mr-auto text-xs font-bold text-slate-400 bg-slate-100 px-3 py-1 rounded-full">
                        {{ isset($startFromEnd) && $startFromEnd ? 'المسار: من الناس إلى الفاتحة' : 'المسار: من الفاتحة إلى الناس' }}
                    </span>
                </div>

                @if (isset($memorizedSurahs) && count($memorizedSurahs) > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach ($memorizedSurahs as $surah)
                            <div
                                class="p-4 rounded-2xl border transition-all duration-300 hover:shadow-md {{ $surah->is_completed ? 'bg-emerald-50/50 border-emerald-100' : 'bg-white border-slate-100' }}">
                                <div class="flex justify-between items-center mb-3">
                                    <div class="flex items-center gap-2">
                                        <div
                                            class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold {{ $surah->is_completed ? 'bg-emerald-200 text-emerald-800' : 'bg-slate-200 text-slate-600' }}">
                                            {{ $loop->iteration }}
                                        </div>
                                        <span class="font-bold text-slate-800">{{ $surah->sura_name }}</span>
                                    </div>
                                    @if ($surah->is_completed)
                                        <span
                                            class="text-[10px] bg-emerald-500 text-white px-2 py-0.5 rounded-full shadow-sm flex items-center gap-1">
                                            <i class="fas fa-check"></i> تم الحفظ
                                        </span>
                                    @else
                                        <span
                                            class="text-[10px] bg-amber-500 text-white px-2 py-0.5 rounded-full shadow-sm flex items-center gap-1">
                                            <i class="fas fa-sync fa-spin text-[8px]"></i> جاري الحفظ
                                        </span>
                                    @endif
                                </div>

                                <div class="flex justify-between items-end text-xs text-slate-500 mb-2 font-bold">
                                    <span>
                                        الآيات: {{ $surah->min_v }} <i class="fas fa-arrow-left text-[8px] mx-1"></i>
                                        {{ $surah->max_v }}
                                    </span>
                                    <span class="text-slate-400">الإجمالي: {{ $surah->total_verses }}</span>
                                </div>

                                <div class="w-full bg-slate-200 rounded-full h-1.5 overflow-hidden">
                                    @php
                                        $s_perc =
                                            $surah->total_verses > 0 ? ($surah->max_v / $surah->total_verses) * 100 : 0;
                                        if ($surah->is_completed) {
                                            $s_perc = 100;
                                        }
                                    @endphp
                                    <div class="{{ $surah->is_completed ? 'bg-emerald-500' : 'bg-indigo-500' }} h-full rounded-full"
                                        style="width: {{ $s_perc }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-10 bg-slate-50 rounded-3xl border border-dashed border-slate-200">
                        <i class="fas fa-quran text-4xl text-slate-300 mb-3"></i>
                        <p class="text-slate-500 font-bold">لم يتم تسجيل أي تسميع للطالب حتى الآن.</p>
                    </div>
                @endif
            </section>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">

                <div class="lg:col-span-8 space-y-8">
                    <section
                        class="bg-white rounded-[2.5rem] p-8 shadow-lg shadow-slate-100 premium-card animate__animated animate__fadeInUp"
                        style="animation-delay: 0.2s">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-1.5 h-8 bg-amber-500 rounded-full"></div>
                            <h3 class="text-xl font-black text-slate-800">البيانات الشخصية</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div
                                class="flex items-center gap-4 p-3 hover:bg-slate-50 rounded-2xl transition-all duration-300 hover:-translate-y-1 group">
                                <div
                                    class="w-12 h-12 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center text-xl shadow-sm transition-all duration-300 group-hover:bg-amber-500 group-hover:text-white group-hover:rotate-12">
                                    <i class="fas fa-id-card"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 font-bold">رقم الهوية</p>
                                    <p class="font-bold text-slate-700 font-mono">{{ $student->id_number }}</p>
                                </div>
                            </div>

                            <div
                                class="flex items-center gap-4 p-3 hover:bg-slate-50 rounded-2xl transition-all duration-300 hover:-translate-y-1 group">
                                <div
                                    class="w-12 h-12 rounded-xl bg-rose-50 text-rose-500 flex items-center justify-center text-xl shadow-sm transition-all duration-300 group-hover:bg-rose-500 group-hover:text-white group-hover:rotate-12">
                                    <i class="fas fa-venus-mars"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 font-bold">الجنس</p>
                                    <p class="font-bold text-slate-700">
                                        {{ $student->gender == 'male' ? 'ذكر' : 'أنثى' }}
                                    </p>
                                </div>
                            </div>

                            <div
                                class="flex items-center gap-4 p-3 hover:bg-slate-50 rounded-2xl transition-all duration-300 hover:-translate-y-1 group">
                                <div
                                    class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-500 flex items-center justify-center text-xl shadow-sm transition-all duration-300 group-hover:bg-indigo-500 group-hover:text-white group-hover:rotate-12">
                                    <i class="fas fa-phone"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 font-bold">رقم الهاتف</p>
                                    <p class="font-bold text-slate-700 font-mono">{{ $student->phone_number ?? '---' }}
                                    </p>
                                </div>
                            </div>

                            <div
                                class="flex items-center gap-4 p-3 hover:bg-slate-50 rounded-2xl transition-all duration-300 hover:-translate-y-1 group">
                                <div
                                    class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center text-xl shadow-sm transition-all duration-300 group-hover:bg-emerald-500 group-hover:text-white group-hover:rotate-12">
                                    <i class="fab fa-whatsapp"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 font-bold">رقم الواتساب</p>
                                    <p class="font-bold text-slate-700 font-mono">{{ $student->whatsapp_number ?? '---' }}
                                    </p>
                                </div>
                            </div>

                            <div
                                class="flex items-center gap-4 p-3 hover:bg-slate-50 rounded-2xl transition-all duration-300 hover:-translate-y-1 group">
                                <div
                                    class="w-12 h-12 rounded-xl bg-blue-50 text-blue-500 flex items-center justify-center text-xl shadow-sm transition-all duration-300 group-hover:bg-blue-500 group-hover:text-white group-hover:rotate-12">
                                    <i class="fas fa-birthday-cake"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 font-bold">تاريخ الميلاد</p>
                                    <p class="font-bold text-slate-700">
                                        {{ \Carbon\Carbon::parse($student->date_of_birth)->format('Y-m-d') }}
                                        <span class="text-xs text-slate-400 mr-1">({{ $age }} سنة)</span>
                                    </p>
                                </div>
                            </div>

                            <div
                                class="flex items-center gap-4 p-3 hover:bg-slate-50 rounded-2xl transition-all duration-300 hover:-translate-y-1 group">
                                <div
                                    class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center text-xl shadow-sm transition-all duration-300 group-hover:bg-emerald-600 group-hover:text-white group-hover:rotate-12">
                                    <i class="fas fa-hospital-user"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 font-bold">مكان الميلاد</p>
                                    <p class="font-bold text-slate-700">{{ $student->birth_place ?? 'غير مسجل' }}</p>
                                </div>
                            </div>

                            <div
                                class="flex items-center gap-4 p-3 hover:bg-slate-50 rounded-2xl transition-all duration-300 hover:-translate-y-1 group">
                                <div
                                    class="w-12 h-12 rounded-xl bg-purple-50 text-purple-500 flex items-center justify-center text-xl shadow-sm transition-all duration-300 group-hover:bg-purple-500 group-hover:text-white group-hover:rotate-12">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 font-bold">العنوان</p>
                                    <p class="font-bold text-slate-700">{{ $student->address }}</p>
                                </div>
                            </div>

                            <div
                                class="flex items-center gap-4 p-3 hover:bg-slate-50 rounded-2xl transition-all duration-300 hover:-translate-y-1 group">
                                <div
                                    class="w-12 h-12 rounded-xl bg-teal-50 text-teal-500 flex items-center justify-center text-xl shadow-sm transition-all duration-300 group-hover:bg-teal-500 group-hover:text-white group-hover:rotate-12">
                                    <i class="fas fa-user-tag"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 font-bold">حالة الإقامة</p>
                                    <p
                                        class="font-bold {{ $student->is_displaced ? 'text-red-500' : 'text-emerald-600' }}">
                                        {{ $student->is_displaced ? 'نازح' : 'مقيم' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section
                        class="bg-white rounded-[2.5rem] p-8 shadow-lg shadow-slate-100 premium-card animate__animated animate__fadeInUp"
                        style="animation-delay: 0.3s">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-3">
                                <div class="w-1.5 h-8 bg-sky-500 rounded-full"></div>
                                <h3 class="text-xl font-black text-slate-800">سجل الدورات</h3>
                            </div>
                            <span class="bg-sky-50 text-sky-600 px-3 py-1 rounded-lg text-sm font-bold shadow-sm">
                                {{ $student->courses->count() }} دورة
                            </span>
                        </div>

                        <div class="overflow-hidden rounded-2xl border border-slate-100">
                            <table class="w-full text-right">
                                <thead class="bg-slate-50 text-slate-500 text-xs uppercase font-bold">
                                    <tr>
                                        <th class="px-6 py-4">اسم الدورة</th>
                                        <th class="px-6 py-4">تاريخ التسجيل</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($student->courses as $course)
                                        <tr class="hover:bg-slate-50 transition-colors">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-3">
                                                    <i class="fas fa-certificate text-sky-500"></i>
                                                    <span class="font-bold text-slate-700">{{ $course->name }}</span>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 font-mono text-slate-500 text-sm font-bold">
                                                {{ $course->pivot->created_at ? \Carbon\Carbon::parse($course->pivot->created_at)->format('Y-m-d') : '-' }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="px-6 py-10 text-center text-slate-400">
                                                <i class="fas fa-inbox text-3xl mb-3 block opacity-30"></i>
                                                لا توجد دورات مسجلة حتى الآن
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>

                <div class="lg:col-span-4 space-y-6">
                    <section
                        class="bg-white rounded-[2.5rem] p-8 shadow-xl shadow-slate-200/50 border-t-8 border-amber-500 premium-card animate__animated animate__fadeInRight">
                        <div class="text-center mb-6">
                            <div class="inline-block p-1.5 rounded-full border-4 border-amber-50 mb-4 bg-white shadow-lg">
                                <img src="https://ui-avatars.com/api/?name={{ $teacher ? urlencode($teacher->full_name) : 'No+Teacher' }}&background=1e293b&color=fff&size=150"
                                    class="w-24 h-24 rounded-full">
                            </div>

                            @if ($teacher)
                                <h3 class="text-xl font-black text-slate-800">{{ $teacher->full_name }}</h3>
                                <p
                                    class="text-sm text-slate-500 font-bold mt-1 bg-slate-100 inline-block px-3 py-1 rounded-full">
                                    المحفظ/ة</p>

                                <div class="mt-8 space-y-4">
                                    <div
                                        class="flex items-center gap-4 p-3.5 rounded-2xl bg-white border border-slate-100 shadow-sm transition-all duration-300 hover:border-amber-300 hover:shadow-md hover:-translate-y-1 group">
                                        <div
                                            class="w-12 h-12 rounded-xl bg-amber-50 text-amber-500 flex items-center justify-center text-xl transition-all duration-300 group-hover:bg-amber-500 group-hover:text-white group-hover:rotate-12">
                                            <i class="fas fa-phone-alt"></i>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">رقم
                                                الهاتف</p>
                                            <p class="text-lg font-black text-slate-700 font-mono tracking-tighter">
                                                {{ $teacher->phone_number }}</p>
                                        </div>
                                    </div>

                                    @if ($teacher->whatsapp_number)
                                        <div
                                            class="flex items-center gap-4 p-3.5 rounded-2xl bg-white border border-slate-100 shadow-sm transition-all duration-300 hover:border-emerald-300 hover:shadow-md hover:-translate-y-1 group">
                                            <div
                                                class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-500 flex items-center justify-center text-xl transition-all duration-300 group-hover:bg-emerald-500 group-hover:text-white group-hover:-rotate-12">
                                                <i class="fab fa-whatsapp"></i>
                                            </div>
                                            <div class="flex-1">
                                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                                                    واتساب</p>
                                                <p class="text-lg font-black text-slate-700 font-mono tracking-tighter">
                                                    {{ $teacher->whatsapp_number }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <div
                                    class="mt-8 p-6 rounded-3xl bg-slate-50 border border-dashed border-slate-200 text-center">
                                    <i class="fas fa-user-slash text-slate-300 text-3xl mb-3"></i>
                                    <p class="text-slate-500 font-bold text-sm">لم يتم تعيين معلم لهذا الطالب بعد</p>
                                </div>
                            @endif
                        </div>
                    </section>

                    <section
                        class="bg-slate-800 rounded-[2.5rem] p-8 text-white shadow-lg relative overflow-hidden animate__animated animate__fadeInRight"
                        style="animation-delay: 0.1s">
                        <div
                            class="absolute top-0 right-0 w-32 h-32 bg-amber-500 rounded-full filter blur-[50px] opacity-20 -translate-y-1/2 translate-x-1/2">
                        </div>
                        <div
                            class="absolute bottom-0 left-0 w-32 h-32 bg-indigo-500 rounded-full filter blur-[50px] opacity-20 translate-y-1/2 -translate-x-1/2">
                        </div>

                        <h4 class="font-bold mb-6 flex items-center gap-3 border-b border-white/10 pb-4 relative z-10">
                            <i class="fas fa-mosque text-amber-400 text-lg"></i> بيانات المركز
                        </h4>

                        <div class="space-y-6 relative z-10">
                            <div class="flex gap-4 items-start">
                                <div
                                    class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-amber-400 flex-shrink-0 mt-1">
                                    <i class="fas fa-landmark text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] text-slate-400 uppercase tracking-wider font-bold">اسم المركز</p>
                                    <p class="font-bold text-sm">{{ $student->center_name }}</p>
                                </div>
                            </div>

                            <div class="flex gap-4 items-start">
                                <div
                                    class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-amber-400 flex-shrink-0 mt-1">
                                    <i class="fas fa-kaaba text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] text-slate-400 uppercase tracking-wider font-bold">المسجد</p>
                                    <p class="font-bold text-sm">{{ $student->mosque_name }}</p>
                                </div>
                            </div>

                            <div class="flex gap-4 items-start">
                                <div
                                    class="w-8 h-8 rounded-lg bg-white/10 flex items-center justify-center text-amber-400 flex-shrink-0 mt-1">
                                    <i class="fas fa-map-pin text-xs"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] text-slate-400 uppercase tracking-wider font-bold">العنوان</p>
                                    <p class="font-bold text-sm leading-relaxed text-slate-300">
                                        {{ $student->mosque_address }}</p>
                                </div>
                            </div>
                        </div>
                    </section>
                    <section
                        class="bg-gradient-to-br from-emerald-600 to-teal-700 rounded-[2.5rem] p-8 text-white shadow-lg relative overflow-hidden animate__animated animate__fadeInRight"
                        style="animation-delay: 0.2s">
                        <div
                            class="absolute top-0 left-0 w-24 h-24 bg-white/10 rounded-full -translate-x-1/2 -translate-y-1/2 blur-2xl">
                        </div>

                        <div class="relative z-10 text-center">
                            <div
                                class="w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-4 backdrop-blur-sm">
                                <i class="fas fa-hands-praying text-2xl text-emerald-100"></i>
                            </div>

                            <h4 class="text-lg font-bold mb-3 underline decoration-emerald-400/50 underline-offset-8">دعاء
                            </h4>

                            <p class="text-sm leading-relaxed font-medium italic text-emerald-50">
                                "اللهم اجعل القرآن العظيم ربيع قلوبنا، ونور صدورنا، وجلاء أحزاننا، وذهاب همنا، وارزقنا
                                تلاوته
                                آناء
                                الليل وأطراف النهار على الوجه الذي يرضيك عنا."
                            </p>

                            <div class="mt-4 flex justify-center gap-1">
                                <span class="w-1 h-1 rounded-full bg-emerald-300"></span>
                                <span class="w-8 h-1 rounded-full bg-emerald-300/50"></span>
                                <span class="w-1 h-1 rounded-full bg-emerald-300"></span>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

        </main>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Cairo', 'sans-serif'],
                    }
                }
            }
        }
    </script>
@endpush
