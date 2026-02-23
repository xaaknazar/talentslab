<?php

namespace App\Http\Controllers;

use App\Models\Candidate;
use App\Models\GallupReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Knp\Snappy\Pdf;

class CandidateReportController extends Controller
{
    public function show(Candidate $candidate)
    {
        // Загружаем связанные данные
        $candidate->load(['gallupTalents', 'gallupReports', 'user.gardnerTestResult']);

        // Подготавливаем URL фото
        $photoUrl = null;
        if ($candidate->photo && Storage::disk('public')->exists($candidate->photo)) {
            $photoUrl = Storage::disk('public')->url($candidate->photo);
        }

        // Подготавливаем данные для отображения
        return view('candidates.report', compact('candidate', 'photoUrl'));
    }

    public function showV2(Candidate $candidate, $version = null)
    {
        // Загружаем связанные данные
        $candidate->load(['gallupTalents', 'gallupReports', 'user.gardnerTestResult']);

        // Если есть готовый anketa_pdf - показываем его сразу (только для публичного доступа)
        if ($candidate->anketa_pdf && Storage::disk('public')->exists($candidate->anketa_pdf)) {
            $pdfUrl = Storage::disk('public')->url($candidate->anketa_pdf) . '?t=' . time();
            $title = "{$candidate->full_name} — Полный отчёт";
            $downloadFileName = $this->generateDownloadFileName($candidate);
            return view('candidates.view-anketa', compact('candidate', 'pdfUrl', 'title', 'downloadFileName'));
        }

        // Если нет готового PDF - показываем HTML версию
        return $this->renderReportHtml($candidate, $version);
    }

    /**
     * Рендерит HTML версию отчёта (используется для генерации PDF и отображения)
     */
    public function renderReportHtml(Candidate $candidate, $version = null)
    {
        // Загружаем связанные данные если ещё не загружены
        if (!$candidate->relationLoaded('gallupTalents')) {
            $candidate->load(['gallupTalents', 'gallupReports', 'user.gardnerTestResult']);
        }

        // Подготавливаем URL фото
        $photoUrl = null;
        if ($candidate->photo && Storage::disk('public')->exists($candidate->photo)) {
            $photoUrl = Storage::disk('public')->url($candidate->photo);
        }

        // Определяем тип отчета на основе URL
        $isFullReport = $version === 'full' || $version === null;
        $isReducedReport = $version === 'reduced';

        // Подготавливаем данные для отображения (новая версия)
        return view('candidates.report-v2', compact('candidate', 'photoUrl', 'isFullReport', 'isReducedReport'));
    }

    public function pdf(Candidate $candidate)
    {
        // Для будущей генерации PDF
        $candidate->load(['gallupTalents', 'gallupReports', 'user.gardnerTestResult']);

        $photoUrl = null;
        if ($candidate->photo && Storage::disk('public')->exists($candidate->photo)) {
            $photoUrl = Storage::disk('public')->url($candidate->photo);
        }

        // Здесь можно добавить логику генерации PDF
        return view('candidates.report-pdf', compact('candidate', 'photoUrl'));
    }

    public function downloadGallup(Candidate $candidate)
    {
        // Проверяем, есть ли Gallup файл у кандидата
        if (!$candidate->gallup_pdf || !Storage::disk('public')->exists($candidate->gallup_pdf)) {
            abort(404, 'Gallup файл не найден');
        }

        $filePath = storage_path('app/public/' . $candidate->gallup_pdf);
        $fileName = "gallup_{$candidate->full_name}_{$candidate->id}.pdf";

        return response()->download($filePath, $fileName);
    }

    public function downloadGallupReport(Candidate $candidate, string $type)
    {
        // Ищем отчет указанного типа для кандидата
        $report = $candidate->gallupReports()->where('type', $type)->first();

        if (!$report || !Storage::disk('public')->exists($report->pdf_file)) {
            abort(404, "Отчет типа {$type} не найден");
        }

        $filePath = storage_path('app/public/' . $report->pdf_file);
        $fileName = "gallup_report_{$type}_{$candidate->full_name}_{$candidate->id}.pdf";

        return response()->download($filePath, $fileName);
    }

    public function downloadGallupReportPublic(Candidate $candidate, string $type)
    {
        // Валидируем тип отчета
        $validTypes = ['DPs', 'DPT', 'FMD'];
        if (!in_array($type, $validTypes)) {
            abort(404, 'Неверный тип отчета');
        }

        // Ищем отчет указанного типа для кандидата
        $report = $candidate->gallupReports()->where('type', $type)->first();

        if (!$report || !Storage::disk('public')->exists($report->pdf_file)) {
            abort(404, "Отчет типа {$type} не найден");
        }

        $filePath = storage_path('app/public/' . $report->pdf_file);
        $fileName = "gallup_report_{$type}_{$candidate->full_name}_{$candidate->id}.pdf";

        return response()->download($filePath, $fileName);
    }

    public function dowloadAnketaReport(Candidate $candidate){
        $filePath = storage_path("app/public/" . $candidate->anketa_pdf);

        if (!file_exists($filePath)) {
            abort(404, 'Файл не найден');
        }

        return response()->download($filePath);
    }

