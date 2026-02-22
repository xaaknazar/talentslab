<?php

namespace App\Jobs;

use App\Http\Controllers\GallupController;
use App\Models\Candidate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GenerateAnketaPdf implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $candidate;

    /**
     * Create a new job instance.
     */
    public function __construct(Candidate $candidate)
    {
        $this->candidate = $candidate;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Увеличиваем лимит памяти для генерации PDF
        ini_set('memory_limit', '512M');

        try {
            Log::info('Starting anketa PDF generation', [
                'candidate_id' => $this->candidate->id,
                'candidate_name' => $this->candidate->full_name
            ]);

            // Обновляем модель
            $this->candidate->refresh();

            $gallupController = app(GallupController::class);
            $pdfPath = $gallupController->generateAnketaPdfOnDemand($this->candidate);

            // Сохраняем путь к anketa_pdf в базе данных
            // Копируем из temp_anketas в постоянное хранилище
            $permanentPath = "anketas/anketa_{$this->candidate->id}.pdf";

            if (Storage::disk('public')->exists($pdfPath)) {
                // Создаём директорию если не существует
                Storage::disk('public')->makeDirectory('anketas');

                // Удаляем старый файл если есть
                if (Storage::disk('public')->exists($permanentPath)) {
                    Storage::disk('public')->delete($permanentPath);
                }

                Storage::disk('public')->copy($pdfPath, $permanentPath);
                Storage::disk('public')->delete($pdfPath);

                Candidate::where('id', $this->candidate->id)->update(['anketa_pdf' => $permanentPath]);

                Log::info('Anketa PDF generated successfully', [
                    'candidate_id' => $this->candidate->id,
                    'path' => $permanentPath
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error generating anketa PDF', [
                'candidate_id' => $this->candidate->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Anketa PDF generation job failed', [
            'candidate_id' => $this->candidate->id,
            'error' => $exception->getMessage()
        ]);
    }
}
