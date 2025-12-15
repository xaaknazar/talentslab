<?php

namespace App\Console\Commands;

use App\Models\Candidate;
use Illuminate\Console\Command;

class UpdateCandidateNames extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'candidates:update-names {--dry-run : Показать изменения без сохранения}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновить ФИО кандидатов: убрать отчество и изменить порядок на "Имя Фамилия"';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('Режим dry-run: изменения не будут сохранены');
        }

        $candidates = Candidate::all();
        $updated = 0;
        $skipped = 0;

        $this->info("Найдено кандидатов: " . $candidates->count());

        foreach ($candidates as $candidate) {
            $oldName = $candidate->full_name;

            if (empty($oldName)) {
                $this->warn("Пропущен кандидат ID {$candidate->id}: пустое имя");
                $skipped++;
                continue;
            }

            // Разделяем ФИО на части
            $nameParts = explode(' ', trim($oldName));

            // Если уже в правильном формате (2 слова), пропускаем
            if (count($nameParts) == 2) {
                $this->info("Пропущен кандидат ID {$candidate->id}: уже в формате 'Имя Фамилия' - {$oldName}");
                $skipped++;
                continue;
            }

            // Если 3 слова - это "Фамилия Имя Отчество"
            if (count($nameParts) >= 3) {
                $surname = $nameParts[0];  // Фамилия
                $name = $nameParts[1];     // Имя
                // Отчество игнорируем

                $newName = trim($name . ' ' . $surname);  // "Имя Фамилия"

                $this->line("ID {$candidate->id}: '{$oldName}' → '{$newName}'");

                if (!$dryRun) {
                    $candidate->full_name = $newName;
                    $candidate->save();
                }

                $updated++;
            }
            // Если 1 слово - просто оставляем как есть
            elseif (count($nameParts) == 1) {
                $this->warn("Пропущен кандидат ID {$candidate->id}: только одно слово - {$oldName}");
                $skipped++;
            }
        }

        $this->newLine();
        $this->info("Обработано: {$candidates->count()}");
        $this->info("Обновлено: {$updated}");
        $this->info("Пропущено: {$skipped}");

        if ($dryRun && $updated > 0) {
            $this->newLine();
            $this->warn("Это был dry-run режим. Для применения изменений запустите без флага --dry-run:");
            $this->line("php artisan candidates:update-names");
        }

        return Command::SUCCESS;
    }
}
