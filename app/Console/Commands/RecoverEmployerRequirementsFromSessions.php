<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Candidate;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class RecoverEmployerRequirementsFromSessions extends Command
{
    protected $signature = 'candidate:recover-employer-requirements
                            {--dry-run : Показать что будет сделано без фактического сохранения}
                            {--force : Перезаписать существующие значения}';

    protected $description = 'Попытка восстановить employer_requirements из активных сессий Livewire';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $force = $this->option('force');

        $this->info('Поиск данных в активных сессиях...');
        $this->line('');

        $recovered = 0;
        $failed = 0;
        $skipped = 0;

        // Получаем все сессии в зависимости от драйвера
        $sessionDriver = config('session.driver');

        if ($sessionDriver === 'database') {
            $this->recoverFromDatabaseSessions($dryRun, $force, $recovered, $failed, $skipped);
        } elseif ($sessionDriver === 'file') {
            $this->recoverFromFileSessions($dryRun, $force, $recovered, $failed, $skipped);
        } else {
            $this->error("Драйвер сессий '{$sessionDriver}' не поддерживается этой командой.");
            $this->line("Поддерживаются: database, file");
            return 1;
        }

        $this->line('');
        $this->info("=== Итоги ===");
        $this->line("Восстановлено: {$recovered}");
        $this->line("Пропущено (уже заполнено): {$skipped}");
        $this->line("Не удалось восстановить: {$failed}");

        if ($dryRun) {
            $this->warn('');
            $this->warn('Это был пробный запуск (--dry-run)');
            $this->warn('Запустите без --dry-run для фактического сохранения');
        }

        return 0;
    }

    protected function recoverFromDatabaseSessions($dryRun, $force, &$recovered, &$failed, &$skipped)
    {
        $sessions = DB::table('sessions')->get();
        $this->info("Найдено сессий в БД: " . $sessions->count());
        $this->line('');

        $progressBar = $this->output->createProgressBar($sessions->count());
        $progressBar->start();

        foreach ($sessions as $session) {
            $progressBar->advance();

            try {
                $payload = base64_decode($session->payload);

                // Ищем данные Livewire в сессии
                $data = unserialize($payload);

                if (!is_array($data)) {
                    continue;
                }

                // Ищем компонент CandidateForm
                foreach ($data as $key => $value) {
                    if (strpos($key, 'livewire') !== false && is_array($value)) {
                        $this->processLivewireData($value, $dryRun, $force, $recovered, $failed, $skipped);
                    }
                }
            } catch (\Exception $e) {
                // Пропускаем поврежденные сессии
                continue;
            }
        }

        $progressBar->finish();
        $this->line('');
    }

    protected function recoverFromFileSessions($dryRun, $force, &$recovered, &$failed, &$skipped)
    {
        $sessionPath = storage_path('framework/sessions');

        if (!is_dir($sessionPath)) {
            $this->error("Директория сессий не найдена: {$sessionPath}");
            return;
        }

        $files = glob($sessionPath . '/*');
        $this->info("Найдено файлов сессий: " . count($files));
        $this->line('');

        $progressBar = $this->output->createProgressBar(count($files));
        $progressBar->start();

        foreach ($files as $file) {
            $progressBar->advance();

            try {
                $payload = file_get_contents($file);

                // Декодируем payload
                $data = unserialize($payload);

                if (!is_array($data)) {
                    continue;
                }

                // Ищем компонент CandidateForm
                foreach ($data as $key => $value) {
                    if (strpos($key, 'livewire') !== false && is_array($value)) {
                        $this->processLivewireData($value, $dryRun, $force, $recovered, $failed, $skipped);
                    }
                }
            } catch (\Exception $e) {
                // Пропускаем поврежденные сессии
                continue;
            }
        }

        $progressBar->finish();
        $this->line('');
    }

    protected function processLivewireData($data, $dryRun, $force, &$recovered, &$failed, &$skipped)
    {
        try {
            // Ищем employer_requirements и candidate_id в данных компонента
            if (!isset($data['data'])) {
                return;
            }

            $componentData = $data['data'];

            // Проверяем, есть ли employer_requirements и candidate
            if (!isset($componentData['employer_requirements']) || !isset($componentData['candidate'])) {
                return;
            }

            $employerRequirements = $componentData['employer_requirements'];

            // Пропускаем пустые значения
            if (empty($employerRequirements) || trim($employerRequirements) === '') {
                return;
            }

            // Получаем ID кандидата
            $candidateData = $componentData['candidate'];
            $candidateId = null;

            if (is_array($candidateData) && isset($candidateData['id'])) {
                $candidateId = $candidateData['id'];
            } elseif (is_object($candidateData) && isset($candidateData->id)) {
                $candidateId = $candidateData->id;
            }

            if (!$candidateId) {
                return;
            }

            // Находим кандидата в БД
            $candidate = Candidate::find($candidateId);

            if (!$candidate) {
                return;
            }

            // Проверяем, нужно ли обновлять
            if (!empty($candidate->employer_requirements) && !$force) {
                $skipped++;
                return;
            }

            // Выводим информацию
            $this->line('');
            $this->info("Найдено для кандидата ID {$candidateId}: {$candidate->full_name}");
            $this->line("Значение: " . substr($employerRequirements, 0, 100) . (strlen($employerRequirements) > 100 ? '...' : ''));

            if (!$dryRun) {
                // Сохраняем в БД
                $candidate->employer_requirements = $employerRequirements;
                $candidate->save();
                $this->line("✓ Сохранено");
            } else {
                $this->line("⚠ Будет сохранено (используйте без --dry-run)");
            }

            $recovered++;

        } catch (\Exception $e) {
            $failed++;
        }
    }
}
