<?php
namespace App\Filament\Pages;

use App\Models\Candidate;
use App\Services\TranslationService;
use Filament\Pages\Page;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Url;

class ViewCandidatePdf extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static string $view = 'filament.pages.view-candidate-pdf';

    public ?Candidate $candidate = null;
    public string $url;
    public string $type;
    public string $sourceLanguage = 'ru';
    public ?string $selectedLanguage = null;
    public array $availableLanguages = [];
    public bool $isTranslating = false;
    public ?string $translatedUrl = null;

    public function mount(Candidate $candidate, string $type)
    {
        $this->candidate = $candidate;
        $this->type = strtoupper($type);

        // Определяем исходный язык и доступные переводы
        $translationService = app(TranslationService::class);
        $this->sourceLanguage = $translationService->detectLanguage($candidate);
        $this->availableLanguages = $translationService->getAvailableLanguages($candidate);

        $this->generateOriginalUrl($type);
    }

    private function generateOriginalUrl(string $type): void
    {
        if ($type == 'anketa') {
            $gallupController = app(\App\Http\Controllers\GallupController::class);
            $pdfPath = $gallupController->generateAnketaPdfOnDemand($this->candidate);
            $this->url = Storage::disk('public')->url($pdfPath);
        } elseif ($type == 'anketa-reduced') {
            $gallupController = app(\App\Http\Controllers\GallupController::class);
            $pdfPath = $gallupController->generateAnketaPdfOnDemand($this->candidate, 'reduced');
            $this->url = Storage::disk('public')->url($pdfPath);
        } else {
            $report = $this->candidate->gallupReportByType($type);
            abort_if(!$report || !Storage::disk('public')->exists($report->pdf_file), 404);
            $this->url = Storage::url($report->pdf_file);
        }
    }

    public function translateAndDownload(): void
    {
        if (!$this->selectedLanguage) {
            Notification::make()
                ->title('Выберите язык для перевода')
                ->warning()
                ->send();
            return;
        }

        $this->isTranslating = true;

        try {
            $gallupController = app(\App\Http\Controllers\GallupController::class);
            $pdfPath = $gallupController->generateTranslatedAnketaPdf(
                $this->candidate,
                $this->selectedLanguage,
                strtolower($this->type) === 'anketa-reduced' ? 'reduced' : 'full'
            );

            $this->translatedUrl = Storage::disk('public')->url($pdfPath);

            Notification::make()
                ->title('Перевод готов!')
                ->body('Переведённая анкета сгенерирована. Нажмите "Скачать перевод" для загрузки.')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Ошибка перевода')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }

        $this->isTranslating = false;
    }

    public function downloadTranslated(): void
    {
        if ($this->translatedUrl) {
            $this->dispatch('download-file', url: $this->translatedUrl);
        }
    }

    public function downloadOriginal(): void
    {
        $this->dispatch('download-file', url: $this->url);
    }

    public function getTitle(): string
    {
        return "{$this->candidate->full_name} — {$this->type} отчет";
    }

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function getSlug(): string
    {
        return 'view-candidate-pdf/{candidate}/{type}';
    }

    protected function getLanguageLabel(string $code): string
    {
        $labels = [
            'ru' => 'Русский',
            'en' => 'English',
            'ar' => 'العربية',
        ];
        return $labels[$code] ?? $code;
    }
}
