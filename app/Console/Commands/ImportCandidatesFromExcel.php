<?php

namespace App\Console\Commands;

use App\Models\Candidate;
use App\Models\GardnerTestResult;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ImportCandidatesFromExcel extends Command
{
    protected $signature = 'candidates:import {file : Путь к Excel файлу} {--dry-run : Показать что будет импортировано без сохранения}';

    protected $description = 'Импортировать кандидатов из Excel файла';

    // Маппинг колонок Excel на поля модели
    private array $columnMapping = [
        'A' => 'display_number',
        'B' => 'full_name',
        'C' => 'email',
        'D' => 'phone',
        'E' => 'gender',
        'F' => 'marital_status',
        'G' => 'birth_date',
        'H' => 'birth_place',
        'I' => 'current_city',
        'J' => 'ready_to_relocate',
        'K' => 'instagram',
        'L' => 'religion',
        'M' => 'is_practicing',
        'N' => 'parents',
        'O' => 'siblings',
        'P' => 'children',
        'Q' => 'hobbies',
        'R' => 'interests',
        'S' => 'visited_countries',
        'T' => 'books_per_year',
        'U' => 'favorite_sports',
        'V' => 'entertainment_hours_weekly',
        'W' => 'educational_hours_weekly',
        'X' => 'social_media_hours_weekly',
        'Y' => 'has_driving_license',
        'Z' => 'school',
        'AA' => 'universities',
        'AB' => 'language_skills',
        'AC' => 'computer_skills',
        'AD' => 'work_experience',
        'AE' => 'total_experience_years',
        'AF' => 'job_satisfaction',
        'AG' => 'desired_positions',
        'AH' => 'activity_sphere',
        'AI' => 'awards',
        'AJ' => 'expected_salary',
        'AK' => 'employer_requirements',
        'AL' => 'mbti_type',
        // AM - дата создания (пропускаем)
        'AN' => 'gardner_linguistic',
        'AO' => 'gardner_logical_mathematical',
        'AP' => 'gardner_spatial',
        'AQ' => 'gardner_musical',
        'AR' => 'gardner_bodily_kinesthetic',
        'AS' => 'gardner_intrapersonal',
        'AT' => 'gardner_interpersonal',
        'AU' => 'gardner_naturalistic',
        'AV' => 'gardner_existential',
    ];

    public function handle()
    {
        $filePath = $this->argument('file');
        $dryRun = $this->option('dry-run');

        if (!file_exists($filePath)) {
            $this->error("Файл не найден: {$filePath}");
            return Command::FAILURE;
        }

        if ($dryRun) {
            $this->info('Режим dry-run: изменения не будут сохранены');
        }

        $this->info("Загрузка файла: {$filePath}");

        try {
            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $highestRow = $sheet->getHighestRow();

            $this->info("Найдено строк: " . ($highestRow - 1)); // минус заголовок

            $created = 0;
            $updated = 0;
            $skipped = 0;
            $errors = 0;

            $progressBar = $this->output->createProgressBar($highestRow - 1);
            $progressBar->start();

            for ($row = 2; $row <= $highestRow; $row++) {
                try {
                    $rowData = $this->parseRow($sheet, $row);

                    if (empty($rowData['email']) && empty($rowData['phone'])) {
                        $this->newLine();
                        $this->warn("Строка {$row}: пропущена (нет email и телефона)");
                        $skipped++;
                        $progressBar->advance();
                        continue;
                    }

                    // Ищем существующего кандидата по email или телефону
                    $candidate = null;
                    if (!empty($rowData['email'])) {
                        $candidate = Candidate::where('email', $rowData['email'])->first();
                    }
                    if (!$candidate && !empty($rowData['phone'])) {
                        $candidate = Candidate::where('phone', $rowData['phone'])->first();
                    }

                    // Извлекаем данные Gardner перед сохранением кандидата
                    $gardnerData = $this->extractGardnerData($rowData);
                    foreach (array_keys($gardnerData) as $key) {
                        unset($rowData['gardner_' . $key]);
                    }

                    if ($dryRun) {
                        if ($candidate) {
                            $this->newLine();
                            $this->line("Строка {$row}: будет обновлён - {$rowData['full_name']} ({$rowData['email']})");
                            $updated++;
                        } else {
                            $this->newLine();
                            $this->line("Строка {$row}: будет создан - {$rowData['full_name']} ({$rowData['email']})");
                            $created++;
                        }
                    } else {
                        if ($candidate) {
                            $candidate->update($rowData);
                            $updated++;
                        } else {
                            $candidate = Candidate::create($rowData);
                            $created++;
                        }

                        // Сохраняем результаты теста Гарднера
                        if (!empty(array_filter($gardnerData))) {
                            $this->saveGardnerResults($candidate, $gardnerData);
                        }
                    }
                } catch (\Exception $e) {
                    $this->newLine();
                    $this->error("Строка {$row}: ошибка - " . $e->getMessage());
                    $errors++;
                }

                $progressBar->advance();
            }

            $progressBar->finish();
            $this->newLine(2);

            $this->info("Результаты:");
            $this->line("  Создано: {$created}");
            $this->line("  Обновлено: {$updated}");
            $this->line("  Пропущено: {$skipped}");
            $this->line("  Ошибок: {$errors}");

            if ($dryRun && ($created > 0 || $updated > 0)) {
                $this->newLine();
                $this->warn("Это был dry-run режим. Для применения изменений запустите без флага --dry-run");
            }

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error("Ошибка чтения файла: " . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function parseRow($sheet, int $row): array
    {
        $data = [];

        foreach ($this->columnMapping as $col => $field) {
            $value = $sheet->getCell($col . $row)->getValue();

            // Преобразуем значения
            $data[$field] = $this->transformValue($field, $value);
        }

        // Обрабатываем сложные поля
        $data = $this->processComplexFields($data);

        return $data;
    }

    private function transformValue(string $field, $value)
    {
        if ($value === null || $value === '') {
            return null;
        }

        // Булевы поля
        if (in_array($field, ['ready_to_relocate', 'is_practicing', 'has_driving_license'])) {
            return in_array(mb_strtolower(trim($value)), ['да', 'yes', '1', 'true']);
        }

        // Числовые поля
        if (in_array($field, ['entertainment_hours_weekly', 'educational_hours_weekly', 'social_media_hours_weekly', 'total_experience_years', 'job_satisfaction'])) {
            return is_numeric($value) ? (int)$value : null;
        }

        // Дата рождения
        if ($field === 'birth_date') {
            return $this->parseDate($value);
        }

        return trim((string)$value);
    }

    private function parseDate($value): ?string
    {
        if (empty($value)) {
            return null;
        }

        // Если это число (Excel serial date)
        if (is_numeric($value)) {
            $date = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value);
            return $date->format('Y-m-d');
        }

        // Если это строка формата dd.mm.yyyy
        if (preg_match('/^(\d{2})\.(\d{2})\.(\d{4})$/', $value, $matches)) {
            return "{$matches[3]}-{$matches[2]}-{$matches[1]}";
        }

        // Пробуем распарсить как дату
        try {
            $date = new \DateTime($value);
            return $date->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    private function processComplexFields(array $data): array
    {
        // Удаляем временные поля для семьи (они хранятся в family_members)
        $parents = $data['parents'] ?? null;
        $siblings = $data['siblings'] ?? null;
        $children = $data['children'] ?? null;
        unset($data['parents'], $data['siblings'], $data['children']);

        // Формируем family_members если есть данные
        if ($parents || $siblings || $children) {
            $data['family_members'] = [
                'parents' => $this->parseFamilyMembers($parents, 'parent'),
                'siblings' => $this->parseFamilyMembers($siblings, 'sibling'),
                'children' => $this->parseFamilyMembers($children, 'child'),
            ];
        }

        // Парсим массивы из строк
        if (!empty($data['visited_countries'])) {
            $data['visited_countries'] = $this->parseArrayFromString($data['visited_countries']);
        }

        if (!empty($data['favorite_sports'])) {
            $data['favorite_sports'] = $this->parseArrayFromString($data['favorite_sports']);
        }

        if (!empty($data['desired_positions'])) {
            $data['desired_positions'] = $this->parseArrayFromString($data['desired_positions']);
        }

        // Парсим университеты
        if (!empty($data['universities'])) {
            $data['universities'] = $this->parseUniversities($data['universities']);
        }

        // Парсим языки
        if (!empty($data['language_skills'])) {
            $data['language_skills'] = $this->parseLanguages($data['language_skills']);
        }

        // Парсим опыт работы
        if (!empty($data['work_experience'])) {
            $data['work_experience'] = $this->parseWorkExperience($data['work_experience']);
        }

        // Парсим награды
        if (!empty($data['awards'])) {
            $data['awards'] = $this->parseAwards($data['awards']);
        }

        // Парсим зарплату
        if (!empty($data['expected_salary'])) {
            $salary = $this->parseSalary($data['expected_salary']);
            $data['expected_salary_from'] = $salary['from'];
            $data['expected_salary_to'] = $salary['to'];
            unset($data['expected_salary']);
        }

        // MBTI тип - извлекаем только код
        if (!empty($data['mbti_type'])) {
            $data['mbti_type'] = $this->parseMbtiType($data['mbti_type']);
        }

        // Удаляем display_number - он генерируется автоматически
        unset($data['display_number']);

        return $data;
    }

    private function parseArrayFromString($value): array
    {
        if (is_array($value)) {
            return $value;
        }

        return array_map('trim', explode(',', $value));
    }

    private function parseFamilyMembers($value, string $type): array
    {
        if (empty($value)) {
            return [];
        }

        $members = [];
        $lines = explode("\n", $value);

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Парсим формат: "Отец - 1965 г.р. - Инженер"
            if (preg_match('/^(\S+)\s*-\s*(\d{4})\s*г\.р\.(?:\s*-\s*(.+))?$/', $line, $matches)) {
                $member = [
                    'relation' => $matches[1],
                    'birth_year' => $matches[2],
                ];
                if (!empty($matches[3])) {
                    $member['profession'] = trim($matches[3]);
                }
                $members[] = $member;
            }
        }

        return $members;
    }

    private function parseUniversities($value): array
    {
        if (empty($value)) {
            return [];
        }

        $universities = [];
        $lines = explode("\n", $value);

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            $uni = ['name' => $line];

            // Пробуем извлечь факультет и степень
            if (preg_match('/^(.+?),\s*(.+?)\s*\((.+?)\)\s*(\d{4})-(\d{4})?$/', $line, $matches)) {
                $uni = [
                    'name' => trim($matches[1]),
                    'faculty' => trim($matches[2]),
                    'degree' => trim($matches[3]),
                    'year_start' => $matches[4],
                    'year_end' => $matches[5] ?? null,
                ];
            }

            $universities[] = $uni;
        }

        return $universities;
    }

    private function parseLanguages($value): array
    {
        if (empty($value)) {
            return [];
        }

        $languages = [];
        $lines = explode("\n", $value);

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            if (preg_match('/^(.+?)\s*-\s*(.+)$/', $line, $matches)) {
                $languages[] = [
                    'language' => trim($matches[1]),
                    'level' => trim($matches[2]),
                ];
            } else {
                $languages[] = ['language' => $line, 'level' => ''];
            }
        }

        return $languages;
    }

    private function parseWorkExperience($value): array
    {
        if (empty($value)) {
            return [];
        }

        $experiences = [];
        $lines = explode("\n", $value);

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Формат: "Компания - Должность (01.2020 - 12.2023)"
            if (preg_match('/^(.+?)\s*-\s*(.+?)\s*\((.+?)\s*-\s*(.+?)\)$/', $line, $matches)) {
                $experiences[] = [
                    'company' => trim($matches[1]),
                    'position' => trim($matches[2]),
                    'start_date' => trim($matches[3]),
                    'end_date' => trim($matches[4]),
                ];
            } else {
                $experiences[] = ['company' => $line, 'position' => ''];
            }
        }

        return $experiences;
    }

    private function parseAwards($value): array
    {
        if (empty($value)) {
            return [];
        }

        $awards = [];
        $lines = explode("\n", $value);

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            if (preg_match('/^(.+?)\s*\((\d{4})\)$/', $line, $matches)) {
                $awards[] = [
                    'title' => trim($matches[1]),
                    'year' => $matches[2],
                ];
            } else {
                $awards[] = ['title' => $line];
            }
        }

        return $awards;
    }

    private function parseSalary($value): array
    {
        // Убираем пробелы и символ валюты
        $value = preg_replace('/[^\d\-]/', '', str_replace(' ', '', $value));

        if (preg_match('/^(\d+)-(\d+)$/', $value, $matches)) {
            return ['from' => (float)$matches[1], 'to' => (float)$matches[2]];
        }

        if (is_numeric($value)) {
            return ['from' => (float)$value, 'to' => (float)$value];
        }

        return ['from' => null, 'to' => null];
    }

    private function parseMbtiType($value): ?string
    {
        // Извлекаем MBTI код (4 буквы)
        if (preg_match('/([IENFSTJP]{4})/', strtoupper($value), $matches)) {
            return $matches[1];
        }

        return null;
    }

    private function extractGardnerData(array $rowData): array
    {
        $types = [
            'linguistic', 'logical_mathematical', 'spatial', 'musical',
            'bodily_kinesthetic', 'intrapersonal', 'interpersonal',
            'naturalistic', 'existential'
        ];

        $data = [];
        foreach ($types as $type) {
            $key = 'gardner_' . $type;
            if (isset($rowData[$key]) && is_numeric($rowData[$key])) {
                $data[$type] = (int)$rowData[$key];
            }
        }

        return $data;
    }

    private function saveGardnerResults(Candidate $candidate, array $results): void
    {
        // Для сохранения результатов Гарднера нужен user_id
        // Если у кандидата нет user_id, создаём временного пользователя
        $userId = $candidate->user_id;

        if (!$userId && !empty($candidate->email)) {
            // Проверяем, есть ли пользователь с таким email
            $user = User::where('email', $candidate->email)->first();

            if (!$user) {
                // Создаём пользователя с временным паролем
                $user = User::create([
                    'name' => $candidate->full_name ?? 'Кандидат',
                    'email' => $candidate->email,
                    'password' => Hash::make(Str::random(16)),
                ]);
            }

            // Привязываем пользователя к кандидату
            $candidate->update(['user_id' => $user->id]);
            $userId = $user->id;
        }

        if (!$userId) {
            return; // Не можем сохранить без user_id
        }

        // Обновляем или создаём результаты теста Гарднера
        GardnerTestResult::updateOrCreate(
            ['user_id' => $userId],
            ['results' => $results, 'answers' => []]
        );
    }
}
