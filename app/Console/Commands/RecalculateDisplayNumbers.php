<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RecalculateDisplayNumbers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'candidates:recalculate-numbers {--dry-run : Показать изменения без сохранения}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Пересчитать display_number для всех кандидатов последовательно по дате создания';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('Режим dry-run: изменения не будут сохранены');
        }

        // Получаем всех кандидатов в порядке создания
        $candidates = DB::table('candidates')->orderBy('created_at')->get();

        $this->info("Найдено кандидатов: " . $candidates->count());

        $number = 1;
        $updated = 0;

        foreach ($candidates as $candidate) {
            $oldNumber = $candidate->display_number;

            if ($oldNumber != $number) {
                $this->line("ID {$candidate->id}: display_number {$oldNumber} → {$number}");

                if (!$dryRun) {
                    DB::table('candidates')
                        ->where('id', $candidate->id)
                        ->update(['display_number' => $number]);
                }

                $updated++;
            }

            $number++;
        }

        $this->newLine();
        $this->info("Всего кандидатов: " . $candidates->count());
        $this->info("Обновлено: {$updated}");

        if ($dryRun && $updated > 0) {
            $this->newLine();
            $this->warn("Это был dry-run режим. Для применения изменений запустите без флага --dry-run:");
            $this->line("php artisan candidates:recalculate-numbers");
        }

        return Command::SUCCESS;
    }
}
