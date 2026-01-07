<?php

namespace App\Filament\Pages;

use App\Models\Candidate;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Statistics extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Статистика';
    protected static ?string $title = 'Статистика';
    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.pages.statistics';

    public string $period = '30';
    public ?string $dateFrom = null;
    public ?string $dateTo = null;

    public function mount(): void
    {
        $this->dateTo = now()->format('Y-m-d');
        $this->dateFrom = now()->subDays(30)->format('Y-m-d');
    }

    public function updatedPeriod($value): void
    {
        $this->dateTo = now()->format('Y-m-d');

        switch ($value) {
            case '1':
                $this->dateFrom = now()->format('Y-m-d');
                break;
            case '7':
                $this->dateFrom = now()->subDays(7)->format('Y-m-d');
                break;
            case '30':
                $this->dateFrom = now()->subDays(30)->format('Y-m-d');
                break;
            case '90':
                $this->dateFrom = now()->subDays(90)->format('Y-m-d');
                break;
            case '365':
                $this->dateFrom = now()->subDays(365)->format('Y-m-d');
                break;
            case 'all':
                $this->dateFrom = null;
                $this->dateTo = null;
                break;
        }
    }

    protected function getBaseQuery()
    {
        $query = Candidate::query();

        if ($this->dateFrom && $this->dateTo) {
            $query->whereBetween('created_at', [
                Carbon::parse($this->dateFrom)->startOfDay(),
                Carbon::parse($this->dateTo)->endOfDay()
            ]);
        }

        return $query;
    }

    public function getTotalCandidates(): int
    {
        return $this->getBaseQuery()->count();
    }

    public function getCompletedToday(): int
    {
        return Candidate::whereDate('created_at', today())->count();
    }

    public function getCompletedThisWeek(): int
    {
        return Candidate::whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ])->count();
    }

    public function getCompletedThisMonth(): int
    {
        return Candidate::whereBetween('created_at', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ])->count();
    }

    public function getStatusStats(): array
    {
        $total = $this->getBaseQuery()->count();
        if ($total === 0) {
            return [
                'full' => ['count' => 0, 'percent' => 0],
                'without_gallup' => ['count' => 0, 'percent' => 0],
                'incomplete' => ['count' => 0, 'percent' => 0],
            ];
        }

        // Полные анкеты: step >= 5 И есть gallup_pdf
        $full = $this->getBaseQuery()
            ->where('step', '>=', 5)
            ->whereNotNull('gallup_pdf')
            ->where('gallup_pdf', '!=', '')
            ->count();

        // Без Gallup: step >= 5 НО нет gallup_pdf
        $withoutGallup = $this->getBaseQuery()
            ->where('step', '>=', 5)
            ->where(function ($q) {
                $q->whereNull('gallup_pdf')->orWhere('gallup_pdf', '');
            })
            ->count();

        // Неполные: step < 5
        $incomplete = $this->getBaseQuery()
            ->where('step', '<', 5)
            ->count();

        return [
            'full' => [
                'count' => $full,
                'percent' => round(($full / $total) * 100, 1)
            ],
            'without_gallup' => [
                'count' => $withoutGallup,
                'percent' => round(($withoutGallup / $total) * 100, 1)
            ],
            'incomplete' => [
                'count' => $incomplete,
                'percent' => round(($incomplete / $total) * 100, 1)
            ],
        ];
    }

    public function getGenderStats(): array
    {
        $total = $this->getBaseQuery()->count();
        if ($total === 0) {
            return [
                'male' => ['count' => 0, 'percent' => 0],
                'female' => ['count' => 0, 'percent' => 0],
                'unknown' => ['count' => 0, 'percent' => 0],
            ];
        }

        $male = $this->getBaseQuery()->where('gender', 'Мужской')->count();
        $female = $this->getBaseQuery()->where('gender', 'Женский')->count();
        $unknown = $total - $male - $female;

        return [
            'male' => [
                'count' => $male,
                'percent' => round(($male / $total) * 100, 1)
            ],
            'female' => [
                'count' => $female,
                'percent' => round(($female / $total) * 100, 1)
            ],
            'unknown' => [
                'count' => $unknown,
                'percent' => round(($unknown / $total) * 100, 1)
            ],
        ];
    }

    public function getCityStats(): array
    {
        return $this->getBaseQuery()
            ->select('current_city', DB::raw('count(*) as count'))
            ->whereNotNull('current_city')
            ->where('current_city', '!=', '')
            ->groupBy('current_city')
            ->orderByDesc('count')
            ->limit(15)
            ->get()
            ->map(function ($item) {
                $total = $this->getBaseQuery()->whereNotNull('current_city')->where('current_city', '!=', '')->count();
                return [
                    'city' => $item->current_city,
                    'count' => $item->count,
                    'percent' => $total > 0 ? round(($item->count / $total) * 100, 1) : 0,
                ];
            })
            ->toArray();
    }

    public function getTimelineData(): array
    {
        $days = $this->period === 'all' ? 365 : (int) $this->period;
        if ($days > 90) {
            // По месяцам
            return $this->getMonthlyData();
        }
        // По дням
        return $this->getDailyData($days);
    }

    protected function getDailyData(int $days): array
    {
        $startDate = now()->subDays($days);
        $endDate = now();

        $data = Candidate::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('count(*) as count')
        )
            ->whereBetween('created_at', [$startDate->startOfDay(), $endDate->endOfDay()])
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        $labels = [];
        $values = [];

        for ($i = $days; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = now()->subDays($i)->format('d.m');
            $values[] = $data[$date] ?? 0;
        }

        return [
            'labels' => $labels,
            'values' => $values,
            'type' => 'daily',
        ];
    }

    protected function getMonthlyData(): array
    {
        $data = Candidate::select(
            DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
            DB::raw('count(*) as count')
        )
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('count', 'month')
            ->toArray();

        $labels = [];
        $values = [];

        $months = [
            '01' => 'Янв', '02' => 'Фев', '03' => 'Мар', '04' => 'Апр',
            '05' => 'Май', '06' => 'Июн', '07' => 'Июл', '08' => 'Авг',
            '09' => 'Сен', '10' => 'Окт', '11' => 'Ноя', '12' => 'Дек'
        ];

        foreach ($data as $month => $count) {
            $parts = explode('-', $month);
            $labels[] = $months[$parts[1]] . ' ' . $parts[0];
            $values[] = $count;
        }

        return [
            'labels' => $labels,
            'values' => $values,
            'type' => 'monthly',
        ];
    }

    public function getAgeStats(): array
    {
        $candidates = $this->getBaseQuery()
            ->whereNotNull('birth_date')
            ->get();

        $ranges = [
            '18-24' => 0,
            '25-34' => 0,
            '35-44' => 0,
            '45-54' => 0,
            '55+' => 0,
        ];

        foreach ($candidates as $candidate) {
            $age = $candidate->birth_date->age;
            if ($age < 18) continue;
            if ($age <= 24) $ranges['18-24']++;
            elseif ($age <= 34) $ranges['25-34']++;
            elseif ($age <= 44) $ranges['35-44']++;
            elseif ($age <= 54) $ranges['45-54']++;
            else $ranges['55+']++;
        }

        $total = array_sum($ranges);

        return collect($ranges)->map(function ($count, $range) use ($total) {
            return [
                'range' => $range,
                'count' => $count,
                'percent' => $total > 0 ? round(($count / $total) * 100, 1) : 0,
            ];
        })->values()->toArray();
    }

    public function getStepStats(): array
    {
        $total = $this->getBaseQuery()->count();
        if ($total === 0) {
            return [];
        }

        return $this->getBaseQuery()
            ->select('step', DB::raw('count(*) as count'))
            ->groupBy('step')
            ->orderBy('step')
            ->get()
            ->map(function ($item) use ($total) {
                $stepLabels = [
                    1 => 'Шаг 1 - Основная информация',
                    2 => 'Шаг 2 - Дополнительная информация',
                    3 => 'Шаг 3 - Образование и работа',
                    4 => 'Шаг 4 - Тесты',
                    5 => 'Шаг 5 - Завершено',
                    6 => 'Шаг 6 - С Gallup',
                ];
                return [
                    'step' => $item->step,
                    'label' => $stepLabels[$item->step] ?? "Шаг {$item->step}",
                    'count' => $item->count,
                    'percent' => round(($item->count / $total) * 100, 1),
                ];
            })
            ->toArray();
    }

    public function getAverageCompletionTime(): ?string
    {
        $candidates = $this->getBaseQuery()
            ->where('step', '>=', 5)
            ->whereNotNull('updated_at')
            ->get();

        if ($candidates->isEmpty()) {
            return null;
        }

        $totalMinutes = 0;
        $count = 0;

        foreach ($candidates as $candidate) {
            $diff = $candidate->created_at->diffInMinutes($candidate->updated_at);
            if ($diff > 0 && $diff < 10080) { // Не более 7 дней
                $totalMinutes += $diff;
                $count++;
            }
        }

        if ($count === 0) return null;

        $avgMinutes = $totalMinutes / $count;

        if ($avgMinutes < 60) {
            return round($avgMinutes) . ' мин';
        } elseif ($avgMinutes < 1440) {
            return round($avgMinutes / 60, 1) . ' ч';
        } else {
            return round($avgMinutes / 1440, 1) . ' дн';
        }
    }

    public function getReligionStats(): array
    {
        $total = $this->getBaseQuery()
            ->whereNotNull('religion')
            ->where('religion', '!=', '')
            ->count();

        if ($total === 0) {
            return [];
        }

        return $this->getBaseQuery()
            ->select('religion', DB::raw('count(*) as count'))
            ->whereNotNull('religion')
            ->where('religion', '!=', '')
            ->groupBy('religion')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->map(function ($item) use ($total) {
                return [
                    'religion' => $item->religion,
                    'count' => $item->count,
                    'percent' => round(($item->count / $total) * 100, 1),
                ];
            })
            ->toArray();
    }

    public function getEducationStats(): array
    {
        $candidates = $this->getBaseQuery()
            ->whereNotNull('universities')
            ->get();

        $degrees = [
            'Средне-специальное' => 0,
            'Бакалавр' => 0,
            'Магистратура' => 0,
            'PhD' => 0,
            'Без образования' => 0,
        ];

        foreach ($candidates as $candidate) {
            $universities = $candidate->universities;
            if (empty($universities)) {
                $degrees['Без образования']++;
                continue;
            }

            $highestDegree = null;
            $degreeOrder = ['PhD' => 4, 'Магистратура' => 3, 'Бакалавр' => 2, 'Средне-специальное' => 1];

            foreach ($universities as $uni) {
                $degree = $uni['degree'] ?? null;
                if ($degree && isset($degreeOrder[$degree])) {
                    if (!$highestDegree || $degreeOrder[$degree] > $degreeOrder[$highestDegree]) {
                        $highestDegree = $degree;
                    }
                }
            }

            if ($highestDegree) {
                $degrees[$highestDegree]++;
            }
        }

        $total = array_sum($degrees);

        return collect($degrees)
            ->filter(fn($count) => $count > 0)
            ->map(function ($count, $degree) use ($total) {
                return [
                    'degree' => $degree,
                    'count' => $count,
                    'percent' => $total > 0 ? round(($count / $total) * 100, 1) : 0,
                ];
            })
            ->values()
            ->toArray();
    }

    public function getLanguageStats(): array
    {
        $languages = [];

        $candidates = $this->getBaseQuery()
            ->whereNotNull('language_skills')
            ->get();

        foreach ($candidates as $candidate) {
            $skills = $candidate->language_skills;
            if (!is_array($skills)) continue;

            foreach ($skills as $skill) {
                $lang = $skill['language'] ?? null;
                if ($lang) {
                    $languages[$lang] = ($languages[$lang] ?? 0) + 1;
                }
            }
        }

        arsort($languages);

        $total = array_sum($languages);

        return collect(array_slice($languages, 0, 10, true))
            ->map(function ($count, $lang) use ($total) {
                return [
                    'language' => $lang,
                    'count' => $count,
                    'percent' => $total > 0 ? round(($count / $total) * 100, 1) : 0,
                ];
            })
            ->values()
            ->toArray();
    }
}
