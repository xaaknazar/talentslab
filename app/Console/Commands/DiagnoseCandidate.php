<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Candidate;

class DiagnoseCandidate extends Command
{
    protected $signature = 'candidate:diagnose {id}';
    protected $description = 'Диагностика всех данных кандидата';

    public function handle()
    {
        $id = $this->argument('id');
        $candidate = Candidate::find($id);

        if (!$candidate) {
            $this->error("Кандидат с ID {$id} не найден");
            return 1;
        }

        $this->info("╔═══════════════════════════════════════════════════════════════");
        $this->info("║ ДИАГНОСТИКА КАНДИДАТА ID: {$candidate->id}");
        $this->info("╚═══════════════════════════════════════════════════════════════");
        $this->line('');

        // Основная информация
        $this->info("📋 ОСНОВНАЯ ИНФОРМАЦИЯ");
        $this->line("ФИО: {$candidate->full_name}");
        $this->line("Email: {$candidate->email}");
        $this->line("Телефон: {$candidate->phone}");
        $this->line("Текущий шаг: {$candidate->step}");
        $this->line('');

        // Шаг 3: Образование и работа
        $this->info("💼 ОБРАЗОВАНИЕ И РАБОТА (Шаг 3)");
        $this->line('');

        $this->comment("Зарплата:");
        $this->line("  • Валюта: " . ($candidate->salary_currency ?: '❌ Не указано'));
        $this->line("  • От: " . ($candidate->expected_salary_from ?: '❌ Не указано'));
        $this->line("  • До: " . ($candidate->expected_salary_to ?: '❌ Не указано'));
        $this->line("  • Старое поле (expected_salary): " . ($candidate->expected_salary ?: '❌ Не указано'));
        $this->line('');

        $this->comment("Должность и сфера:");
        $this->line("  • Желаемая должность: " . ($candidate->desired_position ?: '❌ Не указано'));
        $this->line("  • Сфера деятельности: " . ($candidate->activity_sphere ?: '❌ Не указано'));
        $this->line('');

        $this->comment("Навыки и требования:");
        $this->line("  • Компьютерные навыки: " . ($candidate->computer_skills ?: '❌ Не указано'));
        $this->line("  • Пожелания на рабочем месте: " . ($candidate->employer_requirements ?: '❌ Не указано'));
        if ($candidate->employer_requirements) {
            $this->line("    Длина: " . strlen($candidate->employer_requirements) . " символов");
            $this->line("    Превью: " . substr($candidate->employer_requirements, 0, 100) . '...');
        }
        $this->line('');

        $this->comment("Опыт работы:");
        $this->line("  • Общий стаж: " . ($candidate->total_experience_years !== null ? $candidate->total_experience_years . ' лет' : '❌ Не указано'));
        $this->line("  • Удовлетворенность работой: " . ($candidate->job_satisfaction !== null ? $candidate->job_satisfaction . '/5' : '❌ Не указано'));

        if ($candidate->work_experience && is_array($candidate->work_experience)) {
            $this->line("  • Записей опыта работы: " . count($candidate->work_experience));
            foreach ($candidate->work_experience as $index => $exp) {
                $this->line("    [{$index}] " . ($exp['company'] ?? 'Нет компании') . " - " . ($exp['position'] ?? 'Нет должности'));
            }
        } else {
            $this->line("  • Опыт работы: ❌ Не указано");
        }
        $this->line('');

        $this->comment("Языковые навыки:");
        if ($candidate->language_skills && is_array($candidate->language_skills)) {
            $this->line("  • Количество языков: " . count($candidate->language_skills));
            foreach ($candidate->language_skills as $index => $skill) {
                $this->line("    [{$index}] " . ($skill['language'] ?? 'Нет языка') . " - " . ($skill['level'] ?? 'Нет уровня'));
            }
        } else {
            $this->line("  • Языковые навыки: ❌ Не указано");
        }
        $this->line('');

        // Шаг 2: Дополнительная информация
        $this->info("📝 ДОПОЛНИТЕЛЬНАЯ ИНФОРМАЦИЯ (Шаг 2)");
        $this->line("  • Хобби: " . ($candidate->hobbies ?: '❌ Не указано'));
        $this->line("  • Интересы: " . ($candidate->interests ?: '❌ Не указано'));

        if ($candidate->favorite_sports && is_array($candidate->favorite_sports)) {
            $this->line("  • Любимые виды спорта: " . implode(', ', $candidate->favorite_sports));
        } else {
            $this->line("  • Любимые виды спорта: ❌ Не указано");
        }
        $this->line('');

        // RAW данные из БД
        $this->info("🔍 RAW ДАННЫЕ ИЗ БАЗЫ ДАННЫХ");
        $raw = \DB::table('candidates')
            ->where('id', $id)
            ->select([
                'employer_requirements',
                'expected_salary_from',
                'expected_salary_to',
                'salary_currency',
                'computer_skills',
                'desired_position',
                'activity_sphere'
            ])
            ->first();

        if ($raw) {
            $this->line("employer_requirements: " . ($raw->employer_requirements ?: 'NULL'));
            $this->line("expected_salary_from: " . ($raw->expected_salary_from ?: 'NULL'));
            $this->line("expected_salary_to: " . ($raw->expected_salary_to ?: 'NULL'));
            $this->line("salary_currency: " . ($raw->salary_currency ?: 'NULL'));
            $this->line("computer_skills: " . ($raw->computer_skills ?: 'NULL'));
            $this->line("desired_position: " . ($raw->desired_position ?: 'NULL'));
            $this->line("activity_sphere: " . ($raw->activity_sphere ?: 'NULL'));
        }
        $this->line('');

        // Даты обновления
        $this->info("📅 ДАТЫ");
        $this->line("Создано: " . $candidate->created_at);
        $this->line("Обновлено: " . $candidate->updated_at);
        $this->line("Время с последнего обновления: " . $candidate->updated_at->diffForHumans());
        $this->line('');

        return 0;
    }
}
