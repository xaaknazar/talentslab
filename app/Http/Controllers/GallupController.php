<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\GallupParseHistory;
use App\Models\GallupReport;
use App\Models\GallupReportSheet;
use App\Models\GallupReportSheetIndex;
use App\Models\GallupReportSheetValue;
use App\Services\GallupAnalyzerService;
use Google\Client;
use Google\Service\Drive;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Knp\Snappy\Pdf;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf;
use setasign\Fpdi\Fpdi;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Storage;
use Google\Service\Sheets;
use Spatie\Browsershot\Browsershot;

class GallupController extends Controller
{
    /**
     * Записать шаг в историю
     */
    protected function logStep(Candidate $candidate, string $step, string $status = 'in_progress', ?string $details = null): void
    {
        // Обновляем поле в кандидате
        $candidate->update(['step_parse_gallup' => $step]);

        // Записываем в историю
        GallupParseHistory::createHistory(
            $candidate->id,
            $step,
            $status,
            $details
        );
    }

    /**
     * Проверяет, является ли файл валидным Gallup отчетом (PDF или изображение)
     */
    public function isGallupFile(string $relativePath): bool
    {
        if (!Storage::disk('public')->exists($relativePath)) {
            return false;
        }

        $fullPath = storage_path('app/public/' . $relativePath);
        $analyzer = app(GallupAnalyzerService::class);

        return $analyzer->isValidGallupFile($fullPath);
    }