    public function downloadAnketaPublic(Candidate $candidate)
    {
        // Формируем имя файла
        $fileName = $this->generateDownloadFileName($candidate);

        // Если есть готовый anketa_pdf (сгенерирован админом) — отдаём его напрямую
        if ($candidate->anketa_pdf && Storage::disk('public')->exists($candidate->anketa_pdf)) {
            $filePath = storage_path('app/public/' . $candidate->anketa_pdf);
            return response()->download($filePath, $fileName);
        }

        // Иначе генерируем PDF анкеты на лету
        $tempPath = $this->generateAnketaOnlyPdf($candidate);
        $filePath = storage_path('app/public/' . $tempPath);

        return response()->download($filePath, $fileName)->deleteFileAfterSend(true);
    }

    /**
     * Показывает полную анкету с Gallup отчётами для публичного просмотра
     */
    public function viewAnketaPublic(Candidate $candidate)
    {
        // Загружаем связанные данные
        $candidate->load(['gallupTalents', 'gallupReports', 'user.gardnerTestResult']);

        // Проверяем наличие готового anketa_pdf
        if ($candidate->anketa_pdf && Storage::disk('public')->exists($candidate->anketa_pdf)) {
            $pdfUrl = Storage::disk('public')->url($candidate->anketa_pdf) . '?t=' . time();
            $title = "{$candidate->full_name} — Полный отчёт";
            $downloadFileName = $this->generateDownloadFileName($candidate);
            return view('candidates.view-anketa', compact('candidate', 'pdfUrl', 'title', 'downloadFileName'));
        }

        // Если нет готовой анкеты - показываем обычную страницу
        $photoUrl = null;
        if ($candidate->photo && Storage::disk('public')->exists($candidate->photo)) {
            $photoUrl = Storage::disk('public')->url($candidate->photo);
        }

        $isFullReport = true;
        $isReducedReport = false;

        return view('candidates.report-v2', compact('candidate', 'photoUrl', 'isFullReport', 'isReducedReport'));
    }

    /**
     * Формирует имя файла для скачивания в формате: {ФИО} - TL{G/B}{YY}-{номер}.pdf
     */
    private function generateDownloadFileName(Candidate $candidate): string
    {
        $genderCode = ($candidate->gender === 'Женский' || $candidate->gender === 'female') ? 'G' : 'B';
        $birthYear = $candidate->birth_date ? substr(date('Y', strtotime($candidate->birth_date)), -2) : '00';
        $candidateNumber = str_pad($candidate->display_number ?? $candidate->id, 4, '0', STR_PAD_LEFT);
        return "{$candidate->full_name} - TL{$genderCode}{$birthYear}-{$candidateNumber}.pdf";
    }

    /**
     * Генерирует PDF только анкеты (без Gallup отчётов)
     */
    protected function generateAnketaOnlyPdf(Candidate $candidate): string
    {
        $tempPdfPath = storage_path("app/temp_anketa_{$candidate->id}.pdf");

        // Удаляем если существует
        if (file_exists($tempPdfPath)) {
            unlink($tempPdfPath);
        }

        // Генерируем HTML из view (renderReportHtml всегда отдаёт HTML отчёта,
        // а не iframe-viewer как showV2 при наличии anketa_pdf)
        $html = $this->renderReportHtml($candidate, 'full')->render();
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');
        $html = $this->cleanHtmlForPdf($html);
        $html = $this->sanitizeUtf8($html);

        $snappy = new Pdf('/usr/bin/wkhtmltopdf');

        $options = [
            'encoding' => 'utf-8',
            'page-size' => 'A4',
            'margin-top' => '2mm',
            'margin-bottom' => '2mm',
            'margin-left' => '2mm',
            'margin-right' => '2mm',
            'zoom' => 1.20,
            'disable-smart-shrinking' => true,
            'print-media-type' => true,
            'load-error-handling' => 'ignore',
            'load-media-error-handling' => 'ignore',
        ];

        $snappy->generateFromHtml($html, $tempPdfPath, $options, true);

        // Копируем во временную папку public для скачивания
        $relativePath = "temp_anketas/anketa_{$candidate->id}_" . time() . ".pdf";
        Storage::disk('public')->makeDirectory('temp_anketas');

        $publicPath = storage_path('app/public/' . $relativePath);
        copy($tempPdfPath, $publicPath);
        unlink($tempPdfPath);

        return $relativePath;
    }

    /**
     * Очищает HTML для корректной генерации PDF
     */
    protected function cleanHtmlForPdf(string $html): string
    {
        // Используем DOMDocument для корректного удаления no-print элементов
        $dom = new \DOMDocument();
        @$dom->loadHTML('<?xml encoding="UTF-8">' . $html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        $xpath = new \DOMXPath($dom);

        // Находим все элементы с классом no-print
        $noPrintElements = $xpath->query('//*[contains(@class, "no-print")]');

        foreach ($noPrintElements as $element) {
            $element->parentNode->removeChild($element);
        }

        $html = $dom->saveHTML();

        // Убираем добавленный XML заголовок
        $html = str_replace('<?xml encoding="UTF-8">', '', $html);

        return $html;
    }

    /**
     * Очищает строку от некорректных UTF-8 символов
     */
    protected function sanitizeUtf8(string $string): string
    {
        return mb_convert_encoding($string, 'UTF-8', 'UTF-8');
    }
}
