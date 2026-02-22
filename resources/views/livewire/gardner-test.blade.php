<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            @if(!$isCompleted)
                <!-- Тест в процессе -->
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">{{ __('Gardner Test - Multiple Intelligences') }}</h1>
                        <div class="flex items-center space-x-4">
                            <a href="{{ route('gardner-test-all') }}" class="text-blue-600 hover:text-blue-800 text-sm">{{ __('All questions at once') }} →</a>
                            <div class="text-sm text-gray-500">
                                {{ __('Question') }} {{ $currentQuestion + 1 }} {{ __('of') }} {{ $totalQuestions }}
                            </div>
                        </div>
                    </div>

                    <!-- Прогресс бар -->
                    <div class="w-full bg-gray-200 rounded-full h-2 mb-6">
                        <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300"
                             style="width: {{ (($currentQuestion + 1) / $totalQuestions) * 100 }}%"></div>
                    </div>

                    @if (session()->has('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <!-- Индикатор прогресса ответов -->
                    @php
                        $answeredCount = count(array_filter($answers, fn($x) => $x !== null));
                        $allAnswered = !in_array(null, $answers);
                    @endphp

                    @if($answeredCount > 0)
                        <div class="mb-4 p-3 bg-blue-50 rounded-lg">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-blue-800">
                                    {{ __('Answered') }}: {{ $answeredCount }} {{ __('of') }} {{ $totalQuestions }} {{ __('questions') }}
                                </span>
                                @if($allAnswered)
                                    <button type="button"
                                            wire:click="submitTest"
                                            class="inline-flex items-center px-3 py-1 bg-green-600 border border-transparent rounded text-xs text-white font-semibold uppercase tracking-wider hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition">
                                        {{ __('Finish test') }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Текущий вопрос -->
                    <div class="mb-8">
                        <h2 class="text-lg font-semibold mb-4">{{ __($questions[$currentQuestion]['text']) }}</h2>

                        <div class="space-y-3">
                            @foreach([1 => __('Strongly agree'), 2 => __('Agree'), 3 => __('Partially agree'), 4 => __('Disagree'), 5 => __('Strongly disagree')] as $value => $label)
                                <label class="flex items-center p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer {{ isset($answers[$currentQuestion]) && $answers[$currentQuestion] == $value ? 'bg-indigo-50 border-indigo-500' : '' }}">
                                    <input type="radio"
                                           name="question_{{ $currentQuestion }}"
                                           value="{{ $value }}"
                                           wire:click="selectAnswer({{ $value }})"
                                           {{ isset($answers[$currentQuestion]) && $answers[$currentQuestion] == $value ? 'checked' : '' }}
                                           class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300">
                                    <span class="ml-3 text-gray-700">{{ $label }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <!-- Навигация -->
                    <div class="flex justify-between">
                        <button type="button"
                                wire:click="previousQuestion"
                                @if($currentQuestion == 0) disabled @endif
                                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 active:bg-gray-500 focus:outline-none focus:border-gray-500 focus:ring focus:ring-gray-200 disabled:opacity-50 disabled:cursor-not-allowed transition">
                            ← {{ __('Previous') }}
                        </button>

                        @if($currentQuestion == $totalQuestions - 1)
                            @php
                                $allAnswered = !in_array(null, $answers);
                            @endphp
                            <button type="button"
                                    wire:click="submitTest"
                                    {{ !$allAnswered ? 'disabled' : '' }}
                                    class="inline-flex items-center px-4 py-2 {{ $allAnswered ? 'bg-green-600 hover:bg-green-700 active:bg-green-800 focus:border-green-700 focus:ring-green-200' : 'bg-gray-400 cursor-not-allowed' }} border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring transition">
                                @if($allAnswered)
                                    {{ __('Finish test') }}
                                @else
                                    {{ __('Answer all questions') }}
                                @endif
                            </button>
                        @else
                            <button type="button"
                                    wire:click="nextQuestion"
                                    {{ !isset($answers[$currentQuestion]) || $answers[$currentQuestion] === null ? 'disabled' : '' }}
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:border-indigo-700 focus:ring focus:ring-indigo-200 disabled:opacity-50 disabled:cursor-not-allowed transition">
                                {{ __('Next') }} →
                            </button>
                        @endif
                    </div>
                </div>
            @else
                <!-- Результаты теста -->
                <div class="p-6">
                    <div class="flex justify-between items-center mb-6">
                        <h1 class="text-2xl font-bold text-gray-900">{{ __('Gardner Test Results') }}</h1>
                        <button type="button"
                                wire:click="retakeTest"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-800 focus:outline-none focus:border-indigo-700 focus:ring focus:ring-indigo-200 transition">
                            {{ __('Retake test') }}
                        </button>
                    </div>

                    @if (session()->has('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                            {{ session('success') }}
                        </div>
                    @endif

                    @php
                        // Маппинг типов интеллекта на цвета и эмодзи
                        $intelligenceConfig = [
                            'Лингвистический интеллект' => ['color' => '#e06666', 'textColor' => 'black', 'emoji' => '㊗️'],
                            'Логико-математический интеллект' => ['color' => '#ea9999', 'textColor' => 'black', 'emoji' => '🧠'],
                            'Музыкальный интеллект' => ['color' => '#3c78d8', 'textColor' => 'white', 'emoji' => '🎶'],
                            'Телесно-кинестетический интеллект' => ['color' => '#f6b26b', 'textColor' => 'black', 'emoji' => '✋🏻'],
                            'Пространственный интеллект' => ['color' => '#38761d', 'textColor' => 'white', 'emoji' => '👁️'],
                            'Межличностный интеллект' => ['color' => '#073763', 'textColor' => 'white', 'emoji' => '👥'],
                            'Внутриличностный интеллект' => ['color' => '#a6bee7', 'textColor' => 'black', 'emoji' => '💭'],
                            'Натуралистический интеллект' => ['color' => '#f1c232', 'textColor' => 'black', 'emoji' => '🌻'],
                            'Экзистенциальный интеллект' => ['color' => '#6d9eeb', 'textColor' => 'black', 'emoji' => '🙏🏻'],
                        ];

                        $intelligenceNames = [
                            'Лингвистический интеллект' => __('Linguistic Intelligence'),
                            'Логико-математический интеллект' => __('Logical-Mathematical Intelligence'),
                            'Пространственный интеллект' => __('Spatial Intelligence'),
                            'Музыкальный интеллект' => __('Musical Intelligence'),
                            'Телесно-кинестетический интеллект' => __('Bodily-Kinesthetic Intelligence'),
                            'Внутриличностный интеллект' => __('Intrapersonal Intelligence'),
                            'Межличностный интеллект' => __('Interpersonal Intelligence'),
                            'Натуралистический интеллект' => __('Naturalistic Intelligence'),
                            'Экзистенциальный интеллект' => __('Existential Intelligence')
                        ];

                        // Первый ряд (5 типов)
                        $row1Types = [
                            'Лингвистический интеллект',
                            'Логико-математический интеллект',
                            'Музыкальный интеллект',
                            'Телесно-кинестетический интеллект',
                            'Пространственный интеллект',
                        ];

                        // Второй ряд (4 типа)
                        $row2Types = [
                            'Межличностный интеллект',
                            'Внутриличностный интеллект',
                            'Натуралистический интеллект',
                            'Экзистенциальный интеллект',
                        ];
                    @endphp

                    <!-- Диаграмма интеллектов -->
                    <div class="bg-gray-100 rounded-lg p-6 mb-6">
                        <!-- Первый ряд -->
                        <div style="display: flex; align-items: flex-end; height: 200px; margin-bottom: 8px;">
                            <!-- Ось Y -->
                            <div style="width: 32px; height: 200px; position: relative; margin-right: 8px;">
                                @foreach([100, 75, 50, 25, 0] as $mark)
                                    <div style="position: absolute; bottom: {{ $mark * 2 }}px; right: 0; transform: translateY(50%); font-size: 10px; color: #666; text-align: right; width: 28px;">{{ $mark }}</div>
                                @endforeach
                            </div>
                            <!-- Столбцы первого ряда -->
                            <div style="flex: 1; position: relative; height: 200px;">
                                @foreach([100, 75, 50, 25, 0] as $mark)
                                    <div style="position: absolute; bottom: {{ $mark * 2 }}px; left: 0; right: 0; border-bottom: 1px solid #d1d5db; z-index: 0;"></div>
                                @endforeach
                                <div style="display: flex; align-items: flex-end; justify-content: space-around; height: 200px; position: relative; z-index: 1;">
                                    @foreach($row1Types as $type)
                                        @php
                                            $percentage = $results[$type] ?? '0%';
                                            $numericValue = (int) str_replace('%', '', $percentage);
                                            $config = $intelligenceConfig[$type] ?? ['color' => '#cccccc', 'textColor' => 'white', 'emoji' => '❓'];
                                            $barHeight = max(round($numericValue * 2), 32);
                                            $textColor = $config['textColor'] ?? 'white';
                                            $textShadow = $textColor === 'white' ? '1px 1px 2px rgba(0,0,0,0.3)' : 'none';
                                        @endphp
                                        <div style="width: 80px; height: {{ $barHeight }}px; background-color: {{ $config['color'] }}; border-radius: 6px 6px 0 0; display: flex; align-items: flex-start; justify-content: center; padding-top: {{ $barHeight > 40 ? '8' : '4' }}px;">
                                            <span style="font-size: 20px; font-weight: bold; color: {{ $textColor }}; text-shadow: {{ $textShadow }};">{{ $numericValue }}%</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <!-- Подписи первого ряда -->
                        <div style="display: flex; justify-content: space-around; margin-left: 40px; margin-bottom: 24px;">
                            @foreach($row1Types as $type)
                                @php
                                    $shortName = str_replace(' интеллект', '', $type);
                                    $config = $intelligenceConfig[$type] ?? ['color' => '#cccccc', 'emoji' => '❓'];
                                @endphp
                                <div style="width: 80px; display: flex; flex-direction: column; align-items: center;">
                                    <div style="font-size: 20px; margin-bottom: 4px;">{{ $config['emoji'] }}</div>
                                    <div style="text-align: center; font-size: 11px; font-weight: 600; color: #374151; line-height: 1.3;">
                                        {{ $shortName }}<br>интеллект
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Второй ряд -->
                        <div style="display: flex; align-items: flex-end; height: 200px; margin-bottom: 8px;">
                            <!-- Ось Y -->
                            <div style="width: 32px; height: 200px; position: relative; margin-right: 8px;">
                                @foreach([100, 75, 50, 25, 0] as $mark)
                                    <div style="position: absolute; bottom: {{ $mark * 2 }}px; right: 0; transform: translateY(50%); font-size: 10px; color: #666; text-align: right; width: 28px;">{{ $mark }}</div>
                                @endforeach
                            </div>
                            <!-- Столбцы второго ряда -->
                            <div style="flex: 1; position: relative; height: 200px;">
                                @foreach([100, 75, 50, 25, 0] as $mark)
                                    <div style="position: absolute; bottom: {{ $mark * 2 }}px; left: 0; right: 0; border-bottom: 1px solid #d1d5db; z-index: 0;"></div>
                                @endforeach
                                <div style="display: flex; align-items: flex-end; justify-content: space-around; height: 200px; position: relative; z-index: 1;">
                                    @foreach($row2Types as $type)
                                        @php
                                            $percentage = $results[$type] ?? '0%';
                                            $numericValue = (int) str_replace('%', '', $percentage);
                                            $config = $intelligenceConfig[$type] ?? ['color' => '#cccccc', 'textColor' => 'white', 'emoji' => '❓'];
                                            $barHeight = max(round($numericValue * 2), 32);
                                            $textColor = $config['textColor'] ?? 'white';
                                            $textShadow = $textColor === 'white' ? '1px 1px 2px rgba(0,0,0,0.3)' : 'none';
                                        @endphp
                                        <div style="width: 100px; height: {{ $barHeight }}px; background-color: {{ $config['color'] }}; border-radius: 6px 6px 0 0; display: flex; align-items: flex-start; justify-content: center; padding-top: {{ $barHeight > 40 ? '8' : '4' }}px;">
                                            <span style="font-size: 20px; font-weight: bold; color: {{ $textColor }}; text-shadow: {{ $textShadow }};">{{ $numericValue }}%</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <!-- Подписи второго ряда -->
                        <div style="display: flex; justify-content: space-around; margin-left: 40px;">
                            @foreach($row2Types as $type)
                                @php
                                    $shortName = str_replace(' интеллект', '', $type);
                                    $config = $intelligenceConfig[$type] ?? ['color' => '#cccccc', 'emoji' => '❓'];
                                @endphp
                                <div style="width: 100px; display: flex; flex-direction: column; align-items: center;">
                                    <div style="font-size: 20px; margin-bottom: 4px;">{{ $config['emoji'] }}</div>
                                    <div style="text-align: center; font-size: 11px; font-weight: 600; color: #374151; line-height: 1.3;">
                                        {{ $shortName }}<br>интеллект
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-8 p-4 bg-blue-50 rounded-lg">
                        <h3 class="font-semibold text-blue-900 mb-2">{{ __('Results Interpretation') }}</h3>
                        <p class="text-sm text-blue-800">
                            {{ __('Your results show the relative strength of different intelligence types. Dominant intelligence types indicate your natural inclinations and abilities. Remember that all intelligence types are important and can be developed.') }}
                        </p>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('dashboard') }}"
                           class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-800 focus:outline-none focus:border-gray-700 focus:ring focus:ring-gray-200 transition">
                            ← {{ __('Return to dashboard') }}
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div> 