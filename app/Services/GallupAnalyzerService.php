<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Smalot\PdfParser\Parser;

class GallupAnalyzerService
{
    private string $apiKey;
    private string $model;
    private string $baseUrl;

    /**
     * Список всех 34 талантов Gallup на английском
     */
    private array $allTalentKeys = [
        'Achiever', 'Activator', 'Adaptability', 'Analytical', 'Arranger',
        'Belief', 'Command', 'Communication', 'Competition', 'Connectedness',
        'Consistency', 'Context', 'Deliberative', 'Developer', 'Discipline',
        'Empathy', 'Focus', 'Futuristic', 'Harmony', 'Ideation',
        'Includer', 'Individualization', 'Input', 'Intellection', 'Learner',
        'Maximizer', 'Positivity', 'Relator', 'Responsibility', 'Restorative',
        'Self-Assurance', 'Significance', 'Strategic', 'Woo'
    ];

    /**
     * Словарь перевода русских названий талантов на английский
     */
    private array $russianToEnglish = [
        // Исполнение (Executing)
        'достижение' => 'Achiever',
        'достигатор' => 'Achiever',
        'упорядоченность' => 'Arranger',
        'организатор' => 'Arranger',
        'аранжировщик' => 'Arranger',
        'вера' => 'Belief',
        'убеждённость' => 'Belief',
        'беспристрастность' => 'Consistency',
        'последовательность' => 'Consistency',
        'осмотрительность' => 'Deliberative',
        'осторожность' => 'Deliberative',
        'дисциплина' => 'Discipline',
        'дисциплинированность' => 'Discipline',
        'сфокусированность' => 'Focus',
        'фокус' => 'Focus',
        'концентрация' => 'Focus',
        'ответственность' => 'Responsibility',
        'восстановление' => 'Restorative',
        'исправление' => 'Restorative',

        // Влияние (Influencing)
        'активатор' => 'Activator',
        'командование' => 'Command',
        'распорядитель' => 'Command',
        'коммуникация' => 'Communication',
        'общение' => 'Communication',
        'соревновательность' => 'Competition',
        'конкуренция' => 'Competition',
        'максимизатор' => 'Maximizer',
        'развиватель' => 'Maximizer',
        'уверенность' => 'Self-Assurance',
        'самоуверенность' => 'Self-Assurance',
        'значимость' => 'Significance',
        'обаяние' => 'Woo',
        'очарование' => 'Woo',
        'располагающий' => 'Woo',

        // Построение отношений (Relationship Building)
        'адаптивность' => 'Adaptability',
        'гибкость' => 'Adaptability',
        'связанность' => 'Connectedness',
        'связность' => 'Connectedness',
        'взаимосвязанность' => 'Connectedness',
        'развитие' => 'Developer',
        'развитие других' => 'Developer',
        'эмпатия' => 'Empathy',
        'сопереживание' => 'Empathy',
        'гармония' => 'Harmony',
        'включённость' => 'Includer',
        'включенность' => 'Includer',
        'включающий' => 'Includer',
        'индивидуализация' => 'Individualization',
        'позитивность' => 'Positivity',
        'позитив' => 'Positivity',
        'отношения' => 'Relator',
        'общительность' => 'Relator',

        // Стратегическое мышление (Strategic Thinking)
        'аналитик' => 'Analytical',
        'аналитическое мышление' => 'Analytical',
        'контекст' => 'Context',
        'прошлое' => 'Context',
        'будущее' => 'Futuristic',
        'футуристичность' => 'Futuristic',
        'генератор идей' => 'Ideation',
        'идеи' => 'Ideation',
        'креативность' => 'Ideation',
        'коллекционер' => 'Input',
        'накопитель' => 'Input',
        'собиратель' => 'Input',
        'мышление' => 'Intellection',
        'интеллект' => 'Intellection',
        'размышления' => 'Intellection',
        'ученик' => 'Learner',
        'обучаемость' => 'Learner',
        'любознательность' => 'Learner',
        'стратег' => 'Strategic',
        'стратегическое мышление' => 'Strategic',
        'стратегия' => 'Strategic',
    ];

