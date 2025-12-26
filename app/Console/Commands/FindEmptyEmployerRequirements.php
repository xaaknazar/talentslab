<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Candidate;

class FindEmptyEmployerRequirements extends Command
{
    protected $signature = 'candidate:find-empty-employer-requirements';
    protected $description = 'Найти кандидатов с пустым полем "Пожелания на рабочем месте"';

    public function handle()
    {
        $this->info('Поиск кандидатов с пустым полем "Пожелания на рабочем месте"...');
        $this->line('');

        // Находим кандидатов с пустым или NULL employer_requirements
        $candidates = Candidate::where(function($query) {
            $query->whereNull('employer_requirements')
                  ->orWhere('employer_requirements', '')
                  ->orWhere('employer_requirements', 'Не указано');
        })
        ->where('step', '>=', 3) // Только те, кто дошел до 3 шага
        ->get();

        $this->info("Найдено кандидатов с пустым полем: " . $candidates->count());
        $this->line('');

        if ($candidates->count() > 0) {
            $this->table(
                ['ID', 'Имя', 'Email', 'Телефон', 'Шаг'],
                $candidates->map(function($candidate) {
                    return [
                        $candidate->id,
                        $candidate->full_name,
                        $candidate->email,
                        $candidate->phone,
                        $candidate->step,
                    ];
                })->toArray()
            );

            $this->line('');
            $this->info('Эти пользователи должны:');
            $this->line('1. Зайти в форму редактирования: /candidate/form/{id}');
            $this->line('2. Заполнить поле "Пожелания на рабочем месте"');
            $this->line('3. Нажать "Далее" для сохранения');
            $this->line('');

            if ($this->confirm('Экспортировать список в CSV?', false)) {
                $filename = storage_path('app/empty_employer_requirements_' . date('Y-m-d_H-i-s') . '.csv');
                $file = fopen($filename, 'w');

                // Заголовки
                fputcsv($file, ['ID', 'ФИО', 'Email', 'Телефон', 'Текущий шаг', 'Ссылка на редактирование']);

                // Данные
                foreach ($candidates as $candidate) {
                    fputcsv($file, [
                        $candidate->id,
                        $candidate->full_name,
                        $candidate->email,
                        $candidate->phone,
                        $candidate->step,
                        url("/candidate/form/{$candidate->id}")
                    ]);
                }

                fclose($file);
                $this->info("Список экспортирован в: {$filename}");
            }
        } else {
            $this->info('Все кандидаты заполнили поле "Пожелания на рабочем месте"');
        }

        return 0;
    }
}
