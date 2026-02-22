<?php
// resources/views/livewire/candidate-form.blade.php
?>
<div>
    <!-- Loading Overlay для формирования отчётов -->
    <div wire:loading.flex wire:target="submit"
         class="fixed inset-0 z-[9999] items-center justify-center bg-gradient-to-br from-slate-800 via-blue-900 to-slate-900">
        <div class="bg-white rounded-3xl shadow-2xl p-6 sm:p-10 mx-3 sm:mx-4 max-w-sm sm:max-w-md w-full text-center transform transition-all">

            <!-- Большой анимированный спиннер -->
            <div class="relative w-24 h-24 sm:w-32 sm:h-32 mx-auto mb-6 sm:mb-8">
                <!-- Внешнее кольцо -->
                <div class="absolute inset-0 rounded-full border-4 border-blue-100"></div>
                <div class="absolute inset-0 rounded-full border-4 border-transparent border-t-blue-500 border-r-blue-500 animate-spin" style="animation-duration: 1.5s;"></div>

                <!-- Среднее кольцо -->
                <div class="absolute inset-3 sm:inset-4 rounded-full border-4 border-green-100"></div>
                <div class="absolute inset-3 sm:inset-4 rounded-full border-4 border-transparent border-t-green-500 border-l-green-500 animate-spin" style="animation-duration: 1s; animation-direction: reverse;"></div>

                <!-- Внутреннее кольцо -->
                <div class="absolute inset-6 sm:inset-8 rounded-full border-4 border-purple-100"></div>
                <div class="absolute inset-6 sm:inset-8 rounded-full border-4 border-transparent border-b-purple-500 border-r-purple-500 animate-spin" style="animation-duration: 0.75s;"></div>

                <!-- Иконка документа в центре -->
                <div class="absolute inset-0 flex items-center justify-center">
                    <svg class="w-8 h-8 sm:w-10 sm:h-10 text-blue-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>

            <!-- Заголовок с градиентом -->
            <h3 class="text-xl sm:text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent mb-2 sm:mb-3">
                {{ __('Generating your resume') }}
            </h3>

            <!-- Описание -->
            <p class="text-gray-600 text-sm sm:text-base mb-5 sm:mb-6 px-2">
                {{ __('Please wait, we are processing your data and creating reports...') }}
            </p>

            <!-- Прогресс бар с анимацией -->
            <div class="w-full bg-gray-200 rounded-full h-2 mb-4 sm:mb-5 overflow-hidden">
                <div class="bg-gradient-to-r from-blue-500 via-purple-500 to-blue-500 h-2 rounded-full animate-pulse"
                     style="width: 100%; background-size: 200% 100%; animation: shimmer 2s linear infinite;">
                </div>
            </div>

            <!-- Анимированные точки -->
            <div class="flex justify-center items-center space-x-1.5 sm:space-x-2 mb-4">
                <span class="text-gray-500 text-sm">{{ __('Processing') }}</span>
                <div class="flex space-x-1">
                    <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0s;"></div>
                    <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-purple-500 rounded-full animate-bounce" style="animation-delay: 0.15s;"></div>
                    <div class="w-1.5 h-1.5 sm:w-2 sm:h-2 bg-blue-500 rounded-full animate-bounce" style="animation-delay: 0.3s;"></div>
                </div>
            </div>

            <!-- Подсказка -->
            <p class="text-xs sm:text-sm text-gray-400">
                {{ __('This may take a few minutes') }}
            </p>
        </div>
    </div>

    <style>
        @keyframes shimmer {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
    </style>

    <div class="max-w-7xl mx-auto px-3 sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-xl rounded-lg">
        <!-- Step Navigation -->
        <div class="mb-6 px-3 sm:px-6 pt-6">
            <!-- Mobile Navigation (Grid for smaller screens) -->
            <div class="block lg:hidden">
                <div class="grid grid-cols-2 gap-3 mb-4">
                    @foreach([
                        ['step' => 1, 'title' => __('Basic Information')],
                        ['step' => 2, 'title' => __('Additional Information')],
                        ['step' => 3, 'title' => __('Education and Work')],
                        ['step' => 4, 'title' => __('Tests')]
                    ] as $stepInfo)
                    <button type="button" 
                            wire:click="goToStep({{ $stepInfo['step'] }})"
                            class="relative flex items-center p-3 rounded-lg border-2 transition-all duration-200 {{ $currentStep === $stepInfo['step'] ? 'border-blue-600 bg-blue-50' : 'border-gray-200 bg-white hover:border-gray-300' }} {{ $this->hasErrorsOnStep($stepInfo['step']) ? 'border-red-300 bg-red-50' : '' }}">
                        <!-- Индикатор ошибки -->
                        @if($this->hasErrorsOnStep($stepInfo['step']))
                            <div class="absolute -top-1 -right-1 w-4 h-4 bg-red-500 rounded-full flex items-center justify-center">
                                <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                        @endif
                        <div class="flex-shrink-0 mr-3">
                            @if($currentStep > $stepInfo['step'])
                                <svg class="h-5 w-5 text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            @else
                                <div class="w-5 h-5 border-2 {{ $currentStep === $stepInfo['step'] ? 'border-blue-600 bg-blue-600' : 'border-gray-300' }} rounded-full flex items-center justify-center">
                                    <span class="text-xs font-medium {{ $currentStep === $stepInfo['step'] ? 'text-white' : 'text-gray-500' }}">{{ $stepInfo['step'] }}</span>
                                </div>
                            @endif
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-medium {{ $currentStep === $stepInfo['step'] ? 'text-blue-600' : 'text-gray-600' }} truncate">
                                {{ $stepInfo['title'] }}
                            </p>
                        </div>
                    </button>
                    @endforeach
                </div>
            </div>

            <!-- Desktop Navigation (Horizontal for larger screens) -->
            <div class="hidden lg:flex items-center justify-between">
                <div class="flex items-center space-x-2 w-full">
                    @foreach([
                        ['step' => 1, 'title' => __('Basic Information')],
                        ['step' => 2, 'title' => __('Additional Information')],
                        ['step' => 3, 'title' => __('Education and Work')],
                        ['step' => 4, 'title' => __('Tests')]
                    ] as $index => $stepInfo)
                    <!-- Step -->
                    <div class="flex items-center {{ $index === 3 ? '' : 'flex-1' }}">
                        <div class="relative flex items-center {{ $currentStep >= $stepInfo['step'] ? 'text-blue-600' : 'text-gray-500' }}">
                            <!-- Индикатор ошибки -->
                            @if($this->hasErrorsOnStep($stepInfo['step']))
                                <div class="absolute -top-2 -left-2 w-5 h-5 bg-red-500 rounded-full flex items-center justify-center z-10 shadow-lg">
                                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                    </svg>
                                </div>
                            @endif
                            <div class="flex-shrink-0">
                                @if($currentStep > $stepInfo['step'])
                                    <svg class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                @else
                                    <div class="w-6 h-6 border-2 {{ $currentStep === $stepInfo['step'] ? 'border-blue-600' : 'border-gray-300' }} {{ $this->hasErrorsOnStep($stepInfo['step']) ? 'border-red-500' : '' }} rounded-full flex items-center justify-center">
                                        <span class="text-sm {{ $currentStep === $stepInfo['step'] ? 'text-blue-600' : 'text-gray-500' }} {{ $this->hasErrorsOnStep($stepInfo['step']) ? 'text-red-500 font-bold' : '' }}">{{ $stepInfo['step'] }}</span>
                                    </div>
                                @endif
                            </div>
                            <div class="ml-3">
                                <button type="button" wire:click="goToStep({{ $stepInfo['step'] }})" class="text-sm font-medium {{ $currentStep >= $stepInfo['step'] ? 'text-blue-600' : 'text-gray-500' }} {{ $this->hasErrorsOnStep($stepInfo['step']) ? 'text-red-600' : '' }}">
                                    {{ $stepInfo['title'] }}
                                </button>
                            </div>
                        </div>
                        @if($index < 3)
                        <div class="flex-1 px-6">
                            <div class="h-0.5 {{ $currentStep > $stepInfo['step'] ? 'bg-blue-600' : 'bg-gray-200' }}"></div>
                        </div>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Remove old progress bar -->

        <form wire:submit.prevent="submit" class="p-3 sm:p-6 space-y-6">
            @if ($errors->any())
                <div id="validation-errors" tabindex="-1" role="alert" aria-live="assertive" class="px-4 py-3 rounded-md bg-red-50 border border-red-200">
                    <div class="text-red-700 font-semibold mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        {{ __('Please fix the errors below:') }}
                    </div>
                    
                    @php
                        $errorsByStep = $this->getErrorsByStep();
                        $hasMultipleStepsWithErrors = collect($errorsByStep)->filter(fn($errors) => !empty($errors))->count() > 1;
                    @endphp
                    
                    @foreach($errorsByStep as $step => $errorKeys)
                        @if(!empty($errorKeys))
                            <div class="mb-3 last:mb-0">
                                <button type="button"
                                        wire:click="goToStep({{ $step }})"
                                        class="inline-flex items-center px-3 py-1.5 mb-2 bg-red-100 hover:bg-red-200 text-red-800 font-medium rounded-lg transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    Шаг {{ $step }}: {{ $this->getStepTitle($step) }}
                                </button>
                                <ul class="text-sm text-red-600 list-disc pl-5 space-y-1">
                                    @foreach($errorKeys as $errorKey)
                                        @foreach($errors->get($errorKey) as $message)
                                            <li class="ml-6">{{ $message }}</li>
                                        @endforeach
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    @endforeach
                </div>
            @endif
            <!-- Step Content -->
            @include('livewire.candidate-form.step1')
            @include('livewire.candidate-form.step2')
            @include('livewire.candidate-form.step3')
            @include('livewire.candidate-form.step4')

            <!-- Navigation Buttons -->
            <div class="flex flex-col sm:flex-row justify-between items-stretch sm:items-center gap-3 mt-8 px-3 sm:px-0">
                @if ($currentStep > 1)
                    <button type="button"
                            wire:click="previousStep"
                            class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        {{ __('Back') }}
                    </button>
                @else
                    <div class="hidden sm:block"></div>
                @endif

                @if ($currentStep < $totalSteps)
                    <button type="button"
                            wire:click="nextStep"
                            class="inline-flex items-center justify-center px-6 py-3 bg-blue-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition">
                        {{ __('Save and continue') }}
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                @else
                    <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2">
                        <button type="submit"
                                wire:loading.attr="disabled"
                                wire:target="submit"
                                class="inline-flex items-center justify-center px-6 py-3 bg-green-600 border border-transparent rounded-md font-semibold text-sm text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-50 disabled:cursor-not-allowed transition">
                            <!-- Обычное состояние -->
                            <span wire:loading.remove wire:target="submit" class="inline-flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                {{ __('Save and finish') }}
                            </span>
                            <!-- Состояние загрузки -->
                            <span wire:loading wire:target="submit" class="inline-flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ __('Processing...') }}
                            </span>
                        </button>
                        @if ($errors->any())
                            <div class="text-sm text-red-600 flex items-center">
                                {{ __('Check errors at the top of the form') }}
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </form>
    </div>
</div>
</div>

@push('scripts')
<script>
// Глобальный обработчик ползунков - версия 2.0
(function() {
    'use strict';
    
    // Предотвращаем повторную загрузку
    if (window.SliderManager) return;
    
    window.SliderManager = {
        activeSliders: new Map(),
        observer: null,
        
        init() {
            console.log('🎚️ SliderManager: Initializing...');
            this.setupMutationObserver();
            this.scanAndInitSliders();
            this.setupLivewireHooks();
        },
        
        setupMutationObserver() {
            // Отслеживаем изменения в DOM
            this.observer = new MutationObserver((mutations) => {
                let needsReinit = false;
                mutations.forEach((mutation) => {
                    if (mutation.type === 'childList') {
                        mutation.addedNodes.forEach((node) => {
                            if (node.nodeType === 1) { // Element node
                                if (node.matches('input[type="range"]') || 
                                    node.querySelector('input[type="range"]')) {
                                    needsReinit = true;
                                }
                            }
                        });
                    }
                });
                
                if (needsReinit) {
                    console.log('🔄 DOM changed, reinitializing sliders...');
                    setTimeout(() => this.scanAndInitSliders(), 50);
                }
            });
            
            this.observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        },
        
        setupLivewireHooks() {
            if (typeof Livewire !== 'undefined') {
                // Хук для обновлений Livewire
                Livewire.hook('message.processed', (message, component) => {
                    console.log('🔄 Livewire message processed');
                    setTimeout(() => this.scanAndInitSliders(), 100);

                    // Скролл к блоку ошибок, если есть ошибки
                    try {
                        const errs = component?.serverMemo?.errors || {};
                        if (errs && Object.keys(errs).length > 0) {
                            const errorBox = document.getElementById('validation-errors');
                            if (errorBox) {
                                // Вычисляем динамический отступ с учетом фиксированных элементов/хедера
                                const viewportHeight = Math.max(document.documentElement.clientHeight || 0, window.innerHeight || 0);
                                const rect = errorBox.getBoundingClientRect();
                                const absoluteTop = rect.top + window.pageYOffset;
                                const extraOffset = 350; // увеличенный отступ, чтобы блок гарантированно попал в видимую область
                                const targetTop = Math.max(absoluteTop - extraOffset, 0);

                                window.scrollTo({ top: targetTop, behavior: 'smooth' });
                                errorBox.focus({ preventScroll: true });
                            } else {
                                window.scrollTo({ top: 0, behavior: 'smooth' });
                            }
                        }
                    } catch (e) {
                        console.warn('Scroll-to-errors failed', e);
                    }
                });
                
                // Хук для навигации
                document.addEventListener('livewire:navigated', () => {
                    console.log('🔄 Livewire navigated');
                    setTimeout(() => this.scanAndInitSliders(), 100);
                });
                
                // Хук для смены шагов
                document.addEventListener('livewire:step-changed', (event) => {
                    console.log('🎚️ SliderManager: Step changed to:', event.detail.step);
                    this.reinitializeSliders('step-changed');
                });
                
                // Хук для переинициализации JS
                document.addEventListener('livewire:reinitialize-js', (event) => {
                    console.log('🎚️ SliderManager: Reinitialize JS event received');
                    this.reinitializeSliders('reinitialize-js');
                });
            }
        },
        
        scanAndInitSliders() {
            console.log('🔍 Scanning for sliders...');
            
            // Находим все ползунки на странице
            const allSliders = document.querySelectorAll('input[type="range"]');
            console.log(`Found ${allSliders.length} sliders total`);
            
            // Очищаем старые обработчики только если есть активные
            if (this.activeSliders.size > 0) {
                this.clearAllHandlers();
            }
            
            // Конфигурация всех ползунков
            const sliderConfigs = [
                // Step 2
                { name: 'books_per_year', displaySelector: null },
                { name: 'entertainment_hours_weekly', displaySelector: null },
                { name: 'educational_hours_weekly', displaySelector: null },
                { name: 'social_media_hours_weekly', displaySelector: null },
                
                // Step 3
                { name: 'total_experience_years', displaySelector: '#experience-display', minValue: 0 },
                { name: 'job_satisfaction', displaySelector: '#satisfaction-display', minValue: 1 }
            ];
            
            // Инициализируем каждый ползунок только если он виден
            sliderConfigs.forEach(config => {
                const slider = document.querySelector(`input[name="${config.name}"]`);
                if (slider && this.isElementVisible(slider)) {
                    this.initSlider(config);
                }
            });
            
            // Инициализируем GPA ползунки отдельно (динамические)
            this.initGpaSliders();
            
            console.log(`✅ SliderManager: ${this.activeSliders.size} sliders active`);
        },
        
        isElementVisible(element) {
            if (!element) return false;
            return element.offsetParent !== null && 
                   getComputedStyle(element).display !== 'none' &&
                   getComputedStyle(element).visibility !== 'hidden';
        },
        
        reinitializeSliders(source = 'manual') {
            console.log(`🔄 SliderManager: Reinitializing sliders (source: ${source})...`);
            
            // Очищаем старые обработчики
            this.clearAllHandlers();
            
            // Переинициализируем с задержкой для надежности
            setTimeout(() => {
                console.log('🔍 SliderManager: Scanning for new sliders...');
                this.scanAndInitSliders();
                console.log(`✅ SliderManager: Reinitialization complete (source: ${source})`);
            }, 100);
        },
        
        initSlider(config) {
            const slider = document.querySelector(`input[name="${config.name}"]`);
            if (!slider) return;
            
            let display;
            if (config.displaySelector) {
                display = document.querySelector(config.displaySelector);
            } else {
                // Ищем span в родительском элементе (для step2)
                display = slider.closest('div')?.parentElement?.querySelector('span');
            }
            
            if (!display) {
                console.warn(`❌ Display not found for ${config.name}`);
                return;
            }
            
            console.log(`🎚️ Initializing ${config.name}`);
            
            const handlers = this.createSliderHandlers(slider, display, config);
            this.activeSliders.set(config.name, handlers);
        },
        
        initGpaSliders() {
            const gpaSliders = document.querySelectorAll('input[type="range"][name*="universities"][name*="gpa"]');
            console.log(`🎓 Found ${gpaSliders.length} GPA sliders`);
            
            gpaSliders.forEach((slider, index) => {
                const display = slider.closest('div')?.parentElement?.querySelector('span');
                if (display) {
                    const key = `gpa_${index}`;
                    console.log(`🎚️ Initializing GPA slider ${index}`);
                    
                    const handlers = this.createSliderHandlers(slider, display, {
                        formatter: (value) => parseFloat(value).toFixed(2),
                        minValue: 0
                    });
                    this.activeSliders.set(key, handlers);
                }
            });
        },
        
        createSliderHandlers(slider, display, config = {}) {
            const updateDisplay = () => {
                const value = slider.value;
                const numValue = parseFloat(value);
                const minVal = config.minValue !== undefined ? config.minValue : parseFloat(slider.min);
                
                if (config.minValue !== undefined && numValue <= minVal) {
                    // Специальная обработка минимальных значений
                    if (slider.name === 'job_satisfaction') {
                        display.textContent = '1';
                    } else {
                        display.textContent = '0';
                    }
                } else {
                    // Применяем форматирование или выводим как есть
                    if (config.formatter) {
                        display.textContent = config.formatter(value);
                    } else {
                        display.textContent = value;
                    }
                }
            };
            
            // Создаем обработчики событий
            const inputHandler = (e) => {
                updateDisplay();
                // Не мешаем Livewire
                e.stopPropagation();
            };
            
            const changeHandler = (e) => {
                updateDisplay();
                // Позволяем Livewire обработать изменение
            };
            
            // Добавляем обработчики
            slider.addEventListener('input', inputHandler);
            slider.addEventListener('change', changeHandler);
            
            // Инициализируем отображение
            updateDisplay();
            
            return {
                slider,
                display,
                inputHandler,
                changeHandler,
                cleanup: () => {
                    slider.removeEventListener('input', inputHandler);
                    slider.removeEventListener('change', changeHandler);
                }
            };
        },
        
        clearAllHandlers() {
            console.log('🧹 Clearing all slider handlers...');
            this.activeSliders.forEach((handlers, key) => {
                handlers.cleanup();
            });
            this.activeSliders.clear();
        },
        
        destroy() {
            this.clearAllHandlers();
            if (this.observer) {
                this.observer.disconnect();
            }
        }
    };
    
    // Автозапуск
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => window.SliderManager.init(), 100);
        });
    } else {
        setTimeout(() => window.SliderManager.init(), 100);
    }
    
    // Очистка при выгрузке страницы
    window.addEventListener('beforeunload', () => {
        window.SliderManager.destroy();
    });
    
    // Дебаг функции для тестирования
    window.debugSliders = function() {
        console.log('🧪 DEBUG: Slider status');
        console.log('Active sliders:', window.SliderManager.activeSliders.size);
        console.log('All range inputs:', document.querySelectorAll('input[type="range"]').length);
        
        // Проверяем каждый ползунок
        document.querySelectorAll('input[type="range"]').forEach((slider, index) => {
            console.log(`Slider ${index}:`, {
                name: slider.name,
                value: slider.value,
                visible: slider.offsetParent !== null,
                hasListeners: window.SliderManager.activeSliders.has(slider.name)
            });
        });
    };
    
    window.testValidation = function() {
        console.log('🧪 DEBUG: Testing validation reset');
        if (typeof Livewire !== 'undefined' && Livewire.find) {
            const components = Livewire.all();
            console.log('Livewire components:', components.length);
            if (components[0]) {
                console.log('Current step:', components[0].data.currentStep);
                console.log('Has errors:', Object.keys(components[0].errors || {}).length > 0);
                console.log('Errors:', components[0].errors);
            }
        }
    };
    
    // Глобальная функция для принудительной переинициализации всех JS компонентов
    window.forceReinitializeJS = function() {
        console.log('🚀 FORCE: Manual reinitialization of all JS components');
        
        // Переинициализируем ползунки
        if (window.SliderManager) {
            window.SliderManager.reinitializeSliders('manual-force');
        }
        
        // Переинициализируем основные компоненты
        if (typeof reinitializeAllComponents === 'function') {
            reinitializeAllComponents(null, 'manual-force');
        } else {
            // Альтернативный способ если функция не доступна
            document.dispatchEvent(new CustomEvent('livewire:reinitialize-js'));
        }
        
        console.log('✅ FORCE: Manual reinitialization completed');
    };
    
    // Тестовая функция для проверки событий
    window.testEventChain = function() {
        console.log('🧪 TEST: Testing event chain...');
        
        // Тестируем отправку события step-changed
        console.log('📤 Dispatching step-changed event');
        document.dispatchEvent(new CustomEvent('livewire:step-changed', {
            detail: { step: 99 }
        }));
        
        // Тестируем отправку события reinitialize-js
        setTimeout(() => {
            console.log('📤 Dispatching reinitialize-js event');
            document.dispatchEvent(new CustomEvent('livewire:reinitialize-js'));
        }, 500);
        
        // Проверяем состояние через 2 секунды
        setTimeout(() => {
            console.log('🔍 Checking state after events...');
            window.debugSliders();
            window.testValidation();
            console.log('✅ TEST: Event chain test completed');
        }, 2000);
    };
    
})();
</script>
@endpush 