    /**
     * Альтернативные переводы, которые может давать GPT-4o
     */
    private array $alternativeTranslations = [
        'charm' => 'Woo',
        'confidence' => 'Self-Assurance',
        'inclusion' => 'Includer',
        'future' => 'Futuristic',
        'thinking' => 'Intellection',
        'strategy' => 'Strategic',
        'ideas' => 'Ideation',
        'input' => 'Input',
        'empathy' => 'Empathy',
        'context' => 'Context',
        'analytical' => 'Analytical',
        'harmony' => 'Harmony',
        'relations' => 'Relator',
        'relationships' => 'Relator',
        'development' => 'Developer',
        'individualization' => 'Individualization',
        'connectedness' => 'Connectedness',
        'adaptability' => 'Adaptability',
        'positivity' => 'Positivity',
        'competition' => 'Competition',
        'communication' => 'Communication',
        'command' => 'Command',
        'maximizer' => 'Maximizer',
        'activator' => 'Activator',
        'significance' => 'Significance',
        'deliberative' => 'Deliberative',
        'belief' => 'Belief',
        'arranger' => 'Arranger',
        'focus' => 'Focus',
        'achiever' => 'Achiever',
        'consistency' => 'Consistency',
        'discipline' => 'Discipline',
        'responsibility' => 'Responsibility',
        'restorative' => 'Restorative',
        'learner' => 'Learner',
        'futuristic' => 'Futuristic',
        'ideation' => 'Ideation',
        'strategic' => 'Strategic',
        'developer' => 'Developer',
        'includer' => 'Includer',
        'relator' => 'Relator',
        'woo' => 'Woo',
        'self-assurance' => 'Self-Assurance',
    ];

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key', '');
        $this->model = 'gpt-4o'; // Используем GPT-4o для лучшего качества
        $this->baseUrl = 'https://api.openai.com/v1';
    }

    /**
     * Анализирует файл Gallup (PDF или изображение) и возвращает массив талантов
     *
     * @param string $filePath Путь к файлу
     * @param string $fileType Тип файла: 'pdf' или 'image'
     * @return array ['success' => bool, 'talents' => array|null, 'error' => string|null]
     */
    public function analyzeGallupFile(string $filePath, string $fileType = 'pdf'): array
    {
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'talents' => null,
                'error' => 'API ключ OpenAI не настроен. Добавьте OPENAI_API_KEY в .env файл.',
            ];
        }

        if (!file_exists($filePath)) {
            return [
                'success' => false,
                'talents' => null,
                'error' => 'Файл не найден: ' . $filePath,
            ];
        }

        try {
            // Анализируем файл через GPT-4o
            $rawTalents = $this->analyzeWithGpt4o($filePath, $fileType);

            if (empty($rawTalents)) {
                return [
                    'success' => false,
                    'talents' => null,
                    'error' => 'Не удалось извлечь таланты из файла. Попробуйте другой файл или изображение лучшего качества.',
                ];
            }

            // Нормализуем результат
            $normalizedTalents = $this->normalizeTalents($rawTalents);

            // Проверяем количество талантов
            if (count($normalizedTalents) !== 34) {
                return [
                    'success' => false,
                    'talents' => null,
                    'error' => "Найдено " . count($normalizedTalents) . " из 34 талантов. Для создания отчетов требуются все 34 таланта.",
                ];
            }

            // Проверяем уникальность номеров
            $usedNumbers = array_values($normalizedTalents);
            if (count($usedNumbers) !== count(array_unique($usedNumbers))) {
                $duplicates = array_diff_assoc($usedNumbers, array_unique($usedNumbers));
                return [
                    'success' => false,
                    'talents' => null,
                    'error' => "Обнаружены дублирующиеся номера талантов. GPT-4o неточно прочитал файл.",
                ];
            }

            // Проверяем диапазон номеров
            if (min($usedNumbers) < 1 || max($usedNumbers) > 34) {
                return [
                    'success' => false,
                    'talents' => null,
                    'error' => "Обнаружены некорректные номера талантов. Таланты должны иметь номера от 1 до 34.",
                ];
            }

            // Сортируем по номеру и возвращаем массив названий
            arsort($normalizedTalents);
            $sortedTalents = [];
            for ($i = 1; $i <= 34; $i++) {
                $talentName = array_search($i, $normalizedTalents);
                if ($talentName !== false) {
                    $sortedTalents[] = $talentName;
                }
            }

            return [
                'success' => true,
                'talents' => $sortedTalents,
                'raw' => $normalizedTalents,
                'error' => null,
            ];

        } catch (\Exception $e) {
            Log::error('GallupAnalyzerService Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'talents' => null,
                'error' => 'Ошибка анализа файла: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Анализирует файл через GPT-4o
     */
    private function analyzeWithGpt4o(string $filePath, string $fileType): array
    {
        if ($fileType === 'pdf') {
            return $this->analyzePdfWithGpt4o($filePath);
        } else {
            return $this->analyzeImageWithGpt4oVision($filePath);
        }
    }

    /**
     * Анализирует PDF через GPT-4o (извлекает текст и отправляет на анализ)
     */
    private function analyzePdfWithGpt4o(string $filePath): array
    {
        // Извлекаем текст из PDF
        $extractedText = $this->extractTextFromPdf($filePath);

        if (empty($extractedText) || strlen($extractedText) < 100) {
            Log::warning('Слишком мало текста извлечено из PDF', ['length' => strlen($extractedText ?? '')]);
            return [];
        }

        // Обрезаем текст для экономии токенов
        $maxChars = 12000;
        if (strlen($extractedText) > $maxChars) {
            $content = "Gallup отчет (обрезано):\n\n" . substr($extractedText, 0, $maxChars) . "\n\n[...текст обрезан...]";
        } else {
            $content = "Gallup отчет:\n\n" . $extractedText;
        }

        $talentList = implode(', ', $this->allTalentKeys);

        $systemPrompt = <<<PROMPT
Найди в Gallup отчете все 34 таланта с номерами 1-34.

СПИСОК ТАЛАНТОВ: {$talentList}

ЗАДАЧА: Извлеки пары "талант:номер" из текста. Таланты могут быть на русском или английском языке.
ФОРМАТ ОТВЕТА: только JSON без комментариев
ПРИМЕР: {"Achiever": 1, "Activator": 2}

Если талант на русском - переведи на английский.
Если талант не найден - не включай в результат.
PROMPT;

        Log::info('Отправляем запрос к GPT-4o для PDF...');

        $response = Http::timeout(120)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])
            ->post("{$this->baseUrl}/chat/completions", [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    ['role' => 'user', 'content' => $content],
                ],
                'temperature' => 0,
                'max_tokens' => 800,
            ]);

        return $this->parseGptResponse($response);
    }

    /**
     * Анализирует изображение через GPT-4o Vision
     */
    private function analyzeImageWithGpt4oVision(string $filePath): array
    {
        // Читаем и кодируем изображение в base64
        $imageData = file_get_contents($filePath);
        $base64Image = base64_encode($imageData);

        // Определяем MIME тип
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filePath);
        finfo_close($finfo);

        // Примеры переводов для промпта
        $translationExamples = array_slice($this->russianToEnglish, 0, 8, true);
        $examplesText = implode(", ", array_map(fn($ru, $en) => "$ru -> $en", array_keys($translationExamples), $translationExamples));

        Log::info('Отправляем запрос к GPT-4o Vision для изображения...');

        $systemPrompt = <<<PROMPT
