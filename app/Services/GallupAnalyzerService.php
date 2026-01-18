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
     * Официальные переводы Gallup + альтернативные варианты написания
     */
    private array $russianToEnglish = [
        // Официальные переводы Gallup (из RUSSIAN_TO_ENGLISH_TALENTS)
        'достижение' => 'Achiever',
        'дисциплинированность' => 'Discipline',
        'организатор' => 'Arranger',
        'сосредоточенность' => 'Focus',
        'убеждение' => 'Belief',
        'ответственность' => 'Responsibility',
        'последовательность' => 'Consistency',
        'восстановление' => 'Restorative',
        'осмотрительность' => 'Deliberative',
        'катализатор' => 'Activator',
        'максимизатор' => 'Maximizer',
        'распорядитель' => 'Command',
        'уверенность' => 'Self-Assurance',
        'коммуникация' => 'Communication',
        'значимость' => 'Significance',
        'конкуренция' => 'Competition',
        'обаяние' => 'Woo',
        'приспособляемость' => 'Adaptability',
        'включенность' => 'Includer',
        'взаимосвязанность' => 'Connectedness',
        'индивидуализация' => 'Individualization',
        'развитие' => 'Developer',
        'позитивность' => 'Positivity',
        'эмпатия' => 'Empathy',
        'отношения' => 'Relator',
        'гармония' => 'Harmony',
        'аналитик' => 'Analytical',
        'вклад' => 'Input',
        'контекст' => 'Context',
        'мышление' => 'Intellection',
        'будущее' => 'Futuristic',
        'ученик' => 'Learner',
        'генератор идей' => 'Ideation',
        'стратегия' => 'Strategic',

        // Альтернативные варианты написания - Исполнение (Executing)
        'достигатор' => 'Achiever',
        'достиженец' => 'Achiever',
        'упорядоченность' => 'Arranger',
        'аранжировщик' => 'Arranger',
        'вера' => 'Belief',
        'убеждённость' => 'Belief',
        'убежденность' => 'Belief',
        'беспристрастность' => 'Consistency',
        'постоянство' => 'Consistency',
        'осторожность' => 'Deliberative',
        'рассудительность' => 'Deliberative',
        'дисциплина' => 'Discipline',
        'сфокусированность' => 'Focus',
        'фокус' => 'Focus',
        'концентрация' => 'Focus',
        'исправление' => 'Restorative',
        'восстановитель' => 'Restorative',

        // Альтернативные варианты - Влияние (Influencing)
        'активатор' => 'Activator',
        'командование' => 'Command',
        'руководство' => 'Command',
        'общение' => 'Communication',
        'коммуникатор' => 'Communication',
        'соревновательность' => 'Competition',
        'состязательность' => 'Competition',
        'максимизация' => 'Maximizer',
        'самоуверенность' => 'Self-Assurance',
        'уверенность в себе' => 'Self-Assurance',
        'значительность' => 'Significance',
        'очарование' => 'Woo',
        'располагающий' => 'Woo',
        'завоеватель' => 'Woo',

        // Альтернативные варианты - Построение отношений (Relationship Building)
        'адаптивность' => 'Adaptability',
        'гибкость' => 'Adaptability',
        'связанность' => 'Connectedness',
        'связность' => 'Connectedness',
        'единение' => 'Connectedness',
        'развитие других' => 'Developer',
        'развиватель' => 'Developer',
        'воспитатель' => 'Developer',
        'сопереживание' => 'Empathy',
        'сочувствие' => 'Empathy',
        'согласие' => 'Harmony',
        'включённость' => 'Includer',
        'включающий' => 'Includer',
        'принятие' => 'Includer',
        'индивидуальность' => 'Individualization',
        'индивидуальный подход' => 'Individualization',
        'позитив' => 'Positivity',
        'оптимизм' => 'Positivity',
        'общительность' => 'Relator',
        'коммуникабельность' => 'Relator',

        // Альтернативные варианты - Стратегическое мышление (Strategic Thinking)
        'аналитическое мышление' => 'Analytical',
        'аналитичность' => 'Analytical',
        'прошлое' => 'Context',
        'история' => 'Context',
        'футуристичность' => 'Futuristic',
        'провидец' => 'Futuristic',
        'идеи' => 'Ideation',
        'креативность' => 'Ideation',
        'творчество' => 'Ideation',
        'коллекционер' => 'Input',
        'накопитель' => 'Input',
        'собиратель' => 'Input',
        'ввод' => 'Input',
        'информация' => 'Input',
        'интеллект' => 'Intellection',
        'размышления' => 'Intellection',
        'созерцание' => 'Intellection',
        'обучаемость' => 'Learner',
        'любознательность' => 'Learner',
        'обучение' => 'Learner',
        'стратег' => 'Strategic',
        'стратегическое мышление' => 'Strategic',
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
            // Сначала пробуем извлечь текст из PDF
            $result = $this->analyzePdfWithGpt4o($filePath);

            // Если текстовый анализ не дал результатов, пробуем анализ как изображение
            if (empty($result) || count($result) < 20) {
                Log::info('Текстовый анализ PDF не дал достаточно результатов, пробуем анализ как изображение');
                $imageResult = $this->analyzePdfAsImage($filePath);
                if (!empty($imageResult) && count($imageResult) > count($result)) {
                    return $imageResult;
                }
            }

            return $result;
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
Найди в Gallup/CliftonStrengths отчете все 34 таланта с их номерами (позициями от 1 до 34).

СПИСОК АНГЛИЙСКИХ НАЗВАНИЙ ТАЛАНТОВ: {$talentList}

РУССКИЕ НАЗВАНИЯ ТАЛАНТОВ И ИХ ПЕРЕВОДЫ:
- Достижение = Achiever
- Активатор = Activator
- Адаптивность = Adaptability
- Аналитик = Analytical
- Организатор = Arranger
- Вера = Belief
- Командование = Command
- Коммуникация = Communication
- Соревновательность = Competition
- Связанность = Connectedness
- Беспристрастность = Consistency
- Контекст = Context
- Осмотрительность = Deliberative
- Развитие = Developer
- Дисциплина = Discipline
- Эмпатия = Empathy
- Сфокусированность = Focus
- Будущее = Futuristic
- Гармония = Harmony
- Генератор идей = Ideation
- Включённость = Includer
- Индивидуализация = Individualization
- Коллекционер = Input
- Мышление = Intellection
- Ученик = Learner
- Максимизатор = Maximizer
- Позитивность = Positivity
- Отношения = Relator
- Ответственность = Responsibility
- Восстановление = Restorative
- Уверенность = Self-Assurance
- Значимость = Significance
- Стратег = Strategic
- Обаяние = Woo

ЗАДАЧА:
1. Найди в тексте все упоминания талантов с их номерами/позициями
2. Талант может быть указан в формате: "1. Achiever" или "Достижение - 1" или другом
3. Переводи русские названия на английский
4. Верни ТОЛЬКО JSON с парами "EnglishTalentName": number

ФОРМАТ ОТВЕТА: только JSON без комментариев
ПРИМЕР: {"Achiever": 1, "Activator": 2, "Strategic": 3}
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
Анализируй изображение Gallup/CliftonStrengths и найди все 34 таланта с их номерами (позициями 1-34).

МЕТОД ЧТЕНИЯ:
1. Если это таблица - читай построчно, слева направо, сверху вниз
2. В каждой ячейке: НОМЕР (большая цифра) + НАЗВАНИЕ таланта под ним
3. Если это список - читай номер и название для каждого таланта
4. Проверь, что нашел ровно 34 пары номер-талант

СТРУКТУРА GALLUP ТАБЛИЦЫ (4 колонки если это таблица):
[ИСПОЛНЕНИЕ/EXECUTING] [ВЛИЯНИЕ/INFLUENCING] [ВЗАИМООТНОШЕНИЯ/RELATIONSHIP] [СТРАТЕГИЧЕСКОЕ МЫШЛЕНИЕ/STRATEGIC THINKING]

РУССКИЕ НАЗВАНИЯ ТАЛАНТОВ -> АНГЛИЙСКИЙ ПЕРЕВОД:
Достижение->Achiever, Дисциплинированность->Discipline, Организатор->Arranger, Сосредоточенность->Focus,
Убеждение->Belief, Ответственность->Responsibility, Последовательность->Consistency, Восстановление->Restorative,
Осмотрительность->Deliberative, Катализатор->Activator, Активатор->Activator, Максимизатор->Maximizer,
Распорядитель->Command, Командование->Command, Уверенность->Self-Assurance, Коммуникация->Communication,
Значимость->Significance, Конкуренция->Competition, Соревновательность->Competition, Обаяние->Woo,
Приспособляемость->Adaptability, Адаптивность->Adaptability, Включенность->Includer, Включённость->Includer,
Взаимосвязанность->Connectedness, Связанность->Connectedness, Индивидуализация->Individualization,
Развитие->Developer, Позитивность->Positivity, Эмпатия->Empathy, Отношения->Relator, Гармония->Harmony,
Аналитик->Analytical, Вклад->Input, Коллекционер->Input, Накопитель->Input, Контекст->Context,
Мышление->Intellection, Будущее->Futuristic, Ученик->Learner, Генератор идей->Ideation, Стратегия->Strategic,
Стратег->Strategic, Дисциплина->Discipline, Вера->Belief, Фокус->Focus

ВАЖНО:
- Все номера должны быть разные (от 1 до 34)
- Если талант на русском - переведи в английский
- Верни ТОЛЬКО JSON без пояснений

ФОРМАТ: {"EnglishTalentName": number}
ПРИМЕР: {"Achiever": 1, "Activator": 2, "Strategic": 34}
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
     * Принимает все PDF и изображения - детальный анализ делает GPT-4o
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

        // Для PDF просто проверяем, что файл можно прочитать
        // GPT-4o проанализирует содержимое позже
        try {
            $text = $this->extractTextFromPdf($filePath);

            // Если текст извлечён - файл валиден
            // Если текста нет (сканированный PDF) - тоже принимаем, GPT-4o Vision обработает
            return true;

        } catch (\Exception $e) {
            Log::error('Ошибка проверки Gallup файла: ' . $e->getMessage());
            // Даже при ошибке принимаем файл - возможно GPT-4o сможет обработать
            return true;
        }
    }

    /**
     * Анализирует PDF как изображение через GPT-4o Vision
     * Используется для сканированных PDF без текстового слоя
     */
    public function analyzePdfAsImage(string $filePath): array
    {
        try {
            // Конвертируем первую страницу PDF в изображение
            $imagick = new \Imagick();
            $imagick->setResolution(150, 150); // Меньшее разрешение для экономии
            $imagick->readImage($filePath . '[0]'); // Первая страница
            $imagick->setImageFormat('jpg');
            $imagick->setImageCompressionQuality(85);

            $tempImagePath = sys_get_temp_dir() . '/gallup_' . uniqid() . '.jpg';
            $imagick->writeImage($tempImagePath);
            $imagick->clear();
            $imagick->destroy();

            // Анализируем как изображение
            $result = $this->analyzeImageWithGpt4oVision($tempImagePath);

            // Удаляем временный файл
            @unlink($tempImagePath);

            return $result;
        } catch (\Exception $e) {
            Log::error('Ошибка конвертации PDF в изображение: ' . $e->getMessage());
            return [];
        }
    }
}
