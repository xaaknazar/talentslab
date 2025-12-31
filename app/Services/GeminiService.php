<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    private string $apiKey;
    private string $model;
    private string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.gemini.api_key', '');
        $this->model = config('services.gemini.model', 'gemini-2.0-flash');
        $this->baseUrl = 'https://generativelanguage.googleapis.com/v1beta';
    }

    /**
     * Анализирует резюме и формирует аналитический отчёт
     */
    public function analyzeResume(string $resumeText, ?string $userComment = null): array
    {
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'error' => 'API ключ Gemini не настроен. Добавьте GEMINI_API_KEY в .env файл.',
            ];
        }

        $systemPrompt = $this->getSystemPrompt();
        $userPrompt = $this->buildUserPrompt($resumeText, $userComment);

        try {
            $response = Http::timeout(120)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                ])
                ->post("{$this->baseUrl}/models/{$this->model}:generateContent?key={$this->apiKey}", [
                    'contents' => [
                        [
                            'role' => 'user',
                            'parts' => [
                                ['text' => $systemPrompt . "\n\n" . $userPrompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'topK' => 40,
                        'topP' => 0.95,
                        'maxOutputTokens' => 8192,
                    ],
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;

                if ($text) {
                    return [
                        'success' => true,
                        'report' => $text,
                    ];
                }

                return [
                    'success' => false,
                    'error' => 'Не удалось получить ответ от модели',
                ];
            }

            $errorMessage = $response->json()['error']['message'] ?? 'Неизвестная ошибка API';
            Log::error('Gemini API Error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            return [
                'success' => false,
                'error' => "Ошибка API: {$errorMessage}",
            ];
        } catch (\Exception $e) {
            Log::error('Gemini Service Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'error' => 'Ошибка при обращении к API: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Системный промпт для анализа резюме
     */
    private function getSystemPrompt(): string
    {
        return <<<'PROMPT'
Ты — senior HR-аналитик, executive-консультант и карьерный стратег с опытом работы с управленцами и high-potential специалистами.

Тебе передаётся полный текст резюме пользователя в формате PDF и, опционально, дополнительные комментарии пользователя.

Твоя задача — НЕ пересказывать резюме и НЕ использовать Gallup/CliftonStrengths напрямую, НЕ считать проценты и НЕ воспроизводить тесты, а интерпретировать человека через его опыт, формулировки, карьерные решения и контекст.

Формируй структурированный аналитический отчёт в деловом, спокойном, профессиональном тоне без инфоцыганства и мотивационных лозунгов.

СТРУКТУРА ОТЧЁТА (строго соблюдай):

## 1. Общий профессиональный профиль
- Кто этот человек как специалист
- Основной вектор карьеры
- Тип роли (исполнитель / эксперт / управленец)
- Логика переходов между позициями
- Степень осознанности карьерного пути

## 2. Профессиональные функции
Анализ на основе резюме по направлениям:
- Аналитика
- Продажи
- Клиентоориентированность
- Исследования
- Маркетинг
- HR
- Операционный менеджмент
- Проектный менеджмент
- Стратегия
- Коучинг
- Технологии

Описывай силу и зрелость функций, НЕ используй проценты.

## 3. Потенциал к управлению
- Специалист vs Менеджер
- Потенциал роста
- Готовность брать ответственность
- Переговорный потенциал
- Умение вдохновлять
- Эмоциональная зрелость

## 4. Уровни мышления
Делай выводы по языку и масштабу задач в резюме:
- Операционное мышление
- Тактическое мышление
- Стратегическое мышление
- Критическое мышление
- Инновационное мышление

## 5. Стили менеджмента (прогноз)
Определи основной и вспомогательные стили:
- Демократический
- Обучающий
- Авторитарный
- Товарищеский
- Амбициозный

## 6. Поведенческие ограничения в управлении
Экологично выявляй возможные ограничения:
- Склонность к микроменеджменту
- Гиперответственность
- Страх делегирования
- Перфекционизм
- Избегание конфликтов

## 7. Шкала «Радикальная прямота»
- Эмпатия (оценка)
- Прямота (оценка)
- Потенциальные перекосы и управленческие риски

## 8. Интегративный психопрофиль
Аналитическая гипотеза БЕЗ буквенных диагнозов:
- Интерпретация типов интеллекта по Гарднеру
- Поведенческий паттерн (на основе MBTI-логики)
- Управленческий психотип

## 9. Итоговые выводы
- Сильные стороны
- Зоны роста
- Оптимальные роли
- Рекомендации на 6–12 месяцев

ТРЕБОВАНИЯ К СТИЛЮ:
- Деловой аналитический язык
- Без воды и общих фраз
- Без обращения на «ты»
- Без эмодзи
- Без ссылок на исходный PDF
- Чёткие формулировки
- Тон: консультант уровня senior / partner
- Пиши как платное консультативное заключение для руководителя, HR или самого кандидата
PROMPT;
    }

    /**
     * Формирует пользовательский промпт
     */
    private function buildUserPrompt(string $resumeText, ?string $userComment = null): string
    {
        $prompt = "ТЕКСТ РЕЗЮМЕ:\n\n{$resumeText}";

        if (!empty($userComment)) {
            $prompt .= "\n\n---\n\nДОПОЛНИТЕЛЬНЫЙ ЗАПРОС/КОММЕНТАРИЙ ОТ ПОЛЬЗОВАТЕЛЯ:\n{$userComment}";
        }

        $prompt .= "\n\n---\n\nСформируй полный аналитический отчёт согласно структуре.";

        return $prompt;
    }

    /**
     * Извлекает текст из PDF файла
     */
    public function extractTextFromPdf(string $pdfPath): ?string
    {
        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($pdfPath);
            $text = $pdf->getText();

            // Очистка текста от лишних пробелов и переносов
            $text = preg_replace('/\s+/', ' ', $text);
            $text = preg_replace('/\n{3,}/', "\n\n", $text);

            return trim($text);
        } catch (\Exception $e) {
            Log::error('PDF Parse Error', [
                'path' => $pdfPath,
                'message' => $e->getMessage(),
            ]);

            return null;
        }
    }
}