Анализируй Gallup таблицу построчно, слева направо, сверху вниз.

МЕТОД ЧТЕНИЯ:
1. Начни с верхнего левого угла
2. Читай каждую ячейку: сначала НОМЕР (большая цифра), потом НАЗВАНИЕ под ним
3. Переходи слева направо, затем на следующую строку
4. Проверь, что нашел ровно 34 пары номер-талант

СТРУКТУРА ТАБЛИЦЫ (4 колонки):
[ИСПОЛНЕНИЕ] [ВЛИЯНИЕ] [ВЗАИМООТНОШЕНИЯ] [СТРАТЕГИЧЕСКОЕ МЫШЛЕНИЕ]

ПЕРЕВОДИ русские названия на английский:
{$examplesText}

ПРОВЕРКА: В итоге должны быть номера 1,2,3...34 (все разные)

ФОРМАТ: {"talent_name": number}
ТОЛЬКО JSON, без текста!
PROMPT;

        $response = Http::timeout(120)
            ->withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])
            ->post("{$this->baseUrl}/chat/completions", [
                'model' => $this->model,
                'messages' => [
                    ['role' => 'system', 'content' => $systemPrompt],
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => 'Читай таблицу методично: строка за строкой, ячейка за ячейкой. В каждой ячейке большая цифра - это номер, под ней - название таланта. Например: если видишь большую цифру "7" и под ней "Генератор идей", то это "Ideation": 7'
                            ],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => "data:{$mimeType};base64,{$base64Image}"
                                ]
                            ]
                        ]
                    ],
                ],
                'temperature' => 0,
                'max_tokens' => 1000,
            ]);

        return $this->parseGptResponse($response);
    }

    /**
     * Парсит ответ от GPT и извлекает JSON с талантами
     */
    private function parseGptResponse($response): array
    {
        if (!$response->successful()) {
            $errorMessage = $response->json()['error']['message'] ?? 'Неизвестная ошибка API';
            Log::error('GPT-4o API Error', [
                'status' => $response->status(),
                'error' => $errorMessage,
            ]);
            return [];
        }

        $data = $response->json();
        $gptResponse = $data['choices'][0]['message']['content'] ?? '';

        Log::info('GPT-4o ответ (первые 300 символов): ' . substr($gptResponse, 0, 300));

        if (empty($gptResponse)) {
            Log::error('GPT-4o вернул пустой ответ');
            return [];
        }

        // Ищем JSON в ответе
        if (preg_match('/\{.*\}/s', $gptResponse, $matches)) {
            $jsonStr = $matches[0];
            try {
                $talents = json_decode($jsonStr, true, 512, JSON_THROW_ON_ERROR);
                Log::info('GPT-4o вернул ' . count($talents) . ' талантов');
                return $talents;
            } catch (\JsonException $e) {
                Log::error('Ошибка парсинга JSON от GPT-4o: ' . $e->getMessage());
                return [];
            }
        }

        Log::error('JSON не найден в ответе GPT-4o: ' . $gptResponse);
        return [];
    }

    /**
     * Нормализует названия талантов и переводит с русского на английский
     */
    private function normalizeTalents(array $rawTalents): array
    {
        $normalized = [];
        $keyMapping = array_combine(
            array_map('strtolower', $this->allTalentKeys),
            $this->allTalentKeys
        );

        foreach ($rawTalents as $talentName => $talentNumber) {
            if (!is_int($talentNumber) && !is_string($talentNumber)) {
                continue;
            }

            $talentNumber = (int) $talentNumber;
            if ($talentNumber < 1 || $talentNumber > 34) {
                continue;
            }

            $normalizedName = trim($talentName);
            $lowerName = mb_strtolower($normalizedName, 'UTF-8');

            // 1. Точное совпадение с английским названием
            if (in_array($normalizedName, $this->allTalentKeys)) {
                $normalized[$normalizedName] = $talentNumber;
                continue;
            }

            // 2. Проверяем русский перевод
            if (isset($this->russianToEnglish[$lowerName])) {
                $englishName = $this->russianToEnglish[$lowerName];
                $normalized[$englishName] = $talentNumber;
                Log::info("Переведен талант: {$normalizedName} -> {$englishName}");
                continue;
            }

            // 3. Проверяем альтернативные переводы GPT-4o
            if (isset($this->alternativeTranslations[$lowerName])) {
                $englishName = $this->alternativeTranslations[$lowerName];
                $normalized[$englishName] = $talentNumber;
                Log::info("Исправлен перевод GPT-4o: {$normalizedName} -> {$englishName}");
                continue;
            }

            // 4. Поиск по нижнему регистру (английский)
            if (isset($keyMapping[$lowerName])) {
                $normalized[$keyMapping[$lowerName]] = $talentNumber;
                continue;
            }

            // 5. Fuzzy matching для русских названий
            foreach ($this->russianToEnglish as $russian => $english) {
                similar_text($lowerName, $russian, $percent);
                if ($percent > 80) {
                    $normalized[$english] = $talentNumber;
                    Log::info("Найден похожий русский талант: {$normalizedName} -> {$english} ({$percent}%)");
                    break;
                }
            }

            // 6. Fuzzy matching для английских названий
            if (!isset($normalized[$normalizedName])) {
                foreach ($this->allTalentKeys as $talent) {
                    similar_text($lowerName, strtolower($talent), $percent);
                    if ($percent > 70) {
                        $normalized[$talent] = $talentNumber;
                        Log::info("Найден похожий английский талант: {$normalizedName} -> {$talent} ({$percent}%)");
                        break;
                    }
                }
            }

            if (!isset($normalized[$normalizedName]) && !in_array($talentNumber, $normalized)) {
                Log::warning("Не удалось сопоставить талант: {$normalizedName}");
            }
        }

        return $normalized;
    }

    /**
     * Извлекает текст из PDF файла
     */
    private function extractTextFromPdf(string $filePath): ?string
    {
        try {
            $parser = new Parser();
            $pdf = $parser->parseFile($filePath);
            $text = '';

            foreach ($pdf->getPages() as $page) {
                $pageText = $page->getText();
                if ($pageText) {
                    $text .= $pageText . "\n";
                }
            }

            return trim($text);
        } catch (\Exception $e) {
            Log::error('Ошибка извлечения текста из PDF: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Определяет тип файла по расширению или MIME
     */
    public function detectFileType(string $filePath): string
    {
        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        if ($extension === 'pdf') {
            return 'pdf';
        }

        if (in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'])) {
            return 'image';
        }

        // Fallback - проверяем MIME тип
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $filePath);
        finfo_close($finfo);

        if ($mimeType === 'application/pdf') {
            return 'pdf';
        }

        if (str_starts_with($mimeType, 'image/')) {
            return 'image';
        }

        return 'unknown';
    }

    /**
     * Проверяет, является ли файл валидным Gallup отчетом (быстрая проверка)
     * Возвращает true для PDF с ключевыми признаками Gallup или для изображений
     */
    public function isValidGallupFile(string $filePath): bool
    {
        $fileType = $this->detectFileType($filePath);

        if ($fileType === 'unknown') {
            return false;
        }

        // Изображения принимаем без проверки - GPT-4o сам разберется
        if ($fileType === 'image') {
            return true;
        }

        // Для PDF проверяем базовые признаки
        try {
            $text = $this->extractTextFromPdf($filePath);

            if (empty($text)) {
                return false;
            }

            // Проверяем наличие хотя бы нескольких талантов (на русском или английском)
            $talentPatterns = [
                '/\b(Achiever|Activator|Analytical|Strategic|Learner|Ideation)\b/i',
                '/\b(достижение|активатор|аналитик|стратег|ученик|генератор идей)\b/ui',
                '/\b([1-9]|[1-2][0-9]|3[0-4])\.\s+\w+/u', // Нумерованный список талантов
            ];

            foreach ($talentPatterns as $pattern) {
                if (preg_match($pattern, $text)) {
                    return true;
                }
            }

            // Проверяем наличие Gallup или CliftonStrengths
            if (stripos($text, 'Gallup') !== false || stripos($text, 'CliftonStrengths') !== false) {
                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Ошибка проверки Gallup файла: ' . $e->getMessage());
            return false;
        }
    }
}