    /**
     * Проверяет, является ли файл английским Gallup PDF (старый метод для совместимости)
     */
    public function isGallupPdf(string $relativePath): bool
    {
        if (!Storage::disk('public')->exists($relativePath)) {
            return false;
        }

        $fullPath = storage_path('app/public/' . $relativePath);

        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($fullPath);
            $text = $pdf->getText();
            $pages = $pdf->getPages();

            // Ключевые признаки Gallup-отчета (английский)
            $containsCliftonHeader = str_contains($text, 'Gallup, Inc. All rights reserved.');
            $containsTalentList = preg_match('/1\.\s+[A-Za-z-]+/', $text);
            $containsTalentList34 = preg_match('/34\.\s+[A-Za-z-]+/', $text);

            return $containsCliftonHeader && $containsTalentList && $containsTalentList34;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Парсит Gallup файл (PDF или изображение) и обновляет данные кандидата
     */
    public function parseGallupFromCandidateFile(Candidate $candidate)
    {
        // Шаг 1: Проверка файла
        $this->logStep($candidate, 'Проверка файла');

        if (!$candidate->gallup_pdf || !Storage::disk('public')->exists($candidate->gallup_pdf)) {
            $this->logStep($candidate, 'Ошибка: Файл не найден', 'error', 'Файл Gallup не найден в файловой системе');
            return response()->json(['error' => 'Файл не найден.'], 404);
        }

        $fullPath = storage_path('app/public/' . $candidate->gallup_pdf);
        $analyzer = app(GallupAnalyzerService::class);
        $fileType = $analyzer->detectFileType($fullPath);

        // Проверяем валидность файла
        if (!$analyzer->isValidGallupFile($fullPath)) {
            $this->logStep($candidate, 'Ошибка: Неверный формат файла', 'error', 'Файл не является корректным Gallup отчетом');
            return response()->json(['error' => 'Файл не является корректным Gallup отчетом.'], 422);
        }

        // Шаг 2: Определяем метод парсинга
        $talents = null;

        if ($fileType === 'pdf') {
            // Проверяем, является ли это оригинальным Gallup PDF
            $isOriginalGallup = $this->isOriginalGallupPdf($fullPath);

            if ($isOriginalGallup) {
                // Оригинальный Gallup PDF - используем быстрый парсер
                $this->logStep($candidate, 'Обнаружен оригинальный Gallup PDF, используем стандартный парсинг');

                $standardResult = $this->tryStandardPdfParsing($fullPath);
                if ($standardResult !== null) {
                    $talents = $standardResult;
                    $this->logStep($candidate, 'Стандартный парсинг успешен');
                }
            } else {
                // Не оригинальный Gallup PDF - сразу используем GPT-4o (как для изображений)
                $this->logStep($candidate, 'PDF не является оригинальным Gallup, используем GPT-4o');
            }
        }

        // Шаг 3: Если парсинг не сработал или это изображение/неоригинальный PDF - используем GPT-4o
        if ($talents === null) {
            $this->logStep($candidate, 'Анализ через GPT-4o');

            $result = $analyzer->analyzeGallupFile($fullPath, $fileType);

            if (!$result['success']) {
                $this->logStep($candidate, 'Ошибка GPT-4o: ' . $result['error'], 'error', $result['error']);
                return response()->json(['error' => $result['error']], 422);
            }

            $talents = $result['talents'];
            $this->logStep($candidate, 'GPT-4o анализ успешен');
        }

        // Проверка количества талантов
        if (count($talents) !== 34) {
            $this->logStep($candidate, 'Ошибка: Найдено ' . count($talents) . ' талантов вместо 34', 'error');
            return response()->json([
                'error' => 'Найдено ' . count($talents) . ' талантов. Ожидается 34.',
                'debug' => $talents,
            ], 422);
        }

        // Шаг 4: Обновление талантов
        $this->logStep($candidate, 'Обновление талантов');

        // Получаем текущие таланты из базы
        $existingTalents = $candidate->gallupTalents()
            ->orderBy('position')
            ->pluck('name')
            ->toArray();

        // Проверка на изменения
        $hasChanged = $existingTalents !== $talents;

        // Если таланты изменились — обновляем их
        if ($hasChanged) {
            $candidate->gallupTalents()->delete();

            foreach ($talents as $index => $name) {
                $candidate->gallupTalents()->create([
                    'name' => trim($name),
                    'position' => $index + 1,
                ]);
            }
        }

        // Шаг 5: Обработка отчетов (всегда выполняем, даже если таланты не изменились)
        $this->logStep($candidate, 'Обработка отчетов');

        // Получаем все активные листы отчетов из базы данных
        $reportSheets = GallupReportSheet::with('indices')->orderBy('id', 'desc')->get();

        foreach ($reportSheets as $reportSheet) {
            // Шаг 6: Обновление Google Sheets
            $this->logStep($candidate, "Обновление Google Sheets: {$reportSheet->name_report}");
            $this->updateGoogleSheetByCellMap($candidate, $talents, $reportSheet);

            Log::info('Перед вызовом importFormulaValues', [
                'reportSheet_id' => $reportSheet->id,
                'candidate_id' => $candidate->id,
            ]);

            // Шаг 7: Импорт формул
            $this->logStep($candidate, "Импорт формул: {$reportSheet->name_report}");
            $this->importFormulaValues($reportSheet, $candidate);

            // Шаг 8: Скачивание PDF
            $this->logStep($candidate, "Скачивание PDF: {$reportSheet->name_report}");
            $this->downloadSheetPdf(
                $candidate,
                $reportSheet
            );
        }

        // Шаг 9: Завершение
        $this->logStep($candidate, 'Завершено успешно', 'completed', 'Все отчеты успешно обработаны');

        return response()->json([
            'message' => 'Данные Gallup обновлены, Google Sheet заполнен.',
            'step' => $candidate->step_parse_gallup
        ]);
    }

    /**
     * Проверяет, является ли PDF оригинальным Gallup файлом (быстрая проверка без полного парсинга)
     */
    private function isOriginalGallupPdf(string $fullPath): bool
    {
        try {
            // Читаем только первые 50KB файла для быстрой проверки
            $handle = fopen($fullPath, 'r');
            if (!$handle) {
                return false;
            }

            $content = fread($handle, 50000);
            fclose($handle);

            // Проверяем наличие ключевых признаков оригинального Gallup PDF
            $hasGallupInc = strpos($content, 'Gallup, Inc.') !== false;
            $hasCliftonStrengths = strpos($content, 'CliftonStrengths') !== false;
            $hasStrengthsFinder = strpos($content, 'StrengthsFinder') !== false;

            $isOriginal = $hasGallupInc || $hasCliftonStrengths || $hasStrengthsFinder;

            Log::info('Проверка оригинального Gallup PDF', [
                'file' => basename($fullPath),
                'has_gallup_inc' => $hasGallupInc,
                'has_clifton' => $hasCliftonStrengths,
                'is_original' => $isOriginal
            ]);

            return $isOriginal;
        } catch (\Exception $e) {
            Log::warning('Ошибка проверки оригинального Gallup PDF: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Попытка стандартного парсинга английского PDF
     * Возвращает массив талантов или null если парсинг не удался
     * ВАЖНО: Вызывать только для оригинальных Gallup PDF файлов!
     */
    private function tryStandardPdfParsing(string $fullPath): ?array
    {
        try {
            // Увеличиваем лимит памяти только для парсинга
            $originalMemoryLimit = ini_get('memory_limit');
            ini_set('memory_limit', '256M');

            $parser = new Parser();
            $pdf = $parser->parseFile($fullPath);
            $pages = $pdf->getPages();

            // Восстанавливаем лимит памяти
            ini_set('memory_limit', $originalMemoryLimit);

            if (empty($pages)) {
                return null;
            }

            $firstPageText = $pages[0]->getText();

            // Пытаемся найти английские таланты
            preg_match_all('/\b([1-9]|[1-2][0-9]|3[0-4])\.\s+([A-Za-z-]+)/', $firstPageText, $matches);
            $numbers = $matches[1] ?? [];
            $talents = $matches[2] ?? [];

            // Проверяем, что найдено ровно 34 таланта
            if (count($talents) !== 34 || max($numbers) != 34 || min($numbers) != 1) {
                return null;
            }

            return $talents;

        } catch (\Exception $e) {
            Log::warning('Стандартный парсинг PDF не удался: ' . $e->getMessage());
            return null;
        }
    }

    protected function updateGoogleSheetByCellMap(Candidate $candidate, array $talents, GallupReportSheet $reportSheet)
    {
        try {
            $credentialsPath = storage_path('app/google/credentials.json');

            // Проверяем существование файла credentials
            if (!file_exists($credentialsPath)) {
                throw new \Exception("Файл credentials.json не найден. Необходимо настроить Google Service Account для генерации отчётов.");
            }

            $client = new \Google\Client();
            $client->setAuthConfig($credentialsPath);
            $client->addScope(\Google\Service\Sheets::SPREADSHEETS);
            $client->setAccessType('offline');
            $spreadsheetId = $reportSheet->spreadsheet_id;
            $sheets = new \Google\Service\Sheets($client);

            $talentOrder = [
                'Achiever', 'Discipline', 'Arranger', 'Focus', 'Belief', 'Responsibility',
                'Consistency', 'Restorative', 'Deliberative', 'Activator', 'Maximizer',
                'Command', 'Self-Assurance', 'Communication', 'Significance', 'Competition', 'Woo',
                'Adaptability', 'Includer', 'Connectedness', 'Individualization', 'Developer',
                'Positivity', 'Empathy', 'Relator', 'Harmony', 'Analytical', 'Input',
                'Context', 'Intellection', 'Futuristic', 'Learner', 'Ideation', 'Strategic'
            ];

            // Создаём строку B3:AJ3 = [Имя, 34 позиции]
            $row = [$candidate->full_name];

            foreach ($talentOrder as $talentName) {
                $index = array_search($talentName, $talents);
                $row[] = $index !== false ? $index + 1 : '';
            }

            // Ограничим на всякий случай длину в 35
            $row = array_slice($row, 0, 35);

            $body = new \Google\Service\Sheets\ValueRange([
                'range' => 'Info!B3:AJ3',
                'values' => [$row]
            ]);

            $sheets->spreadsheets_values->update($spreadsheetId, 'Info!B3:AJ3', $body, [
                'valueInputOption' => 'RAW'
            ]);
        } catch (\Exception $e) {
            $this->logStep($candidate, "Ошибка обновления Google Sheets: " . $e->getMessage(), 'error', $e->getMessage());
            throw $e;
        }
    }

    protected function downloadSheetPdf(Candidate $candidate, GallupReportSheet $reportSheet)
    {
        try {
            // 1. Настройка Google клиента
            $credentialsPath = storage_path('app/google/credentials.json');

            if (!file_exists($credentialsPath)) {
                throw new \Exception("Файл credentials.json не найден. Необходимо настроить Google Service Account для генерации отчётов.");
            }

            $client = new \Google\Client();
            $client->setAuthConfig($credentialsPath);
            $client->addScope('https://www.googleapis.com/auth/drive.readonly');
            $client->setAccessType('offline');

            $accessToken = $client->fetchAccessTokenWithAssertion()['access_token'];

            $http_build_query = [
                'format' => 'pdf',
                'portrait' => 'true',
                'size' => 'A4',
                'fitw' => 'true',
                'sheetnames' => 'false',
                'printtitle' => 'false',
                'pagenum' => 'false',
                'gridlines' => 'false',
                'fzr' => 'false',
                'horizontal_alignment' => 'CENTER',
                'top_margin' => '0.50',
                'bottom_margin' => '0.50',
                'left_margin' => '0.50',
                'right_margin' => '0.50',];

            // 2. PDF URL из Google Sheets
            $url = "https://docs.google.com/spreadsheets/d/{$reportSheet->spreadsheet_id}/export?" . http_build_query($http_build_query) . "&gid={$reportSheet->gid}";
            $url_short = "https://docs.google.com/spreadsheets/d/{$reportSheet->spreadsheet_id}/export?" . http_build_query($http_build_query) . "&gid={$reportSheet->short_gid}";

            $response = Http::withHeaders([
                'Authorization' => "Bearer $accessToken"
            ])->get($url);

            $response_short = Http::withHeaders([
                'Authorization' => "Bearer $accessToken"
            ])->get($url_short);

            if (!$response->successful() || !$response_short->successful()) {
                throw new \Exception("Ошибка при скачивании PDF: " . $response->status());
            }

            // 3. Удаляем старый отчёт, если есть
            $existing = GallupReport::where('candidate_id', $candidate->id)
                ->where('type', $reportSheet->name_report)
                ->first();

            if ($existing) {
                if ($existing->pdf_file && Storage::disk('public')->exists($existing->pdf_file)) {
                    Storage::disk('public')->delete($existing->pdf_file);
                }

                if ($existing->short_area_pdf_file && Storage::disk('public')->exists($existing->short_area_pdf_file)) {
                    Storage::disk('public')->delete($existing->short_area_pdf_file);
                }

                $existing->delete();
            }

            // 4. Генерируем пути
            $folder = 'reports/candidate_'.$candidate->id;
            $pdfFileName = str_replace(' ', '_', $candidate->full_name) . "_{$reportSheet->name_report}.pdf";
            $pdfFileName_short = str_replace(' ', '_', $candidate->full_name) . "_{$reportSheet->name_report}_short.pdf";

            $pdfPath = "{$folder}/{$pdfFileName}";
            $pdfPath_short = "{$folder}/{$pdfFileName_short}";

            // 5. Сохраняем PDF
            Storage::disk('public')->put($pdfPath, $response->body());
            Storage::disk('public')->put($pdfPath_short, $response_short->body());

            // 7. Записываем отчет
            $report = GallupReport::create([
                'candidate_id' => $candidate->id,
                'type' => $reportSheet->name_report,
                'pdf_file' => $pdfPath,
                'short_area_pdf_file' => $pdfPath_short,
            ]);
        } catch (\Exception $e) {
            $this->logStep($candidate, "Ошибка скачивания PDF: " . $e->getMessage(), 'error', $e->getMessage());
            throw $e;
        }
    }
    protected function cleanHtmlForPdf($html)
    {
        // 1. Принудительно конвертируем в UTF-8
        $html = mb_convert_encoding($html, 'UTF-8', 'UTF-8');

        // 2. Удаляем проблемные управляющие символы
        $html = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', '', $html);

        // 3. Заменяем проблемные символы
        $html = str_replace([
            '\u00a0', // неразрывный пробел
            '&nbsp;'
        ], ' ', $html);

        // 4. Удаляем неправильные последовательности UTF-8
        $html = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F-\x9F]/u', '', $html);

        // 5. Проверяем и добавляем мета-тег для кодировки
        if (!preg_match('/<meta[^>]*charset/i', $html)) {
            if (preg_match('/<head[^>]*>/i', $html)) {
                $html = preg_replace('/(<head[^>]*>)/i', '$1<meta charset="UTF-8">', $html);
            } else {
                $html = '<meta charset="UTF-8">' . $html;
            }
        }

        // 6. Добавляем DOCTYPE если его нет
        if (!str_starts_with(trim($html), '<!DOCTYPE')) {
            $html = '<!DOCTYPE html>' . $html;
        }

        return $html;
    }
    protected function sanitizeUtf8($text)
    {
        // Удаляем невалидные UTF-8 последовательности
        $text = filter_var($text, FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);

        // Принудительно валидируем UTF-8
        if (!mb_check_encoding($text, 'UTF-8')) {
            // Заменяем невалидные символы на знак вопроса
            $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');
        }

        return $text;
    }

    public function mergeCandidateReportPdfs(Candidate $candidate, string $version = 'full')
    {
        $tempHtmlPdf = storage_path("app/temp_candidate_{$candidate->id}.pdf");
        // ✅ Удаляем временный PDF, если он уже существует
        if (file_exists($tempHtmlPdf)) {
            unlink($tempHtmlPdf);
        }
        // 1️⃣ Сгенерировать PDF анкеты
        $html = app(\App\Http\Controllers\CandidateReportController::class)
            ->showV2($candidate, $version)
            ->render();
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        if (!mb_check_encoding($html, 'UTF-8')) {
            dd("HTML is not valid UTF-8");
        }
        $html = $this->cleanHtmlForPdf($html);
        // Дополнительная проверка и исправление кодировки
        if (!mb_check_encoding($html, 'UTF-8')) {
            // Попытка определить и конвертировать кодировку
            $encoding = mb_detect_encoding($html, ['UTF-8', 'Windows-1251', 'ISO-8859-1'], true);
            if ($encoding && $encoding !== 'UTF-8') {
                $html = mb_convert_encoding($html, 'UTF-8', $encoding);
            } else {
                // Принудительная очистка от некорректных символов
                $html = mb_convert_encoding($html, 'UTF-8', 'UTF-8');
            }
        }

        // Финальная очистка
        $html = $this->sanitizeUtf8($html);

        $snappy = new Pdf('/usr/bin/wkhtmltopdf');

        $s_options = [
            'encoding' => 'utf-8',
            'page-size' => 'A4',
            'margin-top' => '10mm',
            'margin-bottom' => '10mm',
            'margin-left' => '2mm',
            'margin-right' => '2mm',
            'zoom' => 1.30,
            'disable-smart-shrinking' => true,
            'print-media-type' => true,
            'load-error-handling' => 'ignore',
            'load-media-error-handling' => 'ignore',
        ];

        try {
            $snappy->generateFromHtml($html, $tempHtmlPdf, $s_options, true);
        } catch (\Exception $e) {
            dd([
                'message' => $e->getMessage(),
                'snippet' => mb_substr($html, 0, 1000),
            ]);
        }

        // 2️⃣ Получаем все файлы для объединения
        $pdfPaths = [$tempHtmlPdf];

        // Добавляем Gallup отчёты только для полной версии
        if ($version === 'full') {
            $reports = GallupReport::where('candidate_id', $candidate->id)->get();

            foreach ($reports as $report) {

                $file = $report->short_area_pdf_file;
                if (!$file) continue;

                $relative = ltrim($file, '/');

                $fullPath = Storage::disk('public')->path($relative);

                if (file_exists($fullPath)) {
                    $pdfPaths[] = $fullPath;
                }
            }
        }

        // 3️⃣ Объединяем через FPDI
        $pdfFileName = str_replace(' ', '_', $candidate->full_name) . "_full_anketa";
        $outputRelative = "reports/candidate_{$candidate->id}/{$pdfFileName}.pdf";
        $outputFull = Storage::disk('public')->path($outputRelative);

        Storage::disk('public')->makeDirectory(dirname($outputRelative));

        if ($candidate->anketa_pdf){
            if (Storage::disk('public')->exists($candidate->anketa_pdf)) {
                Storage::disk('public')->delete($candidate->anketa_pdf);
            }
        }
        $pdf = new Fpdi();

        foreach ($pdfPaths as $path) {
            try {
                $pageCount = $pdf->setSourceFile($path);
                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    $templateId = $pdf->importPage($pageNo);
                    $size = $pdf->getTemplateSize($templateId);

                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($templateId);
                }
            } catch (\Throwable $e) {
                Log::warning("Ошибка при объединении PDF: {$path} — " . $e->getMessage());
            }
        }

        $pdf->Output($outputFull, 'F');

        //Удаляем временный PDF
        if (file_exists($tempHtmlPdf)) {
            unlink($tempHtmlPdf);
        }

        return $outputRelative;
    }

    public function importFormulaValues_old(GallupReportSheet $reportSheet, Candidate $candidate)
    {
        Log::info('Импорт психотипов начат', ['report_id' => $candidate->id]);
        $spreadsheetId = $reportSheet->spreadsheet_id;
        $sheetName = 'Formula';

        // Авторизация Google
        $client = new \Google\Client();
        $client->setAuthConfig(storage_path('app/google/credentials.json'));
        $client->addScope(Sheets::SPREADSHEETS_READONLY);
        $client->useApplicationDefaultCredentials();

        $service = new Sheets($client);

        // Удаляем старые значения (если нужно)
        GallupReportSheetValue::where('gallup_report_sheet_id', $reportSheet->id)->where('candidate_id', $candidate->id)->delete();

        // Получаем список нужных ячеек
        $indexes = GallupReportSheetIndex::where('gallup_report_sheet_id', $reportSheet->id)->get();
        Log::info('GallupReportSheetIndex values', ['indexes' => $indexes->toArray()]);
        foreach ($indexes as $index) {
            $range = "{$sheetName}!{$index->index}";
            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
            $value = $response->getValues()[0][0] ?? null;

            if ($value === null) continue;

            GallupReportSheetValue::create([
                'gallup_report_sheet_id' => $reportSheet->id,
                'candidate_id' => $candidate->id,
                'type' => $index->type,
                'name' => $index->name,
                'value' => $value,
            ]);

            Log::info('Импорт значения', [
                'index' => $index->index,
                'name' => $index->name,
                'value' => $value,
            ]);
        }

        Log::info('Импорт психотипов завершён');
    }

    public function importFormulaValues(GallupReportSheet $reportSheet, Candidate $candidate)
    {
        try {
            Log::info('Импорт психотипов начат', ['report_id' => $candidate->id]);
            $spreadsheetId = $reportSheet->spreadsheet_id;
            $sheetName = 'Formula';

            // Авторизация Google
            $credentialsPath = storage_path('app/google/credentials.json');

            // Проверяем существование файла credentials
            if (!file_exists($credentialsPath)) {
                throw new \Exception("Файл credentials.json не найден в {$credentialsPath}. Необходимо создать Google Service Account и поместить JSON файл в эту папку.");
            }

            $client = new \Google\Client();
            $client->setAuthConfig($credentialsPath);
            $client->addScope(\Google\Service\Sheets::SPREADSHEETS_READONLY);
            $client->setAccessType('offline');

            $service = new Sheets($client);

            // Получаем список нужных ячеек
            $indexes = GallupReportSheetIndex::where('gallup_report_sheet_id', $reportSheet->id)->get();
            if ($indexes->isEmpty()) return;

            // Определяем минимальную и максимальную ячейку (например: O21:AJ24)
            $minRow = $maxRow = null;
            $minCol = $maxCol = null;

            $positions = [];

            foreach ($indexes as $index) {
                [$col, $row] = $this->cellToCoordinates($index->index);

                $minRow = is_null($minRow) ? $row : min($minRow, $row);
                $maxRow = is_null($maxRow) ? $row : max($maxRow, $row);
                $minCol = is_null($minCol) ? $col : min($minCol, $col);
                $maxCol = is_null($maxCol) ? $col : max($maxCol, $col);

                $positions[$index->index] = [
                    'row' => $row,
                    'col' => $col,
                    'type' => $index->type,
                    'name' => $index->name,
                ];
            }

            // Преобразуем координаты обратно в строку
            $minCell = $this->coordinatesToCell($minCol, $minRow);
            $maxCell = $this->coordinatesToCell($maxCol, $maxRow);
            $range = "{$sheetName}!{$minCell}:{$maxCell}";

            Log::info('Запрашиваем диапазон: ' . $range);

            $response = $service->spreadsheets_values->get($spreadsheetId, $range);
            $matrix = $response->getValues();

            // Удаляем старые значения
            GallupReportSheetValue::where('gallup_report_sheet_id', $reportSheet->id)
                ->where('candidate_id', $candidate->id)
                ->delete();

            foreach ($positions as $cell => $meta) {
                $relativeRow = $meta['row'] - $minRow;
                $relativeCol = $meta['col'] - $minCol;

                $value = $matrix[$relativeRow][$relativeCol] ?? null;
                if ($value === null) continue;

                if (is_string($value)) {
                    $value = preg_replace('/\s*%$/', '', $value);
                }

                GallupReportSheetValue::create([
                    'gallup_report_sheet_id' => $reportSheet->id,
                    'candidate_id' => $candidate->id,
                    'type' => $meta['type'],
                    'name' => $meta['name'],
                    'value' => (int) $value,
                ]);

                Log::info('Импорт значения', [
                    'cell' => $cell,
                    'name' => $meta['name'],
                    'value' => $value,
                ]);
            }

            Log::info('Импорт психотипов завершён');
        } catch (\Exception $e) {
            $this->logStep($candidate, "Ошибка импорта формул: " . $e->getMessage(), 'error', $e->getMessage());
            throw $e;
        }
    }

    protected function cellToCoordinates($cell)
    {
        preg_match('/^([A-Z]+)(\d+)$/', strtoupper($cell), $matches);
        $letters = $matches[1];
        $row = (int)$matches[2];

        $col = 0;
        foreach (str_split($letters) as $char) {
            $col = $col * 26 + (ord($char) - 64);
        }

        return [$col, $row];
    }

    protected function coordinatesToCell($col, $row)
    {
        $letters = '';
        while ($col > 0) {
            $col--;
            $letters = chr($col % 26 + 65) . $letters;
            $col = intdiv($col, 26);
        }

        return $letters . $row;
    }

    /**
     * Генерирует объединенную анкету по требованию с временным хранением
     */
    public function generateAnketaPdfOnDemand(Candidate $candidate, string $version = 'full')
    {
        // Генерируем объединенный PDF
        $mergedPath = $this->mergeCandidateReportPdfs($candidate, $version);

        // Создаем временный файл с уникальным именем
        // Формат: {full_name} - TL{G/B}{YY}-{ID}
        // TL - TalentsLab, G - girl, B - boy, YY - год рождения (2 цифры), ID - id с ведущими нулями
        $genderCode = ($candidate->gender === 'Женский' || $candidate->gender === 'female') ? 'G' : 'B';
        $birthYear = $candidate->birth_date ? substr(date('Y', strtotime($candidate->birth_date)), -2) : '00';
        $candidateId = str_pad($candidate->id, 4, '0', STR_PAD_LEFT);
        $version_text = $version === 'full' ? 'полная' : 'краткая';

        $tempFileName = "{$candidate->full_name} - TL{$genderCode}{$birthYear}-{$candidateId}-{$version_text}.pdf";

        $tempPath = "temp_anketas/{$tempFileName}";

        // Копируем во временную папку
        $tempFullPath = Storage::disk('public')->path($tempPath);
        Storage::disk('public')->makeDirectory(dirname($tempPath));

        copy(Storage::disk('public')->path($mergedPath), $tempFullPath);

        // Удаляем оригинальный объединенный файл
        Storage::disk('public')->delete($mergedPath);

        // Планируем удаление временного файла через 30 минут
        $this->scheduleTempFileDeletion($tempPath, 30);

        return $tempPath;
    }

    /**
     * Планирует удаление временного файла
     */
    protected function scheduleTempFileDeletion(string $filePath, int $minutes)
    {
        // Используем Queue с задержкой для удаления файла
        \App\Jobs\DeleteTempFile::dispatch($filePath)->delay(now()->addMinutes($minutes));
    }

    /**
     * Генерирует переведённую анкету в PDF
     */
    public function generateTranslatedAnketaPdf(Candidate $candidate, string $targetLanguage, string $version = 'full')
    {
        $translationService = app(\App\Services\TranslationService::class);

        // Получаем переведённые данные
        $translatedData = $translationService->translateCandidate($candidate, $targetLanguage);

        // Генерируем HTML с переведёнными данными
        $html = $this->generateTranslatedReportHtml($candidate, $translatedData, $targetLanguage, $version);

        // Создаём PDF
        $tempHtmlPdf = storage_path("app/temp_translated_{$candidate->id}_{$targetLanguage}.pdf");

        if (file_exists($tempHtmlPdf)) {
            unlink($tempHtmlPdf);
        }

        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $html = $this->cleanHtmlForPdf($html);
        $html = $this->sanitizeUtf8($html);

        $snappy = new \Knp\Snappy\Pdf('/usr/bin/wkhtmltopdf');

        $s_options = [
            'encoding' => 'utf-8',
            'page-size' => 'A4',
            'margin-top' => '10mm',
            'margin-bottom' => '10mm',
            'margin-left' => '2mm',
            'margin-right' => '2mm',
            'zoom' => 1.30,
            'disable-smart-shrinking' => true,
            'print-media-type' => true,
            'load-error-handling' => 'ignore',
            'load-media-error-handling' => 'ignore',
        ];

        $snappy->generateFromHtml($html, $tempHtmlPdf, $s_options, true);

        // Создаём имя файла
        $languageNames = ['ru' => 'RU', 'en' => 'EN', 'ar' => 'AR'];
        $langCode = $languageNames[$targetLanguage] ?? strtoupper($targetLanguage);
        $genderCode = ($candidate->gender === 'Женский' || $candidate->gender === 'female') ? 'G' : 'B';
        $birthYear = $candidate->birth_date ? substr(date('Y', strtotime($candidate->birth_date)), -2) : '00';
        $candidateId = str_pad($candidate->id, 4, '0', STR_PAD_LEFT);
        $versionText = $version === 'full' ? 'full' : 'reduced';

        $tempFileName = "{$candidate->full_name} - TL{$genderCode}{$birthYear}-{$candidateId}-{$langCode}-{$versionText}.pdf";
        $tempPath = "temp_anketas/{$tempFileName}";

        $tempFullPath = Storage::disk('public')->path($tempPath);
        Storage::disk('public')->makeDirectory(dirname($tempPath));

        copy($tempHtmlPdf, $tempFullPath);
        unlink($tempHtmlPdf);

        // Планируем удаление через 30 минут
        $this->scheduleTempFileDeletion($tempPath, 30);

        return $tempPath;
    }

    /**
     * Генерирует HTML отчёта с переведёнными данными
     */
    private function generateTranslatedReportHtml(Candidate $candidate, array $translatedData, string $targetLanguage, string $version): string
    {
        // Создаём обёртку с переведёнными данными
        $translatedCandidate = new \App\Services\TranslatedCandidate($candidate, $translatedData, $targetLanguage);

        // Подготавливаем URL фото
        $photoUrl = null;
        if ($candidate->photo && Storage::disk('public')->exists($candidate->photo)) {
            $photoUrl = Storage::disk('public')->url($candidate->photo);
        }

        $isFullReport = $version === 'full';
        $isReducedReport = $version === 'reduced';

        // Рендерим view с переведёнными данными
        return view('candidates.report-v2-translated', [
            'candidate' => $translatedCandidate,
            'originalCandidate' => $candidate,
            'photoUrl' => $photoUrl,
            'isFullReport' => $isFullReport,
            'isReducedReport' => $isReducedReport,
            'targetLanguage' => $targetLanguage,
        ])->render();
    }

    /**
     * Получить историю парсинга для кандидата
     */
    public function getParseHistory(Candidate $candidate)
    {
        $history = GallupParseHistory::getHistoryForCandidate($candidate->id);

        return response()->json([
            'candidate_id' => $candidate->id,
            'candidate_name' => $candidate->full_name,
            'current_step' => $candidate->step_parse_gallup,
            'history' => $history
        ]);
    }

    /**
     * Получить статистику парсинга
     */
    public function getParseStatistics()
    {
        $totalRecords = GallupParseHistory::count();
        $errorRecords = GallupParseHistory::where('status', 'error')->count();
        $completedRecords = GallupParseHistory::where('status', 'completed')->count();
        $inProgressRecords = GallupParseHistory::where('status', 'in_progress')->count();

        $recentErrors = GallupParseHistory::where('status', 'error')
            ->where('created_at', '>=', now()->subHours(24))
            ->with('candidate:id,full_name')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json([
            'statistics' => [
                'total_records' => $totalRecords,
                'error_records' => $errorRecords,
                'completed_records' => $completedRecords,
                'in_progress_records' => $inProgressRecords,
            ],
            'recent_errors' => $recentErrors
        ]);
    }


}
