<?php

namespace App\Services;

use App\Models\Candidate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class TranslationService
{
    private string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
    }

    /**
     * Переводит данные кандидата на указанный язык
     */
    public function translateCandidate(Candidate $candidate, string $targetLanguage): array
    {
        $cacheKey = "candidate_translation_{$candidate->id}_{$targetLanguage}_v2";

        // Проверяем кэш (храним перевод 24 часа)
        $cached = Cache::get($cacheKey);
        if ($cached) {
            return $cached;
        }

        // Собираем все текстовые данные для перевода
        $dataToTranslate = $this->collectTranslatableData($candidate);

        // Переводим через GPT-4o
        $translatedData = $this->translateWithGpt4o($dataToTranslate, $targetLanguage);

        // Кэшируем результат на 24 часа
        Cache::put($cacheKey, $translatedData, now()->addHours(24));

        return $translatedData;
    }

    /**
     * Собирает все текстовые данные кандидата для перевода
     */
    private function collectTranslatableData(Candidate $candidate): array
    {
        $data = [
            // Основная информация
            'full_name' => $candidate->full_name,
            'gender' => $candidate->gender,
            'nationality' => $candidate->nationality,
            'city' => $candidate->city,
            'religion' => $candidate->religion,
            'marital_status' => $candidate->marital_status,

            // Образование
            'school_name' => $candidate->school_name,
            'school_city' => $candidate->school_city,
            'universities' => $candidate->universities ?? [],

            // Опыт работы
            'work_experience' => $candidate->work_experience ?? [],
            'awards' => $candidate->awards ?? [],

            // Интересы
            'hobbies' => $candidate->hobbies,
            'interests' => $candidate->interests,
            'favorite_sports' => $candidate->favorite_sports,
            'visited_countries' => $candidate->visited_countries ?? [],

            // Семья
            'parents' => $candidate->parents ?? [],
            'siblings' => $candidate->siblings ?? [],
            'children' => $candidate->children ?? [],

            // Навыки
            'computer_skills' => $candidate->computer_skills,
            'languages' => $candidate->languages ?? [],

            // MBTI
            'mbti_full_name' => $candidate->mbti_full_name,

            // Дополнительно
            'goals_5_years' => $candidate->goals_5_years,
            'ideal_job' => $candidate->ideal_job,
            'strengths' => $candidate->strengths,
            'weaknesses' => $candidate->weaknesses,
            'achievements' => $candidate->achievements,
        ];

        return $data;
    }

    /**
     * Переводит данные через GPT-4o
     */
    private function translateWithGpt4o(array $data, string $targetLanguage): array
    {
        $languageNames = [
            'ru' => 'Russian',
            'en' => 'English',
            'ar' => 'Arabic',
        ];

        $targetLangName = $languageNames[$targetLanguage] ?? 'English';

        $prompt = "You are a professional translator. Translate the following JSON data to {$targetLangName}.
Keep the JSON structure exactly the same, only translate the text values.
Do not translate:
- Names of people (full_name) - keep original
- Email addresses
- Phone numbers
- URLs
- Dates and years
- Numbers
- University names (keep original but you can add translation in parentheses)
- Company names (keep original)

For arrays, translate each element.
For nested objects, translate all string values.

Important: Return ONLY valid JSON, no markdown formatting, no code blocks.

JSON to translate:
" . json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->timeout(120)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are a professional translator. You translate text accurately while preserving the original meaning and tone. Always respond with valid JSON only.'
                    ],
                    [
                        'role' => 'user',
                        'content' => $prompt
                    ]
                ],
                'temperature' => 0.3,
                'max_tokens' => 8000,
            ]);

            if (!$response->successful()) {
                Log::error('GPT-4o translation failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return $data; // Возвращаем оригинальные данные при ошибке
            }

            $result = $response->json();
            $content = $result['choices'][0]['message']['content'] ?? '';

            // Очищаем от возможных markdown-блоков
            $content = preg_replace('/^```json\s*/', '', $content);
            $content = preg_replace('/^```\s*/', '', $content);
            $content = preg_replace('/\s*```$/', '', $content);
            $content = trim($content);

            $translatedData = json_decode($content, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Failed to parse GPT-4o translation response', [
                    'error' => json_last_error_msg(),
                    'content' => substr($content, 0, 500)
                ]);
                return $data;
            }

            return $translatedData;

        } catch (\Exception $e) {
            Log::error('Translation exception', [
                'message' => $e->getMessage()
            ]);
            return $data;
        }
    }

    /**
     * Создаёт переведённый объект-обёртку для кандидата
     */
    public function createTranslatedCandidateWrapper(Candidate $candidate, string $targetLanguage): object
    {
        $translatedData = $this->translateCandidate($candidate, $targetLanguage);

        // Создаём объект с переведёнными данными, но с оригинальными связями
        return new TranslatedCandidate($candidate, $translatedData, $targetLanguage);
    }

    /**
     * Определяет исходный язык анкеты
     */
    public function detectLanguage(Candidate $candidate): string
    {
        $text = $candidate->hobbies . ' ' . $candidate->interests . ' ' . $candidate->city;

        // Простая эвристика на основе символов
        if (preg_match('/[\x{0600}-\x{06FF}]/u', $text)) {
            return 'ar'; // Arabic
        }

        if (preg_match('/[а-яА-ЯёЁ]/u', $text)) {
            return 'ru'; // Russian
        }

        return 'en'; // Default to English
    }

    /**
     * Получает доступные языки для перевода
     */
    public function getAvailableLanguages(Candidate $candidate): array
    {
        $sourceLanguage = $this->detectLanguage($candidate);

        $allLanguages = [
            'ru' => 'Русский',
            'en' => 'English',
            'ar' => 'العربية',
        ];

        // Убираем исходный язык из списка
        unset($allLanguages[$sourceLanguage]);

        return $allLanguages;
    }
}

/**
 * Обёртка для переведённого кандидата
 */
class TranslatedCandidate
{
    private Candidate $original;
    private array $translatedData;
    private string $language;

    public function __construct(Candidate $original, array $translatedData, string $language)
    {
        $this->original = $original;
        $this->translatedData = $translatedData;
        $this->language = $language;
    }

    public function __get($name)
    {
        // Сначала проверяем переведённые данные
        if (array_key_exists($name, $this->translatedData)) {
            return $this->translatedData[$name];
        }

        // Иначе возвращаем оригинальное значение
        return $this->original->$name;
    }

    public function __isset($name)
    {
        return isset($this->translatedData[$name]) || isset($this->original->$name);
    }

    public function __call($name, $arguments)
    {
        return $this->original->$name(...$arguments);
    }

    public function getOriginal(): Candidate
    {
        return $this->original;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    // Прокси для связей
    public function gallupTalents()
    {
        return $this->original->gallupTalents();
    }

    public function gallupReports()
    {
        return $this->original->gallupReports();
    }

    public function user()
    {
        return $this->original->user();
    }
}
