<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\GardnerTestResult;
use Illuminate\Support\Facades\Auth;

class GardnerTestAllQuestions extends Component
{
    public $answers = [];
    public $questions = [];
    public $totalQuestions = 44;
    public $isCompleted = false;
    public $results = [];

    public function mount()
    {
        $this->initializeQuestions();
        
        // Убеждаемся, что totalQuestions соответствует реальному количеству вопросов
        $this->totalQuestions = count($this->questions);
        
        logger()->debug('GardnerTestAllQuestions initialization', [
            'questions_count' => count($this->questions),
            'totalQuestions' => $this->totalQuestions
        ]);
        
        // Проверяем, есть ли уже результаты теста для пользователя
        $existingResult = GardnerTestResult::where('user_id', Auth::id())->first();
        
        if ($existingResult) {
            $this->isCompleted = true;
            $this->results = $existingResult->results;
            return;
        }

        $this->answers = array_fill(0, $this->totalQuestions, null);
        
        logger()->debug('Answers array initialized', [
            'answers_count' => count($this->answers),
            'answers_keys' => array_keys($this->answers)
        ]);
    }

    private function initializeQuestions()
    {
        $this->questions = [
            ['text' => 'I have always had excellent grades in mathematics and exact sciences', 'type' => 'logical_mathematical'],
            ['text' => 'When meeting new people, I can make a good impression', 'type' => 'interpersonal'],
            ['text' => 'I like different kinds of sports', 'type' => 'bodily_kinesthetic'],
            ['text' => 'I think a lot about other people\'s emotions', 'type' => 'interpersonal'],
            ['text' => 'I often hum a song or melody in my head', 'type' => 'musical'],
            ['text' => 'I am good at smoothing conflicts between other people', 'type' => 'interpersonal'],
            ['text' => 'I like languages and social sciences', 'type' => 'linguistic'],
            ['text' => 'I am interested in questions like "Where is humanity heading?" or "Why are we here?"', 'type' => 'existential'],
            ['text' => 'I often think about the meaning of life', 'type' => 'existential'],
            ['text' => 'I think a lot about my own reaction to certain things', 'type' => 'intrapersonal'],
            ['text' => 'I like to dive deep into my thoughts and analyze everything that happens to me', 'type' => 'intrapersonal'],
            ['text' => 'I get along well with animals', 'type' => 'naturalistic'],
            ['text' => 'I don\'t mind getting my hands dirty creating, building or repairing something', 'type' => 'bodily_kinesthetic'],
            ['text' => 'I often think about deep things that seem meaningless to others', 'type' => 'existential'],
            ['text' => 'I enjoy games and tasks that require lateral thinking, such as chess', 'type' => 'logical_mathematical'],
            ['text' => 'I write poems, quotes, stories or keep a diary', 'type' => 'linguistic'],
            ['text' => 'I am good at reading maps and finding my way in unfamiliar places', 'type' => 'spatial'],
            ['text' => 'I feel most alive in contact with nature', 'type' => 'naturalistic'],
            ['text' => 'I always discover new types of music', 'type' => 'musical'],
            ['text' => 'I often look up word meanings in dictionaries', 'type' => 'linguistic'],
            ['text' => 'I clearly remember the details of decor and furniture in rooms I\'ve been in', 'type' => 'spatial'],
            ['text' => 'I enjoy long walks in nature alone or with friends', 'type' => 'naturalistic'],
            ['text' => 'I like to spend time alone, thinking about my reactions and emotions', 'type' => 'intrapersonal'],
            ['text' => 'I enjoy caring for gardens and plants', 'type' => 'naturalistic'],
            ['text' => 'I am good at detecting lies', 'type' => 'interpersonal'],
            ['text' => 'I enjoy reading', 'type' => 'linguistic'],
            ['text' => 'I enjoy learning new words and languages', 'type' => 'linguistic'],
            ['text' => 'I often analyze my feelings and emotions for a long time', 'type' => 'intrapersonal'],
            ['text' => 'I am good at working with numbers', 'type' => 'logical_mathematical'],
            ['text' => 'I like learning about how different world religions have tried to answer the "big questions of humanity"', 'type' => 'existential'],
            ['text' => 'I always draw graphs and tables to better remember information', 'type' => 'spatial'],
            ['text' => 'I enjoy sewing, carving, model making or other activities involving fine motor skills', 'type' => 'bodily_kinesthetic'],
            ['text' => 'I find it easier to solve a problem when I\'m moving', 'type' => 'bodily_kinesthetic'],
            ['text' => 'It\'s easy for me to understand what I\'m feeling and why', 'type' => 'intrapersonal'],
            ['text' => 'I love solving Rubik\'s cube and sudoku', 'type' => 'logical_mathematical'],
            ['text' => 'I enjoy studying different types of plants and animals', 'type' => 'naturalistic'],
            ['text' => 'I love dancing, sports and workouts', 'type' => 'bodily_kinesthetic'],
            ['text' => 'I can easily detect a false note', 'type' => 'musical'],
            ['text' => 'I love singing or playing musical instruments', 'type' => 'musical'],
            ['text' => 'I remember faces better than names', 'type' => 'spatial'],
            ['text' => 'Other people often come to me for support or advice', 'type' => 'interpersonal'],
            ['text' => 'I enjoy measuring or sorting things', 'type' => 'logical_mathematical'],
            ['text' => 'I often think about philosophical or theological questions', 'type' => 'existential'],
            ['text' => 'Music is one of my biggest passions', 'type' => 'musical'],
            ['text' => 'I find it easier to learn when the material is accompanied by diagrams, charts or other technical illustrations', 'type' => 'spatial'],
        ];
    }

