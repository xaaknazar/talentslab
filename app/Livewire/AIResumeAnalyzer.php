<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Services\GeminiService;
use Illuminate\Support\Facades\Storage;

class AIResumeAnalyzer extends Component
{
    use WithFileUploads;

    public $pdfFile;
    public $comment = '';
    public $report = '';
    public $isLoading = false;
    public $error = '';
    public $fileName = '';

    protected $rules = [
        'pdfFile' => 'required|file|mimes:pdf|max:20480', // 20MB max
    ];

    protected $messages = [
        'pdfFile.required' => 'Загрузите PDF файл резюме',
        'pdfFile.mimes' => 'Файл должен быть в формате PDF',
        'pdfFile.max' => 'Максимальный размер файла — 20 МБ',
    ];

    public function updatedPdfFile()
    {
        $this->validateOnly('pdfFile');

        if ($this->pdfFile) {
            $this->fileName = $this->pdfFile->getClientOriginalName();
            $this->error = '';
        }
    }

    public function removePdf()
    {
        $this->pdfFile = null;
        $this->fileName = '';
    }

    public function generateReport()
    {
        $this->validate();

        $this->isLoading = true;
        $this->error = '';
        $this->report = '';

        try {
            // Сохраняем PDF временно
            $tempPath = $this->pdfFile->store('temp', 'local');
            $fullPath = Storage::disk('local')->path($tempPath);

            // Извлекаем текст из PDF
            $geminiService = new GeminiService();
            $extractedText = $geminiService->extractTextFromPdf($fullPath);

            // Удаляем временный файл
            Storage::disk('local')->delete($tempPath);

            if (empty($extractedText)) {
                $this->error = 'Не удалось извлечь текст из PDF. Убедитесь, что файл содержит текстовый слой.';
                $this->isLoading = false;
                return;
            }

            // Анализируем резюме
            $result = $geminiService->analyzeResume($extractedText, $this->comment);

            if ($result['success']) {
                $this->report = $result['report'];
            } else {
                $this->error = $result['error'];
            }
        } catch (\Exception $e) {
            $this->error = 'Произошла ошибка: ' . $e->getMessage();
        }

        $this->isLoading = false;
    }

    public function clearReport()
    {
        $this->report = '';
        $this->error = '';
    }

    public function render()
    {
        return view('livewire.ai-resume-analyzer')->layout('layouts.app');
    }
}
