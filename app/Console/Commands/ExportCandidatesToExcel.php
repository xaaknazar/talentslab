<?php

namespace App\Console\Commands;

use App\Models\Candidate;
use App\Models\GardnerTestResult;
use Illuminate\Console\Command;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExportCandidatesToExcel extends Command
{
    protected $signature = 'candidates:export {--output=candidates.xlsx : Имя выходного файла}';

    protected $description = 'Экспортировать всех кандидатов в Excel файл';

    public function handle()
    {
        $outputFile = $this->option('output');

        $this->info('Загрузка кандидатов...');
        $candidates = Candidate::orderBy('display_number')->get();

        $this->info("Найдено кандидатов: " . $candidates->count());

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Кандидаты');

        // Заголовки
        $headers = [
            'A' => '№',
            'B' => 'ФИО',
            'C' => 'Email',
            'D' => 'Телефон',
            'E' => 'Пол',
            'F' => 'Семейное положение',
            'G' => 'Дата рождения',
            'H' => 'Место рождения',
            'I' => 'Город проживания',
            'J' => 'Готов к релокации',
            'K' => 'Instagram',
            'L' => 'Религия',
            'M' => 'Практикующий',
            'N' => 'Родители',
            'O' => 'Братья/сёстры',
            'P' => 'Дети',
            'Q' => 'Хобби',
            'R' => 'Интересы',
            'S' => 'Посещённые страны',
            'T' => 'Книг в год',
            'U' => 'Любимые виды спорта',
            'V' => 'Часов развлечений/нед',
            'W' => 'Часов обучения/нед',
            'X' => 'Часов соцсетей/нед',
            'Y' => 'Водительские права',
            'Z' => 'Школа',
            'AA' => 'Университеты',
            'AB' => 'Языки',
            'AC' => 'Компьютерные навыки',
            'AD' => 'Опыт работы',
            'AE' => 'Общий стаж (лет)',
            'AF' => 'Удовлетворённость работой',
            'AG' => 'Желаемые позиции',
            'AH' => 'Сфера деятельности',
            'AI' => 'Награды',
            'AJ' => 'Ожидаемая зарплата',
            'AK' => 'Пожелания на рабочем месте',
            'AL' => 'MBTI тип',
            'AM' => 'Дата создания',
            // Тест Гарднера
            'AN' => 'Гарднер: Лингвистический',
            'AO' => 'Гарднер: Логико-математический',
            'AP' => 'Гарднер: Пространственный',
            'AQ' => 'Гарднер: Музыкальный',
            'AR' => 'Гарднер: Телесно-кинестетический',
            'AS' => 'Гарднер: Внутриличностный',
            'AT' => 'Гарднер: Межличностный',
            'AU' => 'Гарднер: Натуралистический',
            'AV' => 'Гарднер: Экзистенциальный',
        ];

        // Записываем заголовки
        foreach ($headers as $col => $title) {
            $sheet->setCellValue($col . '1', $title);
        }

        // Стиль заголовков
        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '4472C4']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ];
        $sheet->getStyle('A1:AV1')->applyFromArray($headerStyle);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Заполняем данные
        $row = 2;
        $progressBar = $this->output->createProgressBar($candidates->count());
        $progressBar->start();

        foreach ($candidates as $candidate) {
            $sheet->setCellValue('A' . $row, $candidate->display_number);
            $sheet->setCellValue('B' . $row, $candidate->full_name);
            $sheet->setCellValue('C' . $row, $candidate->email);
            $sheet->setCellValue('D' . $row, $candidate->phone);
            $sheet->setCellValue('E' . $row, $candidate->gender);
            $sheet->setCellValue('F' . $row, $candidate->marital_status);
            $sheet->setCellValue('G' . $row, $candidate->birth_date ? $candidate->birth_date->format('d.m.Y') : '');
            $sheet->setCellValue('H' . $row, $candidate->birth_place);
            $sheet->setCellValue('I' . $row, $candidate->current_city);
            $sheet->setCellValue('J' . $row, $candidate->ready_to_relocate ? 'Да' : 'Нет');
            $sheet->setCellValue('K' . $row, $candidate->instagram);
            $sheet->setCellValue('L' . $row, $candidate->religion);
            $sheet->setCellValue('M' . $row, $candidate->is_practicing ? 'Да' : 'Нет');

            // Родители
            $parents = $candidate->getFormattedParents();
            $sheet->setCellValue('N' . $row, implode("\n", $parents));

            // Братья/сёстры
            $siblings = $candidate->getFormattedSiblings();
            $sheet->setCellValue('O' . $row, implode("\n", $siblings));

            // Дети
            $children = $candidate->getFormattedChildren();
            $sheet->setCellValue('P' . $row, implode("\n", $children));

            $sheet->setCellValue('Q' . $row, $candidate->hobbies);
            $sheet->setCellValue('R' . $row, $candidate->interests);

            // Посещённые страны
            $countries = is_array($candidate->visited_countries) ? implode(', ', $candidate->visited_countries) : $candidate->visited_countries;
            $sheet->setCellValue('S' . $row, $countries);

            $sheet->setCellValue('T' . $row, $candidate->books_per_year);

            // Любимые виды спорта
            $sports = is_array($candidate->favorite_sports) ? implode(', ', $candidate->favorite_sports) : $candidate->favorite_sports;
            $sheet->setCellValue('U' . $row, $sports);

            $sheet->setCellValue('V' . $row, $candidate->entertainment_hours_weekly);
            $sheet->setCellValue('W' . $row, $candidate->educational_hours_weekly);
            $sheet->setCellValue('X' . $row, $candidate->social_media_hours_weekly);
            $sheet->setCellValue('Y' . $row, $candidate->has_driving_license ? 'Да' : 'Нет');
            $sheet->setCellValue('Z' . $row, $candidate->school);

            // Университеты
            $universities = $this->formatUniversities($candidate->universities);
            $sheet->setCellValue('AA' . $row, $universities);

            // Языки
            $languages = $this->formatLanguages($candidate->language_skills);
            $sheet->setCellValue('AB' . $row, $languages);

            $sheet->setCellValue('AC' . $row, $candidate->computer_skills);

            // Опыт работы
            $workExp = $this->formatWorkExperience($candidate->work_experience);
            $sheet->setCellValue('AD' . $row, $workExp);

            $sheet->setCellValue('AE' . $row, $candidate->total_experience_years);
            $sheet->setCellValue('AF' . $row, $candidate->job_satisfaction);

            // Желаемые позиции
            $positions = is_array($candidate->desired_positions) ? implode(', ', $candidate->desired_positions) : $candidate->desired_position;
            $sheet->setCellValue('AG' . $row, $positions);

            $sheet->setCellValue('AH' . $row, $candidate->activity_sphere);

            // Награды
            $awards = $this->formatAwards($candidate->awards);
            $sheet->setCellValue('AI' . $row, $awards);

            $sheet->setCellValue('AJ' . $row, $candidate->formatted_salary_range);
            $sheet->setCellValue('AK' . $row, $candidate->employer_requirements);
            $sheet->setCellValue('AL' . $row, $candidate->mbti_full_name);
            $sheet->setCellValue('AM' . $row, $candidate->created_at ? $candidate->created_at->format('d.m.Y H:i') : '');

            // Тест Гарднера - получаем через связь с User
            $gardnerResults = [];
            if ($candidate->user) {
                $gardnerResult = $candidate->user->gardnerTestResult;
                if ($gardnerResult) {
                    $gardnerResults = $gardnerResult->results ?? [];
                }
            }

            $sheet->setCellValue('AN' . $row, $gardnerResults['Лингвистический интеллект'] ?? '');
            $sheet->setCellValue('AO' . $row, $gardnerResults['Логико-математический интеллект'] ?? '');
            $sheet->setCellValue('AP' . $row, $gardnerResults['Пространственный интеллект'] ?? '');
            $sheet->setCellValue('AQ' . $row, $gardnerResults['Музыкальный интеллект'] ?? '');
            $sheet->setCellValue('AR' . $row, $gardnerResults['Телесно-кинестетический интеллект'] ?? '');
            $sheet->setCellValue('AS' . $row, $gardnerResults['Внутриличностный интеллект'] ?? '');
            $sheet->setCellValue('AT' . $row, $gardnerResults['Межличностный интеллект'] ?? '');
            $sheet->setCellValue('AU' . $row, $gardnerResults['Натуралистический интеллект'] ?? '');
            $sheet->setCellValue('AV' . $row, $gardnerResults['Экзистенциальный интеллект'] ?? '');

            $row++;
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        // Автоподбор ширины колонок
        foreach (range('A', 'Z') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        foreach (['AA', 'AB', 'AC', 'AD', 'AE', 'AF', 'AG', 'AH', 'AI', 'AJ', 'AK', 'AL', 'AM', 'AN', 'AO', 'AP', 'AQ', 'AR', 'AS', 'AT', 'AU', 'AV'] as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Стиль данных
        $dataStyle = [
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
                'wrapText' => true
            ],
            'borders' => [
                'allBorders' => ['borderStyle' => Border::BORDER_THIN]
            ]
        ];
        $lastRow = $row - 1;
        if ($lastRow >= 2) {
            $sheet->getStyle("A2:AV{$lastRow}")->applyFromArray($dataStyle);
        }

        // Сохраняем файл
        $writer = new Xlsx($spreadsheet);
        $outputPath = storage_path('app/' . $outputFile);
        $writer->save($outputPath);

        $this->info("Файл сохранён: {$outputPath}");

        return Command::SUCCESS;
    }

    private function formatUniversities($universities): string
    {
        if (!is_array($universities) || empty($universities)) {
            return '';
        }

        $result = [];
        foreach ($universities as $uni) {
            if (!is_array($uni)) continue;

            $line = $uni['name'] ?? '';
            if (!empty($uni['faculty'])) {
                $line .= ', ' . $uni['faculty'];
            }
            if (!empty($uni['degree'])) {
                $line .= ' (' . $uni['degree'] . ')';
            }
            if (!empty($uni['year_start']) || !empty($uni['year_end'])) {
                $line .= ' ' . ($uni['year_start'] ?? '') . '-' . ($uni['year_end'] ?? '');
            }
            $result[] = $line;
        }

        return implode("\n", $result);
    }

    private function formatLanguages($languages): string
    {
        if (!is_array($languages) || empty($languages)) {
            return '';
        }

        $result = [];
        foreach ($languages as $lang) {
            if (!is_array($lang)) continue;

            $line = $lang['language'] ?? '';
            if (!empty($lang['level'])) {
                $line .= ' - ' . $lang['level'];
            }
            $result[] = $line;
        }

        return implode("\n", $result);
    }

    private function formatWorkExperience($experiences): string
    {
        if (!is_array($experiences) || empty($experiences)) {
            return '';
        }

        $result = [];
        foreach ($experiences as $exp) {
            if (!is_array($exp)) continue;

            $line = $exp['company'] ?? '';
            if (!empty($exp['position'])) {
                $line .= ' - ' . $exp['position'];
            }
            if (!empty($exp['start_date']) || !empty($exp['end_date'])) {
                $line .= ' (' . ($exp['start_date'] ?? '') . ' - ' . ($exp['end_date'] ?? 'по н.в.') . ')';
            }
            $result[] = $line;
        }

        return implode("\n", $result);
    }

    private function formatAwards($awards): string
    {
        if (!is_array($awards) || empty($awards)) {
            return '';
        }

        $result = [];
        foreach ($awards as $award) {
            if (!is_array($award)) {
                $result[] = $award;
                continue;
            }

            $line = $award['title'] ?? $award['name'] ?? '';
            if (!empty($award['year'])) {
                $line .= ' (' . $award['year'] . ')';
            }
            $result[] = $line;
        }

        return implode("\n", $result);
    }
}
