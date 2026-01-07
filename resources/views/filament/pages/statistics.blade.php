<x-filament-panels::page>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            transition: all 0.2s;
        }
        .stat-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1f2937;
            line-height: 1;
        }
        .stat-label {
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 4px;
        }
        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .chart-container {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .chart-title {
            font-size: 1.125rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 16px;
        }
        .progress-bar {
            height: 8px;
            background: #e5e7eb;
            border-radius: 4px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.5s ease;
        }
        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
        }
        .period-btn {
            padding: 8px 16px;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 500;
            border: 1px solid #e5e7eb;
            background: white;
            color: #374151;
            cursor: pointer;
            transition: all 0.2s;
        }
        .period-btn:hover {
            background: #f3f4f6;
        }
        .period-btn.active {
            background: #f59e0b;
            color: white;
            border-color: #f59e0b;
        }
        .list-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 12px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .list-item:last-child {
            border-bottom: none;
        }
    </style>

    <!-- Фильтр по периоду -->
    <div class="mb-6">
        <div class="flex flex-wrap gap-2">
            <button wire:click="$set('period', '1')" class="period-btn {{ $period === '1' ? 'active' : '' }}">
                Сегодня
            </button>
            <button wire:click="$set('period', '7')" class="period-btn {{ $period === '7' ? 'active' : '' }}">
                7 дней
            </button>
            <button wire:click="$set('period', '30')" class="period-btn {{ $period === '30' ? 'active' : '' }}">
                30 дней
            </button>
            <button wire:click="$set('period', '90')" class="period-btn {{ $period === '90' ? 'active' : '' }}">
                90 дней
            </button>
            <button wire:click="$set('period', '365')" class="period-btn {{ $period === '365' ? 'active' : '' }}">
                Год
            </button>
            <button wire:click="$set('period', 'all')" class="period-btn {{ $period === 'all' ? 'active' : '' }}">
                Все время
            </button>
        </div>
    </div>

    <!-- Основные метрики -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <div class="stat-number">{{ $this->getTotalCandidates() }}</div>
                    <div class="stat-label">Всего анкет за период</div>
                </div>
                <div class="stat-icon bg-blue-100">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <div class="stat-number">{{ $this->getCompletedToday() }}</div>
                    <div class="stat-label">Сегодня</div>
                </div>
                <div class="stat-icon bg-green-100">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <div class="stat-number">{{ $this->getCompletedThisWeek() }}</div>
                    <div class="stat-label">На этой неделе</div>
                </div>
                <div class="stat-icon bg-purple-100">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="stat-card">
            <div class="flex items-center justify-between">
                <div>
                    <div class="stat-number">{{ $this->getCompletedThisMonth() }}</div>
                    <div class="stat-label">В этом месяце</div>
                </div>
                <div class="stat-icon bg-amber-100">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Статистика по статусу и График динамики -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
        <!-- Статус анкет -->
        <div class="chart-container">
            <h3 class="chart-title">Статус анкет</h3>
            @php $statusStats = $this->getStatusStats(); @endphp

            <div class="space-y-4">
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700">Полные (с Gallup)</span>
                        <span class="text-sm font-bold text-green-600">{{ $statusStats['full']['count'] }} ({{ $statusStats['full']['percent'] }}%)</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill bg-green-500" style="width: {{ $statusStats['full']['percent'] }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700">Без Gallup</span>
                        <span class="text-sm font-bold text-amber-600">{{ $statusStats['without_gallup']['count'] }} ({{ $statusStats['without_gallup']['percent'] }}%)</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill bg-amber-500" style="width: {{ $statusStats['without_gallup']['percent'] }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700">Неполные</span>
                        <span class="text-sm font-bold text-red-600">{{ $statusStats['incomplete']['count'] }} ({{ $statusStats['incomplete']['percent'] }}%)</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill bg-red-500" style="width: {{ $statusStats['incomplete']['percent'] }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Pie chart для статусов -->
            <div class="mt-6">
                <canvas id="statusChart" height="200"></canvas>
            </div>
        </div>

        <!-- График динамики -->
        <div class="chart-container lg:col-span-2">
            <h3 class="chart-title">Динамика заполнений</h3>
            <canvas id="timelineChart" height="120"></canvas>
        </div>
    </div>

    <!-- Распределение по полу и возрасту -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Пол -->
        <div class="chart-container">
            <h3 class="chart-title">Распределение по полу</h3>
            @php $genderStats = $this->getGenderStats(); @endphp

            <div class="flex items-center gap-8">
                <div class="flex-1">
                    <canvas id="genderChart" height="200"></canvas>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 rounded-full bg-blue-500"></div>
                        <div>
                            <div class="font-semibold text-gray-800">Мужчины</div>
                            <div class="text-sm text-gray-500">{{ $genderStats['male']['count'] }} ({{ $genderStats['male']['percent'] }}%)</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 rounded-full bg-pink-500"></div>
                        <div>
                            <div class="font-semibold text-gray-800">Женщины</div>
                            <div class="text-sm text-gray-500">{{ $genderStats['female']['count'] }} ({{ $genderStats['female']['percent'] }}%)</div>
                        </div>
                    </div>
                    @if($genderStats['unknown']['count'] > 0)
                    <div class="flex items-center gap-3">
                        <div class="w-4 h-4 rounded-full bg-gray-400"></div>
                        <div>
                            <div class="font-semibold text-gray-800">Не указано</div>
                            <div class="text-sm text-gray-500">{{ $genderStats['unknown']['count'] }} ({{ $genderStats['unknown']['percent'] }}%)</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Возраст -->
        <div class="chart-container">
            <h3 class="chart-title">Распределение по возрасту</h3>
            @php $ageStats = $this->getAgeStats(); @endphp

            <div class="space-y-3">
                @foreach($ageStats as $age)
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700">{{ $age['range'] }} лет</span>
                        <span class="text-sm font-bold text-gray-600">{{ $age['count'] }} ({{ $age['percent'] }}%)</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill bg-indigo-500" style="width: {{ $age['percent'] }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Города и Шаги -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Города -->
        <div class="chart-container">
            <h3 class="chart-title">Топ-15 городов</h3>
            @php $cityStats = $this->getCityStats(); @endphp

            <div class="max-h-96 overflow-y-auto">
                @forelse($cityStats as $index => $city)
                <div class="list-item">
                    <div class="flex items-center gap-3">
                        <span class="w-6 h-6 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-600">
                            {{ $index + 1 }}
                        </span>
                        <span class="font-medium text-gray-800">{{ $city['city'] }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-sm text-gray-500">{{ $city['percent'] }}%</span>
                        <span class="font-bold text-gray-800">{{ $city['count'] }}</span>
                    </div>
                </div>
                @empty
                <div class="text-gray-500 text-center py-4">Нет данных</div>
                @endforelse
            </div>
        </div>

        <!-- Шаги заполнения -->
        <div class="chart-container">
            <h3 class="chart-title">Распределение по шагам заполнения</h3>
            @php $stepStats = $this->getStepStats(); @endphp

            <div class="space-y-3">
                @foreach($stepStats as $step)
                <div>
                    <div class="flex justify-between mb-1">
                        <span class="text-sm font-medium text-gray-700">{{ $step['label'] }}</span>
                        <span class="text-sm font-bold text-gray-600">{{ $step['count'] }} ({{ $step['percent'] }}%)</span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: {{ $step['percent'] }}%; background: {{ $step['step'] >= 5 ? '#22c55e' : ($step['step'] >= 3 ? '#f59e0b' : '#ef4444') }};"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Образование и Языки -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Образование -->
        <div class="chart-container">
            <h3 class="chart-title">Уровень образования</h3>
            @php $eduStats = $this->getEducationStats(); @endphp

            <canvas id="educationChart" height="200"></canvas>
        </div>

        <!-- Языки -->
        <div class="chart-container">
            <h3 class="chart-title">Знание языков</h3>
            @php $langStats = $this->getLanguageStats(); @endphp

            <div class="space-y-2">
                @forelse($langStats as $lang)
                <div class="flex items-center justify-between py-2">
                    <span class="font-medium text-gray-700">{{ $lang['language'] }}</span>
                    <div class="flex items-center gap-4">
                        <div class="w-32 h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-teal-500 rounded-full" style="width: {{ $lang['percent'] }}%"></div>
                        </div>
                        <span class="text-sm font-bold text-gray-600 w-16 text-right">{{ $lang['count'] }}</span>
                    </div>
                </div>
                @empty
                <div class="text-gray-500 text-center py-4">Нет данных</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Религия -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="chart-container">
            <h3 class="chart-title">Вероисповедание</h3>
            @php $religionStats = $this->getReligionStats(); @endphp

            <div class="space-y-2">
                @forelse($religionStats as $religion)
                <div class="flex items-center justify-between py-2">
                    <span class="font-medium text-gray-700">{{ $religion['religion'] }}</span>
                    <div class="flex items-center gap-4">
                        <div class="w-32 h-2 bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-violet-500 rounded-full" style="width: {{ $religion['percent'] }}%"></div>
                        </div>
                        <span class="text-sm font-bold text-gray-600 w-16 text-right">{{ $religion['count'] }} ({{ $religion['percent'] }}%)</span>
                    </div>
                </div>
                @empty
                <div class="text-gray-500 text-center py-4">Нет данных</div>
                @endforelse
            </div>
        </div>

        <!-- Дополнительная статистика -->
        <div class="chart-container">
            <h3 class="chart-title">Дополнительно</h3>

            <div class="grid grid-cols-2 gap-4">
                <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-4">
                    <div class="text-2xl font-bold text-blue-700">{{ $this->getAverageCompletionTime() ?? '—' }}</div>
                    <div class="text-sm text-blue-600">Среднее время заполнения</div>
                </div>

                @php
                    $avgAge = \App\Models\Candidate::whereNotNull('birth_date')
                        ->selectRaw('AVG(TIMESTAMPDIFF(YEAR, birth_date, CURDATE())) as avg_age')
                        ->value('avg_age');
                @endphp
                <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-4">
                    <div class="text-2xl font-bold text-purple-700">{{ $avgAge ? round($avgAge) : '—' }}</div>
                    <div class="text-sm text-purple-600">Средний возраст</div>
                </div>

                @php
                    $avgExperience = \App\Models\Candidate::whereNotNull('total_experience_years')
                        ->avg('total_experience_years');
                @endphp
                <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-4">
                    <div class="text-2xl font-bold text-green-700">{{ $avgExperience ? round($avgExperience, 1) : '—' }} лет</div>
                    <div class="text-sm text-green-600">Средний стаж</div>
                </div>

                @php
                    $avgSalary = \App\Models\Candidate::whereNotNull('expected_salary_from')
                        ->where('expected_salary_from', '>', 0)
                        ->avg('expected_salary_from');
                @endphp
                <div class="bg-gradient-to-br from-amber-50 to-amber-100 rounded-xl p-4">
                    <div class="text-2xl font-bold text-amber-700">{{ $avgSalary ? number_format($avgSalary, 0, ',', ' ') : '—' }}</div>
                    <div class="text-sm text-amber-600">Средняя ожид. ЗП (от)</div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Статус анкет - Pie chart
            const statusCtx = document.getElementById('statusChart');
            if (statusCtx) {
                new Chart(statusCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Полные (с Gallup)', 'Без Gallup', 'Неполные'],
                        datasets: [{
                            data: [{{ $statusStats['full']['count'] }}, {{ $statusStats['without_gallup']['count'] }}, {{ $statusStats['incomplete']['count'] }}],
                            backgroundColor: ['#22c55e', '#f59e0b', '#ef4444'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        cutout: '60%'
                    }
                });
            }

            // Динамика - Line chart
            @php $timeline = $this->getTimelineData(); @endphp
            const timelineCtx = document.getElementById('timelineChart');
            if (timelineCtx) {
                new Chart(timelineCtx, {
                    type: 'line',
                    data: {
                        labels: {!! json_encode($timeline['labels']) !!},
                        datasets: [{
                            label: 'Анкеты',
                            data: {!! json_encode($timeline['values']) !!},
                            fill: true,
                            backgroundColor: 'rgba(245, 158, 11, 0.1)',
                            borderColor: '#f59e0b',
                            borderWidth: 2,
                            tension: 0.4,
                            pointRadius: 3,
                            pointBackgroundColor: '#f59e0b'
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            }

            // Пол - Doughnut chart
            const genderCtx = document.getElementById('genderChart');
            if (genderCtx) {
                new Chart(genderCtx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Мужчины', 'Женщины'{{ $genderStats['unknown']['count'] > 0 ? ", 'Не указано'" : '' }}],
                        datasets: [{
                            data: [{{ $genderStats['male']['count'] }}, {{ $genderStats['female']['count'] }}{{ $genderStats['unknown']['count'] > 0 ? ', ' . $genderStats['unknown']['count'] : '' }}],
                            backgroundColor: ['#3b82f6', '#ec4899'{{ $genderStats['unknown']['count'] > 0 ? ", '#9ca3af'" : '' }}],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        cutout: '65%'
                    }
                });
            }

            // Образование - Bar chart
            const educationCtx = document.getElementById('educationChart');
            if (educationCtx) {
                new Chart(educationCtx, {
                    type: 'bar',
                    data: {
                        labels: {!! json_encode(collect($eduStats)->pluck('degree')) !!},
                        datasets: [{
                            label: 'Кандидаты',
                            data: {!! json_encode(collect($eduStats)->pluck('count')) !!},
                            backgroundColor: ['#6366f1', '#8b5cf6', '#a855f7', '#c084fc', '#d8b4fe'],
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                display: false
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    stepSize: 1
                                }
                            }
                        }
                    }
                });
            }
        });

        // Обновление графиков при изменении периода
        document.addEventListener('livewire:navigated', function() {
            location.reload();
        });
    </script>
</x-filament-panels::page>
