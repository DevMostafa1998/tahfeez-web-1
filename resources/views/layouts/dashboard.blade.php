@extends('layouts.app')

@section('title', 'الرئيسية')

@section('content')
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0">لوحة التحكم</h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="#">الرئيسية</a></li>
                        <li class="breadcrumb-item active" aria-current="page">لوحة التحكم</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="app-content">
        <div class="container-fluid">
            <div class="row">
                {{-- الكارت الأول: عدد الطلاب/الحفاظ --}}
                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-primary">
                        <div class="inner">
                            <h3>{{ $students_count ?? 0 }}</h3>
                            <p>{{ $isAdmin ? 'إجمالي الطلاب' : 'طلابي' }}</p>
                        </div>
                        <i class="small-box-icon bi bi-people-fill"></i>
                        <a href="{{ route('student.index') }}"
                            class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                            التفاصيل <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                </div>

                {{-- الكارت الثاني: نسبة الحفظ --}}
                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-success">
                        <div class="inner">
                            <h3>{{ $memorization_percentage ?? 0 }}<sup class="fs-5">%</sup></h3>
                            <p>نسبة الحفظ</p>
                        </div>
                        <i class="small-box-icon bi bi-book-half"></i>
                        <a href="{{ route('student.index', ['filter' => 'not_memorized_today']) }}"
                            class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                            معرفة الغائبين عن التسميع <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                </div>

                {{-- الكارت الثالث: عدد المجموعات --}}
                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-warning">
                        <div class="inner">
                            <h3>{{ $groups_count ?? 0 }}</h3>
                            <p>{{ $isAdmin ? 'إجمالي المجموعات' : 'مجموعاتي' }}</p>
                        </div>
                        <i class="small-box-icon bi bi-layers-fill"></i>
                        <a href="{{ route('group.index') }}"
                            class="small-box-footer link-dark link-underline-opacity-0 link-underline-opacity-50-hover">
                            التفاصيل <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                </div>

                {{-- الكارت الرابع: الحضور والغياب --}}
                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-danger">
                        <div class="inner">
                            <div class="row text-center">
                                <div class="col-6">
                                    <h3 class="mb-4">{{ $present_count ?? 0 }}</h3>
                                    <small>حضور</small>
                                </div>
                                <div class="col-6 border-start border-white border-opacity-25">
                                    <h3 class="mb-4">{{ $absent_count ?? 0 }}</h3>
                                    <small>غياب</small>
                                </div>
                            </div>
                        </div>
                        <i class="small-box-icon bi bi-calendar-check-fill" style="opacity: 0.3;"></i>
                        <a href="{{ route('attendance.index') }}"
                            class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                            سجل الحضور اليومي <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-header border-0">
                            <h3 class="card-title text-bold">توزيع الطلاب حسب الأعمار</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="ageDistributionChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card shadow-sm">
                        <div class="card-header border-0">
                            <h3 class="card-title text-bold">عدد الطلاب في كل مجموعة</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="groupDistributionChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="card shadow-sm border-top-success">
                        <div class="card-header border-0">
                            <h3 class="card-title text-bold">توزيع المحفظين حسب التصنيف</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="userCategoryChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- 1. دالة توليد ألوان عشوائية احترافية للمجموعات ---
            function generateSmartColors(count) {
                const colors = [];
                const hueStep = 360 / count;
                for (let i = 0; i < count; i++) {
                    const hue = i * hueStep;
                    // استخدام HSL لضمان ألوان زاهية (Saturation 70%) ومريحة للعين (Lightness 60%)
                    colors.push(`hsl(${hue}, 70%, 60%)`);
                }
                return colors.sort(() => Math.random() - 0.5); // خلط الألوان
            }

            // --- 2. إعدادات مخطط توزيع الأعمار (تصميم عصري متدرج) ---
            const ctxAge = document.getElementById('ageDistributionChart').getContext('2d');

            // إنشاء تدرج لوني (Gradient) للأعمدة
            const ageGradient = ctxAge.createLinearGradient(0, 0, 0, 400);
            ageGradient.addColorStop(0, 'rgba(13, 110, 253, 0.85)'); // أزرق براند
            ageGradient.addColorStop(1, 'rgba(13, 110, 253, 0.05)'); // تلاشي شفاف

            new Chart(ctxAge, {
                type: 'bar',
                data: {
                    labels: {!! json_encode($age_labels) !!},
                    datasets: [{
                        label: 'عدد الطلاب',
                        data: {!! json_encode($age_counts) !!},
                        backgroundColor: ageGradient,
                        borderColor: '#0d6efd',
                        borderWidth: 2,
                        borderRadius: 12, // حواف مستديرة عصرية
                        borderSkipped: false,
                        hoverBackgroundColor: '#0d6efd',
                        barPercentage: 0.5, // أعمدة أنيقة ونحيفة
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            rtl: true,
                            backgroundColor: '#1e293b',
                            padding: 12,
                            cornerRadius: 8,
                            titleAlign: 'right',
                            bodyAlign: 'right'
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.03)',
                                drawBorder: false
                            },
                            ticks: {
                                stepSize: 1,
                                font: {
                                    size: 12
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 12
                                }
                            }
                        }
                    },
                    animation: {
                        duration: 1500,
                        easing: 'easeOutQuart'
                    }
                }
            });

            // --- 3. إعدادات مخطط المجموعات (ألوان عشوائية فريدة) ---
            const groupLabels = {!! json_encode($group_labels) !!};
            const groupData = {!! json_encode($group_students_counts) !!};
            const ctxGroup = document.getElementById('groupDistributionChart').getContext('2d');

            new Chart(ctxGroup, {
                type: 'doughnut',
                data: {
                    labels: groupLabels,
                    datasets: [{
                        data: groupData,
                        backgroundColor: generateSmartColors(groupLabels.length),
                        borderColor: '#ffffff',
                        borderWidth: 3,
                        hoverOffset: 15 // تأثير بروز عند التمرير
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%', // جعل الدائرة أنحف وأجمل
                    plugins: {
                        legend: {
                            position: 'bottom',
                            rtl: true,
                            labels: {
                                padding: 20,
                                usePointStyle: true, // نقاط دائرية بدل المربعات في الليبل
                                font: {
                                    size: 12,
                                    family: 'sans-serif'
                                }
                            }
                        },
                        tooltip: {
                            rtl: true,
                            backgroundColor: '#1e293b',
                            padding: 10
                        }
                    },
                    animation: {
                        animateScale: true,
                        animateRotate: true
                    }
                }
            });
        });
        const ctxUserCat = document.getElementById('userCategoryChart').getContext('2d');

        // 1. إنشاء تدرج لوني أخضر عصري (Modern Green Gradient)
        const gradientBg = ctxUserCat.createLinearGradient(0, 0, 0, 350);
        gradientBg.addColorStop(0, 'rgba(0, 210, 0, 0.8)'); // أخضر زاهي (حسب طلبك)
        gradientBg.addColorStop(1, 'rgba(0, 210, 0, 0.05)'); // تلاشي خفيف جداً للأخضر

        new Chart(ctxUserCat, {
            type: 'bar',
            data: {
                labels: {!! json_encode($user_cat_labels) !!},
                datasets: [{
                    label: 'عدد المحفظين',
                    data: {!! json_encode($user_cat_counts) !!},
                    backgroundColor: gradientBg,
                    borderColor: '#00d200', // لون الحدود أخضر صريح
                    borderWidth: 2,
                    borderRadius: 50, // حواف دائرية بالكامل لمظهر عصري
                    borderSkipped: false,
                    hoverBackgroundColor: '#00ff00', // توهج عند التمرير
                    barPercentage: 0.4, // أعمدة نحيفة وأنيقة
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(20, 30, 45, 0.9)',
                        padding: 12,
                        rtl: true,
                        cornerRadius: 12,
                        displayColors: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        position: 'right',
                        grid: {
                            color: 'rgba(0, 0, 0, 0.04)',
                            drawBorder: false
                        },
                        ticks: {
                            color: '#64748b',
                            stepSize: 1
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#1e293b',
                            font: {
                                size: 13,
                                weight: 'bold'
                            }
                        }
                    }
                },
                animation: {
                    duration: 1800,
                    easing: 'easeOutQuart'
                }
            }
        });
    </script>
@endpush