    public function selectAnswerByIndex($index, $value)
    {
        // Проверяем, что индекс находится в допустимом диапазоне
        if ($index >= 0 && $index < count($this->questions)) {
            $this->answers[$index] = $value;
        }
    }

    public function submitTest()
    {
        try {
            logger()->debug('Gardner test submission started (all questions)', [
                'user_id' => Auth::id(),
                'answers_count' => count($this->answers),
            ]);

            // Проверяем, что все вопросы отвечены
            if (in_array(null, $this->answers)) {
                logger()->debug('Some questions not answered', [
                    'null_count' => count(array_filter($this->answers, fn($x) => $x === null))
                ]);
                session()->flash('error', 'Пожалуйста, ответьте на все вопросы.');
                return;
            }

            // Подсчитываем результаты по типам интеллекта
            $scores = [
                'linguistic' => 0,
                'logical_mathematical' => 0,
                'spatial' => 0,
                'musical' => 0,
                'bodily_kinesthetic' => 0,
                'intrapersonal' => 0,
                'interpersonal' => 0,
                'naturalistic' => 0,
                'existential' => 0,
            ];

            foreach ($this->questions as $index => $question) {
                if (isset($this->answers[$index])) {
                    $scores[$question['type']] += $this->answers[$index];
                }
            }

            // Русские названия типов интеллекта
            $intelligenceTypes = [
                'linguistic' => 'Лингвистический интеллект',
                'logical_mathematical' => 'Логико-математический интеллект',
                'spatial' => 'Пространственный интеллект',
                'musical' => 'Музыкальный интеллект',
                'bodily_kinesthetic' => 'Телесно-кинестетический интеллект',
                'intrapersonal' => 'Внутриличностный интеллект',
                'interpersonal' => 'Межличностный интеллект',
                'naturalistic' => 'Натуралистический интеллект',
                'existential' => 'Экзистенциальный интеллект',
            ];

            // Преобразуем баллы в проценты и создаем результаты с русскими названиями
            $results = [];
            foreach ($scores as $type => $score) {
                $percentage = round(($score / 25) * 100, 0); // Максимум 25 баллов = 100% (5 вопросов × 5 баллов)
                $results[$intelligenceTypes[$type]] = $percentage . '%';
            }

            logger()->debug('Scores calculated', ['scores' => $scores, 'results' => $results]);

            // Сохраняем результаты в базу данных
            $result = GardnerTestResult::create([
                'user_id' => Auth::id(),
                'answers' => $this->answers,
                'results' => $results,
            ]);

            logger()->debug('Gardner test result saved', ['result_id' => $result->id]);

            $this->isCompleted = true;
            $this->results = $results;

            session()->flash('success', 'Тест успешно завершен!');
            
        } catch (\Exception $e) {
            logger()->error('Error submitting Gardner test', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            session()->flash('error', 'Произошла ошибка при сохранении результатов: ' . $e->getMessage());
        }
    }

    public function retakeTest()
    {
        // Удаляем старые результаты
        GardnerTestResult::where('user_id', Auth::id())->delete();
        
        $this->isCompleted = false;
        $this->answers = array_fill(0, $this->totalQuestions, null);
        $this->results = [];
    }

    public function getIntelligenceTypes()
    {
        return [
            'linguistic' => 'Лингвистический интеллект',
            'logical_mathematical' => 'Логико-математический интеллект',
            'spatial' => 'Пространственный интеллект',
            'musical' => 'Музыкальный интеллект',
            'bodily_kinesthetic' => 'Телесно-кинестетический интеллект',
            'intrapersonal' => 'Внутриличностный интеллект',
            'interpersonal' => 'Межличностный интеллект',
            'naturalistic' => 'Натуралистический интеллект',
            'existential' => 'Экзистенциальный интеллект',
        ];
    }

    public function render()
    {
        return view('livewire.gardner-test-all-questions')->layout('layouts.app');
    }
} 