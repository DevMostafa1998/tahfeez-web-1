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
                {{-- الكارت الأول: عدد الحفاظ --}}
                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-primary">
                        <div class="inner">
                            {{-- تم وضع المتغير هنا --}}
                            <h3>{{ $students_count ?? 0 }}</h3>
                            <p>عدد الحفاظ</p>
                        </div>
                        <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path
                                d="M2.25 2.25a.75.75 0 000 1.5h1.386c.17 0 .318.114.362.278l2.558 9.592a3.752 3.752 0 00-2.806 3.63c0 .414.336.75.75.75h15.75a.75.75 0 000-1.5H5.378A2.25 2.25 0 017.5 15h11.218a.75.75 0 00.674-.421 60.358 60.358 0 002.96-7.228.75.75 0 00-.525-.965A60.864 60.864 0 005.68 4.509l-.232-.867A1.875 1.875 0 003.636 2.25H2.25zM3.75 20.25a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0zM16.5 20.25a1.5 1.5 0 113 0 1.5 1.5 0 01-3 0z">
                            </path>
                        </svg>
                        <a href="#"
                            class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                            التفاصيل <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                </div>

                {{-- الكارت الثاني: نسبة الحفظ --}}
                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-success">
                        <div class="inner">
                            {{-- عرض النسبة التي حسبناها في الكنترولر --}}
                            <h3>{{ $memorization_percentage ?? 0 }}<sup class="fs-5">%</sup></h3>
                            <p>نسبة الحفظ</p>
                        </div>
                        <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path
                                d="M18.375 2.25c-1.035 0-1.875.84-1.875 1.875v15.75c0 1.035.84 1.875 1.875 1.875h.75c1.035 0 1.875-.84 1.875-1.875V4.125c0-1.036-.84-1.875-1.875-1.875h-.75zM9.75 8.625c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v11.25c0 1.035-.84 1.875-1.875 1.875h-.75a1.875 1.875 0 01-1.875-1.875V8.625zM3 13.125c0-1.036.84-1.875 1.875-1.875h.75c1.036 0 1.875.84 1.875 1.875v6.75c0 1.035-.84 1.875-1.875 1.875h-.75A1.875 1.875 0 013 19.875v-6.75z">
                            </path>
                        </svg>
                        {{-- الرابط الجديد مع الفلتر --}}
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
                            {{-- تم وضع المتغير هنا --}}
                            <h3>{{ $groups_count ?? 0 }}</h3>
                            <p>عدد المجموعات</p>
                        </div>
                        <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path
                                d="M6.25 6.375a4.125 4.125 0 118.25 0 4.125 4.125 0 01-8.25 0zM3.25 19.125a7.125 7.125 0 0114.25 0v.003l-.001.119a.75.75 0 01-.363.63 13.067 13.067 0 01-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 01-.364-.63l-.001-.122zM19.75 7.5a.75.75 0 00-1.5 0v2.25H16a.75.75 0 000 1.5h2.25v2.25a.75.75 0 001.5 0v-2.25H22a.75.75 0 000-1.5h-2.25V7.5z">
                            </path>
                        </svg>
                        <a href="#"
                            class="small-box-footer link-dark link-underline-opacity-0 link-underline-opacity-50-hover">
                            التفاصيل <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                </div>

                {{-- الكارت الرابع: عدد المحفظين --}}
                <div class="col-lg-3 col-6">
                    <div class="small-box text-bg-danger">
                        <div class="inner">
                            {{-- تم وضع المتغير هنا --}}
                            <h3>{{ $users_count ?? 0 }}</h3>
                            <p>عدد المحفظين</p>
                        </div>
                        <svg class="small-box-icon" fill="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path clip-rule="evenodd" fill-rule="evenodd"
                                d="M2.25 13.5a8.25 8.25 0 018.25-8.25.75.75 0 01.75.75v6.75H18a.75.75 0 01.75.75 8.25 8.25 0 01-16.5 0z">
                            </path>
                            <path clip-rule="evenodd" fill-rule="evenodd"
                                d="M12.75 3a.75.75 0 01.75-.75 8.25 8.25 0 018.25 8.25.75.75 0 01-.75.75h-7.5a.75.75 0 01-.75-.75V3z">
                            </path>
                        </svg>
                        <a href="#"
                            class="small-box-footer link-light link-underline-opacity-0 link-underline-opacity-50-hover">
                            التفاصيل <i class="bi bi-link-45deg"></i>
                        </a>
                    </div>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-header border-0">
                            <h3 class="card-title text-bold">توزيع الطلاب حسب الأعمار</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="ageDistributionChart" style="height: 300px;"></canvas>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card shadow-sm">
                        <div class="card-header border-0">
                            <h3 class="card-title text-bold">عدد الطلاب في كل مجموعة</h3>
                        </div>
                        <div class="card-body">
                            <canvas id="groupDistributionChart" style="height: 300px;"></canvas>
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
    </script>
@endpush
