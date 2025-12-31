<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use App\Services\OpenAIService;
use App\Models\Candidate;
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
    public $extractedText = '';
    public $showExtractedText = false;

    // Candidate search
    public $candidateSearch = '';
    public $candidateResults = [];
    public $selectedCandidate = null;
    public $showCandidateDropdown = false;

    // For resetting file input
    public $uploadKey = 1;

    protected $rules = [
        'pdfFile' => 'required|file|mimes:pdf|max:20480',
    ];

    protected $messages = [
        'pdfFile.required' => 'Загрузите PDF файл',
        'pdfFile.mimes' => 'Файл должен быть в формате PDF',
        'pdfFile.max' => 'Максимальный размер файла — 20 МБ',
    ];

    public function updatedCandidateSearch()
    {
        if (strlen($this->candidateSearch) >= 2) {
            $this->candidateResults = Candidate::where('full_name', 'like', '%' . $this->candidateSearch . '%')
                ->orWhere('email', 'like', '%' . $this->candidateSearch . '%')
                ->limit(10)
                ->get(['id', 'full_name', 'email', 'phone', 'current_city'])
                ->toArray();
            $this->showCandidateDropdown = true;
        } else {
            $this->candidateResults = [];
            $this->showCandidateDropdown = false;
        }
    }

    public function selectCandidate($candidateId)
    {
        $this->selectedCandidate = Candidate::with(['gallupTalents', 'gallupReports'])->find($candidateId);
        $this->candidateSearch = $this->selectedCandidate->full_name ?? '';
        $this->showCandidateDropdown = false;
        $this->candidateResults = [];
    }

    #[On('selectCandidateEvent')]
    public function handleSelectCandidate($id)
    {
        $this->selectCandidate($id);
    }

    public function clearCandidate()
    {
        $this->reset(['selectedCandidate', 'candidateSearch', 'candidateResults', 'showCandidateDropdown']);
    }

    #[On('clearCandidateEvent')]
    public function handleClearCandidate()
    {
        $this->clearCandidate();
    }

    public function updatedPdfFile()
    {
        $this->validateOnly('pdfFile');

        if ($this->pdfFile) {
            $this->fileName = $this->pdfFile->getClientOriginalName();
            $this->error = '';
            $this->extractedText = '';
            $this->showExtractedText = false;
        }
    }

    public function removePdf()
    {
        $this->reset(['pdfFile', 'fileName', 'extractedText', 'showExtractedText']);
        $this->uploadKey++;
    }

    #[On('removePdfEvent')]
    public function handleRemovePdf()
    {
        $this->removePdf();
    }

    public function toggleExtractedText()
    {
        $this->showExtractedText = !$this->showExtractedText;
    }

    /**
     * Format candidate data for AI analysis
     */
    private function formatCandidateData(): string
    {
        if (!$this->selectedCandidate) {
            return '';
        }

        $c = $this->selectedCandidate;
        $data = [];

        $data[] = "=== ДАННЫЕ КАНДИДАТА ИЗ БАЗЫ ДАННЫХ ===\n";

        // Basic info
        $data[] = "ФИО: " . ($c->full_name ?? 'Не указано');
        $data[] = "Email: " . ($c->email ?? 'Не указано');
        $data[] = "Телефон: " . ($c->phone ?? 'Не указано');
        $data[] = "Пол: " . ($c->gender ?? 'Не указано');
        $data[] = "Семейное положение: " . ($c->marital_status ?? 'Не указано');
        $data[] = "Дата рождения: " . ($c->birth_date ? $c->birth_date->format('d.m.Y') : 'Не указано');
        $data[] = "Место рождения: " . ($c->birth_place ?? 'Не указано');
        $data[] = "Текущий город: " . ($c->current_city ?? 'Не указано');
        $data[] = "Готовность к переезду: " . ($c->ready_to_relocate ? 'Да' : 'Нет');

        // Education
        $data[] = "\n--- Образование ---";
        $data[] = "Школа: " . ($c->school ?? 'Не указано');
        if ($c->universities && is_array($c->universities)) {
            $data[] = "Университеты: " . json_encode($c->universities, JSON_UNESCAPED_UNICODE);
        }
        if ($c->language_skills && is_array($c->language_skills)) {
            $data[] = "Языки: " . json_encode($c->language_skills, JSON_UNESCAPED_UNICODE);
        }
        $data[] = "Компьютерные навыки: " . ($c->computer_skills ?? 'Не указано');

        // Work experience
        $data[] = "\n--- Опыт работы ---";
        $data[] = "Общий стаж: " . ($c->total_experience_years ?? 0) . " лет";
        $data[] = "Желаемая должность: " . ($c->desired_position ?? 'Не указано');
        $data[] = "Сфера деятельности: " . ($c->activity_sphere ?? 'Не указано');
        $data[] = "Ожидаемая зарплата: " . $c->formatted_salary_range;
        if ($c->work_experience && is_array($c->work_experience)) {
            $data[] = "История работы: " . json_encode($c->work_experience, JSON_UNESCAPED_UNICODE);
        }
        $data[] = "Требования к работодателю: " . ($c->employer_requirements ?? 'Не указано');

        // Personal
        $data[] = "\n--- Личная информация ---";
        $data[] = "Религия: " . ($c->religion ?? 'Не указано');
        $data[] = "Хобби: " . ($c->hobbies ?? 'Не указано');
        $data[] = "Интересы: " . ($c->interests ?? 'Не указано');
        $data[] = "Книг в год: " . ($c->books_per_year ?? 'Не указано');
        $data[] = "Часов развлечений в неделю: " . ($c->entertainment_hours_weekly ?? 'Не указано');
        $data[] = "Часов обучения в неделю: " . ($c->educational_hours_weekly ?? 'Не указано');
        $data[] = "Часов в соцсетях в неделю: " . ($c->social_media_hours_weekly ?? 'Не указано');

        // MBTI
        if ($c->mbti_type) {
            $data[] = "\n--- MBTI ---";
            $data[] = "Тип: " . $c->mbti_full_name;
        }

        // Gallup Talents
        if ($c->gallupTalents && $c->gallupTalents->count() > 0) {
            $data[] = "\n--- Gallup Таланты ---";
            foreach ($c->gallupTalents as $talent) {
                $data[] = "- " . $talent->name . ": " . $talent->rank;
            }
        }

        $data[] = "\n=== КОНЕЦ ДАННЫХ ИЗ БАЗЫ ===\n";

        return implode("\n", $data);
    }

    public function generateReport()
    {
        $this->validate();

        $this->isLoading = true;
        $this->error = '';
        $this->report = '';

        try {
            // Save PDF temporarily
            $tempPath = $this->pdfFile->store('temp', 'local');
            $fullPath = Storage::disk('local')->path($tempPath);

            // Extract text from PDF
            $aiService = new OpenAIService();
            $extractedText = $aiService->extractTextFromPdf($fullPath);

            // Store extracted text for viewing
            $this->extractedText = $extractedText ?? '';

            // Delete temp file
            Storage::disk('local')->delete($tempPath);

            if (empty($extractedText)) {
                $this->error = 'Не удалось извлечь текст из PDF. Убедитесь, что файл содержит текстовый слой.';
                $this->isLoading = false;
                return;
            }

            // Combine candidate data with PDF text
            $candidateData = $this->formatCandidateData();
            $fullText = $candidateData . "\n\n=== ДАННЫЕ ИЗ PDF ОТЧЕТА ===\n\n" . $extractedText;

            // Analyze resume
            $result = $aiService->analyzeResume($fullText, $this->comment);

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
        $this->extractedText = '';
        $this->showExtractedText = false;
    }

    public function render()
    {
        return view('livewire.ai-resume-analyzer')->layout('layouts.app');
    }
}
