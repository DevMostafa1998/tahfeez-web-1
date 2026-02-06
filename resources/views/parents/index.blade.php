@extends('layouts.app')

@section('content')
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ملف المتابعة الشامل - بوابة ولي الأمر</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;600;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/css/father.css') }}">

    <style>
        /* تأثيرات إضافية مخصصة */
        .premium-card {
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .premium-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.08);
            border-color: rgba(245, 158, 11, 0.4);
            /* لون ذهبي خفيف للحافة */
        }

        .icon-box {
            transition: all 0.4s ease;
        }

        .group:hover .icon-box {
            transform: scale(1.1) rotate(5deg);
        }

        .progress-circle {
            transition: stroke-dashoffset 1.5s ease-in-out;
        }
    </style>

    <div class="bg-gradient-custom text-slate-800 min-h-screen">

        <header class="max-w-7xl mx-auto px-6 py-8 animate__animated animate__fadeIn">
            <div
                class="flex flex-col md:flex-row justify-between items-center gap-6 bg-white/60 backdrop-blur-xl p-6 rounded-[2.5rem] border border-white shadow-xl hover:shadow-amber-100/50 transition-all duration-500">
                <div class="flex items-center gap-5">
                    <div class="relative group">
                        <img src="https://ui-avatars.com/api/?name=A+A&background=fbbf24&color=fff&size=200"
                            class="w-20 h-20 rounded-2xl shadow-lg group-hover:rotate-12 transition-transform duration-500">
                        <div class="absolute -bottom-2 -right-2 bg-green-500 w-6 h-6 rounded-full border-4 border-white">
                        </div>
                    </div>
                    <div>
                        <h1 class="text-2xl font-black text-slate-900 leading-tight">أدهم أحمد سعيد أدهم</h1>
                        <div class="flex flex-wrap gap-2 mt-1">
                            <p class="text-amber-600 font-bold flex items-center gap-2 text-sm">
                                <i class="fas fa-star text-[10px] animate-pulse"></i> طالب متميز في مركز يافا
                            </p>
                            <span class="text-slate-300">|</span>
                            <p class="text-indigo-600 font-bold flex items-center gap-2 text-sm">
                                <i class="fas fa-users text-[10px]"></i> مجموعة: أبو بكر الصديق
                            </p>
                        </div>
                    </div>
                </div>
                <div class="flex gap-4">
                    <div class="text-left md:text-right p-3 bg-white/40 rounded-2xl border border-white/50">
                        <p class="text-xs text-slate-400 font-bold uppercase tracking-widest">تاريخ التقرير</p>
                        <p class="font-bold text-slate-700">الخميس، 5 فبراير 2026</p>
                    </div>
                </div>
            </div>
        </header>

        <main class="w-full px-6 pb-20">
            <section
                class="mb-8 bg-white rounded-[3rem] p-8 shadow-xl shadow-slate-200/50 premium-card animate__animated animate__fadeInDown">
                <div class="flex items-center justify-between mb-8">
                    <div class="flex items-center gap-4">
                        <span class="w-1.5 h-8 bg-green-500 rounded-full animate-bounce"></span>
                        <h3 class="text-xl font-black text-slate-800">مؤشر الإنجاز القرآني</h3>
                    </div>
                    <div
                        class="bg-green-50 px-4 py-1 rounded-full border border-green-100 group hover:bg-green-500 hover:text-white transition-colors duration-300 cursor-default">
                        <span class="text-green-600 group-hover:text-white font-bold text-sm transition-colors">محفوظات
                            الطالب</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-12 gap-8 items-center">
                    <div class="md:col-span-3 relative flex items-center justify-center h-40 group">
                        <svg class="w-32 h-32 transform -rotate-90 overflow-visible">
                            <circle cx="64" cy="64" r="58" stroke="currentColor" stroke-width="12"
                                fill="transparent" class="text-slate-100" />
                            <circle cx="64" cy="64" r="58" stroke="currentColor" stroke-width="12"
                                fill="transparent" stroke-dasharray="364.4" stroke-dashoffset="218.6" stroke-linecap="round"
                                class="text-green-500 transition-all duration-[1.5s] ease-out group-hover:drop-shadow-[0_0_12px_rgba(34,197,94,0.6)] progress-circle" />
                        </svg>
                        <div
                            class="absolute flex flex-col items-center group-hover:scale-110 transition-transform duration-500">
                            <span class="text-3xl font-black text-slate-800">40%</span>
                            <span class="text-[10px] text-slate-400 font-bold uppercase">نسبة الحفظ</span>
                        </div>
                    </div>

                    <div class="md:col-span-5 space-y-6">
                        <div class="group">
                            <div class="flex justify-between mb-2">
                                <span class="text-sm font-bold text-slate-600">الأجزاء المنجزة</span>
                                <span class="text-sm font-black text-green-600">12 / 30 جزء</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden border border-slate-200">
                                <div class="bg-gradient-to-l from-green-400 to-green-600 h-full rounded-full transition-all duration-1000 group-hover:from-green-500 group-hover:to-emerald-700"
                                    style="width: 40%"></div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div
                                class="p-4 bg-slate-50 rounded-2xl border border-slate-100 hover:bg-white hover:shadow-md transition-all duration-300">
                                <p class="text-[10px] text-slate-400 font-bold mb-1">آخر سورة تم حفظها</p>
                                <p class="font-bold text-slate-700 text-sm">سورة الحشر</p>
                            </div>
                            <div
                                class="p-4 bg-amber-50 rounded-2xl border border-amber-100 relative overflow-hidden group transition-all hover:bg-amber-100 hover:shadow-md">
                                <i
                                    class="fas fa-list-ol absolute -left-1 -bottom-1 text-amber-200/40 text-4xl group-hover:scale-125 transition-transform"></i>
                                <div class="relative z-10 flex items-center gap-3">
                                    <div
                                        class="flex-shrink-0 w-10 h-10 border-2 border-amber-500 rounded-full flex items-center justify-center bg-white shadow-sm
                group-hover:!bg-amber-500 transition-all duration-300">
                                        <span
                                            class="text-amber-600 group-hover:!text-white font-black text-sm transition-colors duration-300">48</span>
                                    </div>
                                    <div>
                                        <p class="text-[9px] text-amber-600 font-bold mb-0.5 uppercase">رقم آخر آية</p>
                                        <p
                                            class="font-black text-slate-700 text-xs transition-colors group-hover:text-amber-700">
                                            سورة المائدة</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="md:col-span-4 h-full">
                        <div
                            class="p-5 bg-blue-50/50 rounded-[2rem] border-2 border-dashed border-blue-100 relative group transition-all hover:bg-white hover:border-solid hover:shadow-lg h-full">
                            <div class="flex items-start gap-4">
                                <div
                                    class="w-10 h-10 rounded-xl bg-blue-500 text-white flex items-center justify-center shadow-lg shadow-blue-200 group-hover:rotate-12 transition-transform">
                                    <i class="fas fa-comment-dots"></i>
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-center mb-1">
                                        <p class="text-[10px] text-blue-600 font-bold uppercase">ملاحظات آخر حفظ</p>
                                        <span
                                            class="text-[9px] bg-blue-100 text-blue-700 px-2 py-0.5 rounded-md font-bold group-hover:bg-blue-600 group-hover:text-white transition-colors">ملاحظة
                                            المعلم</span>
                                    </div>
                                    <p class="text-sm font-bold text-slate-700 leading-relaxed italic">
                                        "ما شاء الله، الحفظ متقن جداً في هذه الجلسة، نرجو التركيز أكثر على مخارج الحروف في
                                        سورة المائدة."
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
                <div class="lg:col-span-8 space-y-8">
                    <section
                        class="bg-white rounded-[3rem] p-8 shadow-xl shadow-slate-200/50 premium-card animate__animated animate__fadeInUp">
                        <div class="flex items-center gap-4 mb-8">
                            <span class="w-1.5 h-8 bg-amber-500 rounded-full"></span>
                            <h3 class="text-xl font-black text-slate-800">البيانات الشخصية للطالب</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-10">

                            <div
                                class="flex items-center gap-4 p-4 rounded-3xl bg-slate-50 hover:bg-amber-50 transition-colors group">
                                <div
                                    class="icon-box w-12 h-12 flex-shrink-0 bg-white rounded-2xl shadow-sm text-amber-500 flex items-center justify-center group-hover:!bg-amber-500 group-hover:!text-white transition-all duration-300">
                                    <i class="fas fa-fingerprint text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 font-bold">رقم الهوية</p>
                                    <p class="font-bold text-slate-700">452123215</p>
                                </div>
                            </div>

                            <div
                                class="flex items-center gap-4 p-4 rounded-3xl bg-slate-50 hover:bg-amber-50 transition-colors group">
                                <div
                                    class="icon-box w-12 h-12 flex-shrink-0 bg-white rounded-2xl shadow-sm text-amber-500 flex items-center justify-center group-hover:!bg-amber-500 group-hover:!text-white transition-all duration-300">
                                    <i class="fas fa-street-view text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 font-bold">حالة الإقامة</p>
                                    <p class="font-black text-slate-700">نازح</p>
                                </div>
                            </div>

                            <div
                                class="flex items-center gap-4 p-4 rounded-3xl bg-slate-50 hover:bg-amber-50 transition-colors group">
                                <div
                                    class="icon-box w-12 h-12 flex-shrink-0 bg-white rounded-2xl shadow-sm text-amber-500 flex items-center justify-center group-hover:!bg-amber-500 group-hover:!text-white transition-all duration-300">
                                    <i class="fas fa-calendar-alt text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 font-bold">تاريخ الميلاد</p>
                                    <p class="font-bold text-slate-700">15 مايو 2012</p>
                                </div>
                            </div>

                            <div
                                class="flex items-center gap-4 p-4 rounded-3xl bg-slate-50 hover:bg-amber-50 transition-colors group">
                                <div
                                    class="icon-box w-12 h-12 flex-shrink-0 bg-white rounded-2xl shadow-sm text-amber-500 flex items-center justify-center group-hover:!bg-amber-500 group-hover:!text-white transition-all duration-300">
                                    <i class="fas fa-hourglass-half text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 font-bold">العمر الحالي</p>
                                    <p class="font-bold text-slate-700">13 عاماً</p>
                                </div>
                            </div>

                            <div
                                class="flex items-center gap-4 p-4 rounded-3xl bg-slate-50 hover:bg-amber-50 transition-colors group">
                                <div
                                    class="icon-box w-12 h-12 flex-shrink-0 bg-white rounded-2xl shadow-sm text-amber-500 flex items-center justify-center group-hover:!bg-amber-500 group-hover:!text-white transition-all duration-300">
                                    <i class="fas fa-map-marked-alt text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 font-bold">مكان الميلاد الأصل</p>
                                    <p class="font-bold text-slate-700">غزة - حي الرمال</p>
                                </div>
                            </div>

                            <div
                                class="flex items-center gap-4 p-4 rounded-3xl bg-slate-50 hover:bg-amber-50 transition-colors group">
                                <div
                                    class="icon-box w-12 h-12 flex-shrink-0 bg-white rounded-2xl shadow-sm text-amber-500 flex items-center justify-center group-hover:!bg-amber-500 group-hover:!text-white transition-all duration-300">
                                    <i class="fas fa-house-user text-xl"></i>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-400 font-bold">مكان التواجد الحالي</p>
                                    <p class="font-bold text-slate-700 text-sm">دير البلح، مركز يافا</p>
                                </div>
                            </div>

                        </div>
                    </section>

                    <section
                        class="bg-white rounded-[3rem] p-8 shadow-xl shadow-slate-200/50 premium-card animate__animated animate__fadeInUp"
                        style="animation-delay: 0.1s;">
                        <div class="flex items-center justify-between mb-8">
                            <div class="flex items-center gap-4">
                                <span class="w-1.5 h-8 bg-indigo-500 rounded-full"></span>
                                <h3 class="text-xl font-black text-slate-800">الدورات التدريبية والقرآنية</h3>
                            </div>
                            <div class="bg-indigo-50 px-4 py-1 rounded-full border border-indigo-100">
                                <span class="text-indigo-600 font-bold text-sm">السجل الأكاديمي</span>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="w-full text-right border-separate border-spacing-y-3">
                                <thead>
                                    <tr class="text-slate-400 text-sm uppercase tracking-widest">
                                        <th class="pr-6 pb-4 font-bold">اسم الدورة</th>
                                        <th class="px-4 pb-4 font-bold text-center">الفئة</th>
                                        <th class="pl-6 pb-4 font-bold">تاريخ الإضافة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="bg-slate-50 hover:bg-indigo-50/50 transition-colors group">
                                        <td class="pr-6 py-4 rounded-r-2xl border-y border-r border-slate-100">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 flex-shrink-0 bg-white rounded-xl flex items-center justify-center text-indigo-500 shadow-sm
                            group-hover:!bg-indigo-500 group-hover:!text-white transition-all duration-300">
                                                    <i class="fas fa-certificate"></i>
                                                </div>
                                                <span class="font-bold text-slate-700">دورة أحكام التجويد المبتدئة</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 border-y border-slate-100">
                                            <span
                                                class="px-3 py-1 bg-white rounded-lg text-xs font-bold text-slate-500 border border-slate-100">الطلاب</span>
                                        </td>
                                        <td class="pl-6 py-4 border-y border-slate-100 text-slate-600 font-mono text-sm">
                                            2026-01-15
                                        </td>
                                    </tr>

                                    <tr class="bg-slate-50 hover:bg-indigo-50/50 transition-colors group">
                                        <td class="pr-6 py-4 rounded-r-2xl border-y border-r border-slate-100">
                                            <div class="flex items-center gap-3">
                                                <div
                                                    class="w-10 h-10 flex-shrink-0 bg-white rounded-xl flex items-center justify-center text-indigo-500 shadow-sm
                            group-hover:!bg-indigo-500 group-hover:!text-white transition-all duration-300">
                                                    <i class="fas fa-microphone-alt"></i>
                                                </div>
                                                <span class="font-bold text-slate-700">دورة الادغام</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-4 border-y border-slate-100">
                                            <span
                                                class="px-3 py-1 bg-white rounded-lg text-xs font-bold text-slate-500 border border-slate-100">الطلاب</span>
                                        </td>
                                        <td class="pl-6 py-4 border-y border-slate-100 text-slate-600 font-mono text-sm">
                                            2026-02-01
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <section
                            class="bg-white rounded-[3rem] p-8 shadow-xl shadow-slate-200/50 premium-card animate__animated animate__fadeInUp"
                            style="animation-delay: 0.2s;">
                            <h4 class="font-black mb-6 flex items-center gap-2 text-slate-700">
                                <i class="fas fa-phone-volume text-amber-500 animate-pulse"></i> أرقام التواصل للطالب
                            </h4>
                            <div class="space-y-4">
                                <div
                                    class="flex justify-between items-center p-4 border border-slate-100 rounded-2xl hover:bg-slate-50 transition-colors">
                                    <span class="text-sm font-bold text-slate-400">رقم الجوال</span>
                                    <span class="font-mono font-black text-slate-700">0599632154</span>
                                </div>
                                <div
                                    class="flex justify-between items-center p-4 bg-green-50 rounded-2xl border border-green-100 hover:shadow-md transition-all">
                                    <span class="text-sm font-bold text-green-600"> رقم الواتساب </span>
                                    <a href="#"
                                        class="font-mono font-black text-green-700 hover:underline">970599632154</a>
                                </div>
                            </div>
                        </section>

                        <section
                            class="bg-slate-900 rounded-[3rem] p-8 shadow-xl shadow-slate-900/20 premium-card text-white animate__animated animate__fadeInUp"
                            style="animation-delay: 0.3s;">
                            <h4 class="font-black mb-6 flex items-center gap-2">
                                <i class="fas fa-mosque text-amber-400"></i> بيانات المسجد والمركز
                            </h4>
                            <div class="space-y-4">
                                <div class="flex items-start gap-4 p-3 rounded-2xl hover:bg-white/10 transition-colors">
                                    <div class="text-amber-400 mt-1"><i class="fas fa-landmark"></i></div>
                                    <div>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase">اسم المركز المختص</p>
                                        <p class="font-bold">مركز يافا الثقافي والقرآني</p>
                                    </div>
                                </div>
                                <div class="flex items-start gap-4 p-3 rounded-2xl hover:bg-white/10 transition-colors">
                                    <div class="text-amber-400 mt-1"><i class="fas fa-kaaba"></i></div>
                                    <div>
                                        <p class="text-[10px] text-slate-400 font-bold uppercase">المسجد التابع له</p>
                                        <p class="font-bold">مسجد يافا الكبير</p>
                                    </div>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>

                <div class="lg:col-span-4 space-y-8">
                    <section
                        class="bg-white rounded-[3rem] p-8 shadow-xl shadow-slate-200/50 border-t-8 border-amber-500 premium-card animate__animated animate__fadeInRight">
                        <div class="text-center mb-8 group">
                            <div
                                class="inline-block p-1 rounded-full border-4 border-amber-100 mb-4 transition-transform group-hover:rotate-12 duration-500">
                                <img src="https://ui-avatars.com/api/?name=Mohammad+Yassin&background=slate-900&color=fff&size=150"
                                    class="w-24 h-24 rounded-full shadow-lg">
                            </div>
                            <h3 class="text-xl font-black text-slate-800">أ. محمد علي ياسين</h3>
                            <p class="text-sm text-slate-400 font-bold">المحفظ والمشرف المباشر</p>
                        </div>

                        <div class="space-y-4">
                            <div
                                class="p-4 bg-amber-50 rounded-[2rem] border border-amber-100 relative overflow-hidden group/item hover:bg-amber-100 transition-colors">
                                <i
                                    class="fas fa-graduation-cap absolute -left-2 -bottom-2 text-amber-200/40 text-5xl transition-transform group-hover/item:scale-125 group-hover/item:rotate-12"></i>
                                <div class="relative z-10">
                                    <p class="text-[10px] text-amber-600 font-bold uppercase mb-1">المؤهل العلمي</p>
                                    <p class="font-black text-slate-800">بكالوريوس أصول دين</p>
                                </div>
                            </div>

                            <div
                                class="p-4 bg-blue-50 rounded-[2rem] border border-blue-100 relative overflow-hidden group/item hover:bg-blue-100 transition-colors">
                                <i
                                    class="fas fa-book-open absolute -left-2 -bottom-2 text-blue-200/40 text-5xl transition-transform group-hover/item:scale-125 group-hover/item:-rotate-12"></i>
                                <div class="relative z-10">
                                    <p class="text-[10px] text-blue-600 font-bold uppercase mb-1">التخصص الدقيق</p>
                                    <p class="font-black text-slate-800">علوم القرآن والقراءات</p>
                                </div>
                            </div>

                            <div class="space-y-3 pt-4">
                                <a href="tel:0599887766"
                                    class="flex items-center gap-4 p-4 rounded-2xl bg-slate-900 text-white hover:bg-black transition-all hover:scale-105 active:scale-95 shadow-lg shadow-slate-200">
                                    <i class="fas fa-phone-alt animate-shake"></i>
                                    <div class="text-right">
                                        <p class="text-[10px] opacity-60 font-bold">اتصال هاتفي</p>
                                        <p class="text-sm font-mono">0599-887-766</p>
                                    </div>
                                </a>
                                <a href="https://wa.me/970599887766"
                                    class="flex items-center gap-4 p-4 rounded-2xl bg-green-500 text-white hover:bg-green-600 transition-all hover:scale-105 active:scale-95 shadow-lg shadow-green-100">
                                    <i class="fab fa-whatsapp text-xl"></i>
                                    <div class="text-right">
                                        <p class="text-[10px] opacity-80 font-bold">راسل المحفظ فوراً</p>
                                        <p class="text-sm font-mono">970599887766</p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </section>

                    <div
                        class="p-6 rounded-[2.5rem] bg-gradient-to-br from-amber-400 to-amber-600 text-white shadow-lg relative overflow-hidden group hover:shadow-amber-500/40 transition-shadow">
                        <i
                            class="fas fa-quote-right absolute top-4 left-4 opacity-20 text-4xl group-hover:scale-150 transition-transform duration-700"></i>
                        <p class="text-sm italic leading-relaxed relative z-10 font-bold">
                            "متابعتكم المستمرة لابنكم في المنزل هي الوقود الحقيقي لتقدمه في رحاب القرآن الكريم. نحن شركاء في
                            هذه الرحلة المباركة."
                        </p>
                    </div>

                    <section
                        class="bg-white rounded-[3rem] p-8 shadow-xl shadow-slate-200/50 premium-card animate__animated animate__fadeInUp relative overflow-hidden group"
                        style="animation-delay: 0.4s;">
                        <div
                            class="absolute -top-10 -right-10 w-32 h-32 bg-amber-50 rounded-full opacity-50 group-hover:scale-[2] transition-transform duration-1000">
                        </div>

                        <div class="relative z-10">
                            <div class="flex items-center gap-3 mb-6">
                                <div
                                    class="w-10 h-10 bg-gradient-to-br from-amber-400 to-amber-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-amber-200 group-hover:rotate-12 transition-transform">
                                    <i class="fas fa-hands-praying"></i>
                                </div>
                                <h4 class="font-black text-slate-800">دعاء</h4>
                            </div>

                            <div class="space-y-4">
                                <p
                                    class="text-slate-700 font-bold leading-relaxed text-lg italic group-hover:text-amber-700 transition-colors">
                                    "اللهمَّ اجعل القرآن الكريم ربيع قلوبنا، ونور صدورنا، وجلاء أحزاننا، اللهمَّ ذكّرنا منه
                                    ما نُسّينا، وعلّمنا منه ما جهلنا."
                                </p>

                                <div class="pt-4 border-t border-slate-50">
                                    <p class="text-xs text-slate-400 font-medium">
                                        <i class="far fa-lightbulb text-amber-500 ml-1"></i>
                                        نصيحة: احرص على ترديد هذا الدعاء بعد كل جلسة حفظ.
                                    </p>
                                </div>
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
        // كود بسيط لإضافة أنيميشن عند التمرير لمناطق معينة (Optional)
        document.querySelectorAll('.premium-card').forEach(card => {
            card.addEventListener('mouseenter', () => {
                // يمكنك إضافة أحداث JS إضافية هنا إذا أردت
            });
        });
    </script>
@endpush
