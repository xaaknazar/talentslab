<div class="min-h-screen" style="background-color: #F8FAFC;">
    <div class="mx-auto px-6 lg:px-8 py-6" style="max-width: 1600px;">
        <!-- Header -->
        <div class="mb-6 flex items-center justify-between">
            <div class="flex items-center gap-5">
                <a href="{{ route('dashboard') }}" class="group flex items-center gap-2 px-3 py-2 rounded-lg transition-all hover:bg-slate-100">
                    <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-600 transition-colors" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span class="text-sm text-slate-500 group-hover:text-slate-700 transition-colors">Dashboard</span>
                </a>
                <div class="h-6 w-px bg-slate-200"></div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center bg-gradient-to-br from-blue-500 to-blue-600 shadow-lg shadow-blue-500/25">
                        <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-semibold text-slate-800">AI Анализ</h1>
                        <p class="text-xs text-slate-400">Глубинная аналитика кандидатов</p>
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-blue-50 border border-blue-200">
                <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                <span class="text-xs font-medium text-blue-700">AI version 1.0</span>
            </div>
        </div>

        <!-- Main Layout -->
        <div class="flex gap-6">
            <!-- LEFT: Report Area -->
            <div class="flex-1 min-w-0">
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 h-full" style="min-height: calc(100vh - 160px);">
                    <!-- Report Header -->
                    <div class="px-6 py-4 flex items-center justify-between border-b border-slate-100 bg-slate-50/50 rounded-t-2xl">
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                                <span class="text-sm font-semibold text-slate-700">Аналитический отчет</span>
                            </div>
                            @if($selectedCandidate)
                                <div class="flex items-center gap-2 px-3 py-1 rounded-full bg-blue-50 border border-blue-200">
                                    <svg class="w-3.5 h-3.5 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span class="text-xs font-medium text-blue-700">{{ $selectedCandidate->full_name }}</span>
                                </div>
                            @endif
                        </div>
                        @if($report)
                            <button wire:click="clearReport" class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                <span class="text-xs font-medium">Новый анализ</span>
                            </button>
                        @endif
                    </div>

                    <!-- Report Content -->
                    <div class="p-8 overflow-auto" style="max-height: calc(100vh - 240px);">
                        @if($isLoading)
                            <div class="flex flex-col items-center justify-center" style="min-height: calc(100vh - 320px);">
                                <!-- Animated loader -->
                                <div class="relative mb-8">
                                    <div class="w-24 h-24 rounded-full border-4 border-slate-100"></div>
                                    <div class="w-24 h-24 rounded-full border-4 border-blue-500 border-t-transparent animate-spin absolute top-0 left-0"></div>
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="w-16 h-16 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center shadow-lg">
                                            <svg class="w-8 h-8 text-white animate-pulse" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <h3 class="text-xl font-bold text-slate-800 mb-2">Формируем отчет</h3>
                                <p class="text-sm text-slate-500 mb-6">AI анализирует данные кандидата</p>

                                <!-- Progress steps -->
                                <div class="flex flex-col gap-3 text-sm">
                                    <div class="flex items-center gap-3">
                                        <div class="w-6 h-6 rounded-full bg-blue-500 flex items-center justify-center">
                                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <span class="text-slate-700">Извлечение данных из PDF</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="w-6 h-6 rounded-full bg-blue-500 flex items-center justify-center animate-pulse">
                                            <div class="w-2 h-2 rounded-full bg-white"></div>
                                        </div>
                                        <span class="text-slate-700">Анализ профиля кандидата</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <div class="w-6 h-6 rounded-full bg-slate-200 flex items-center justify-center">
                                            <div class="w-2 h-2 rounded-full bg-slate-400"></div>
                                        </div>
                                        <span class="text-slate-400">Генерация отчета</span>
                                    </div>
                                </div>

                                <p class="text-xs text-slate-400 mt-6">Обычно занимает 1-2 минуты</p>
                            </div>
                        @elseif($error)
                            <div class="flex flex-col items-center justify-center py-16">
                                <div class="w-14 h-14 rounded-2xl flex items-center justify-center bg-red-50 border border-red-200 mb-4">
                                    <svg class="w-7 h-7 text-red-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-slate-800 mb-2">Ошибка</h3>
                                <p class="text-sm text-red-500 text-center max-w-md">{{ $error }}</p>
                            </div>
                        @elseif($report)
                            <article class="report-content">
                                {!! \Illuminate\Support\Str::markdown($report) !!}
                            </article>
                        @else
                            <div class="flex flex-col items-center justify-center text-center" style="min-height: calc(100vh - 320px);">
                                <div class="w-20 h-20 rounded-2xl flex items-center justify-center bg-gradient-to-br from-blue-500 to-blue-600 shadow-xl shadow-blue-500/30 mb-6">
                                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"></path>
                                    </svg>
                                </div>
                                <h2 class="text-2xl font-bold text-slate-800 mb-3">Готов к анализу</h2>
                                <p class="text-slate-500 max-w-md mb-8">
                                    Загрузите PDF-отчет кандидата для получения глубинного психологического анализа
                                </p>

                                <div class="flex flex-wrap items-center justify-center gap-2">
                                    <div class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-blue-50 border border-blue-200">
                                        <div class="w-6 h-6 rounded-md flex items-center justify-center bg-blue-500 text-white text-xs font-bold">1</div>
                                        <span class="text-sm text-blue-700">Выберите кандидата</span>
                                    </div>
                                    <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                    <div class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200">
                                        <div class="w-6 h-6 rounded-md flex items-center justify-center bg-slate-300 text-white text-xs font-bold">2</div>
                                        <span class="text-sm text-slate-500">Загрузите PDF</span>
                                    </div>
                                    <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                    <div class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200">
                                        <div class="w-6 h-6 rounded-md flex items-center justify-center bg-slate-300 text-white text-xs font-bold">3</div>
                                        <span class="text-sm text-slate-500">Получите отчет</span>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- RIGHT: Controls Sidebar -->
            <div class="w-80 flex-shrink-0 space-y-4">
                <!-- Candidate Search -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200">
                    <div class="px-4 py-3 flex items-center justify-between border-b border-slate-100 bg-gradient-to-r from-blue-500 to-blue-600 rounded-t-2xl">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            <span class="text-sm font-semibold text-white">Кандидат</span>
                        </div>
                        @if($selectedCandidate)
                            <span class="flex items-center gap-1.5 text-xs text-blue-100">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                Выбран
                            </span>
                        @endif
                    </div>
                    <div class="p-4">
                        @if($selectedCandidate)
                            <div class="rounded-xl p-4 bg-emerald-50 border border-emerald-200">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-semibold text-emerald-800 truncate">{{ $selectedCandidate->full_name }}</p>
                                        <p class="text-xs text-emerald-600 mt-1 truncate">{{ $selectedCandidate->email }}</p>
                                        @if($selectedCandidate->current_city)
                                            <p class="text-xs text-slate-500 mt-1">{{ $selectedCandidate->current_city }}</p>
                                        @endif
                                    </div>
                                    <button type="button" wire:click="clearCandidate" class="flex-shrink-0 p-2 rounded-lg text-red-500 bg-red-50 hover:bg-red-100 transition-all cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="relative">
                                <input
                                    type="text"
                                    wire:model.live.debounce.300ms="candidateSearch"
                                    wire:keydown.escape="$set('showCandidateDropdown', false)"
                                    placeholder="Поиск по имени..."
                                    class="w-full rounded-xl px-4 py-3 text-sm text-slate-700 placeholder-slate-400 bg-slate-50 border border-slate-200 focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all outline-none"
                                    autocomplete="off"
                                >

                                @if(count($candidateResults) > 0)
                                    <div
                                        class="absolute z-[9999] left-0 right-0 top-full mt-2 bg-white rounded-xl shadow-2xl border border-slate-200"
                                        style="max-height: 280px; overflow-y: auto;"
                                        x-data
                                    >
                                        @foreach($candidateResults as $result)
                                            <div
                                                @mousedown.prevent="$wire.selectCandidate({{ $result['id'] }})"
                                                wire:key="candidate-{{ $result['id'] }}"
                                                class="w-full text-left px-4 py-3 hover:bg-blue-50 active:bg-blue-100 transition-colors border-b border-slate-100 last:border-b-0 cursor-pointer"
                                            >
                                                <p class="text-sm font-medium text-slate-800">{{ $result['full_name'] }}</p>
                                                <p class="text-xs text-slate-500 mt-0.5">{{ $result['email'] }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- PDF Upload -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-4 py-3 flex items-center gap-2 border-b border-slate-100 bg-slate-50">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-red-100">
                            <svg class="w-4 h-4 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-slate-700">PDF Отчет</span>
                    </div>
                    <div class="p-4">
                        @if(!$fileName)
                            <div
                                x-data="{ isDragging: false }"
                                x-on:dragover.prevent="isDragging = true"
                                x-on:dragleave.prevent="isDragging = false"
                                x-on:drop.prevent="isDragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'));"
                                :class="{ 'border-blue-500 bg-blue-50': isDragging }"
                                class="border-2 border-dashed border-slate-200 rounded-xl p-6 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50/50 transition-all"
                                x-on:click="$refs.fileInput.click()"
                            >
                                <input type="file" wire:model="pdfFile" accept=".pdf" class="hidden" x-ref="fileInput">
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center bg-blue-100 mb-3">
                                        <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"></path>
                                        </svg>
                                    </div>
                                    <p class="text-sm text-slate-600">
                                        <span class="font-semibold text-blue-500">Нажмите</span> или перетащите
                                    </p>
                                    <p class="text-xs text-slate-400 mt-1">PDF до 20 МБ</p>
                                </div>
                            </div>
                        @else
                            <div class="rounded-xl p-3 bg-slate-50 border border-slate-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center min-w-0 flex-1 gap-3">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 bg-red-100">
                                            <svg class="w-5 h-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-slate-700 truncate">{{ $fileName }}</p>
                                            <p class="text-xs text-emerald-500">Готов к анализу</p>
                                        </div>
                                    </div>
                                    <button type="button" wire:click="removePdf" class="p-2 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 transition-all cursor-pointer">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endif
                        @error('pdfFile')
                            <p class="mt-3 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Context -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-4 py-3 flex items-center gap-2 border-b border-slate-100 bg-slate-50">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center bg-blue-100">
                            <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-slate-700">Контекст</span>
                        <span class="text-xs text-slate-400">(опционально)</span>
                    </div>
                    <div class="p-4">
                        <textarea
                            wire:model="comment"
                            rows="2"
                            class="w-full rounded-xl px-4 py-3 text-sm text-slate-700 placeholder-slate-400 bg-slate-50 border border-slate-200 resize-none focus:bg-white focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 transition-all outline-none"
                            placeholder="Вакансия или фокус анализа..."
                        ></textarea>
                    </div>
                </div>

                <!-- Generate Button -->
                <button
                    type="button"
                    wire:click="generateReport"
                    wire:loading.attr="disabled"
                    wire:target="generateReport"
                    @if($isLoading || !$fileName) disabled @endif
                    class="w-full h-14 text-white text-sm font-semibold rounded-2xl transition-all flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed bg-gradient-to-r from-blue-500 to-blue-600 hover:from-blue-600 hover:to-blue-700 shadow-lg shadow-blue-500/30 hover:shadow-blue-500/40 active:scale-[0.98]"
                >
                    <span wire:loading.remove wire:target="generateReport" class="flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"></path>
                        </svg>
                        Сформировать отчет
                    </span>
                    <span wire:loading wire:target="generateReport" class="flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Анализируем...
                    </span>
                </button>

                <!-- Debug -->
                @if($extractedText)
                    <div class="rounded-xl overflow-hidden bg-amber-50 border border-amber-200">
                        <button
                            wire:click="toggleExtractedText"
                            class="w-full px-4 py-3 text-left text-xs font-medium flex items-center justify-between text-amber-700 hover:bg-amber-100 transition-colors"
                        >
                            <span class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75L22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3l-4.5 16.5"></path>
                                </svg>
                                Raw данные ({{ number_format(strlen($extractedText)) }} симв.)
                            </span>
                            <svg class="w-4 h-4 transition-transform {{ $showExtractedText ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        @if($showExtractedText)
                            <div class="px-4 pb-4">
                                <pre class="text-[10px] p-3 rounded-lg overflow-auto max-h-40 whitespace-pre-wrap break-words bg-amber-100 text-amber-800" style="font-family: monospace;">{{ $extractedText }}</pre>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        .report-content {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: #334155;
            font-size: 15px;
            line-height: 1.8;
            padding: 16px 24px;
        }

        .report-content h1 {
            font-size: 26px;
            font-weight: 700;
            color: #1e293b;
            margin-top: 0;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 2px solid #3b82f6;
        }

        .report-content h2 {
            font-size: 18px;
            font-weight: 600;
            color: #1e293b;
            margin-top: 36px;
            margin-bottom: 16px;
            padding: 12px 16px;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border-left: 4px solid #3b82f6;
            border-radius: 0 10px 10px 0;
        }

        .report-content h3 {
            font-size: 16px;
            font-weight: 600;
            color: #334155;
            margin-top: 24px;
            margin-bottom: 12px;
            padding-left: 8px;
        }

        .report-content p {
            margin-bottom: 16px;
            padding-left: 8px;
            padding-right: 8px;
            color: #475569;
        }

        .report-content ul, .report-content ol {
            margin-bottom: 20px;
            padding-left: 32px;
            padding-right: 8px;
        }

        .report-content li {
            margin-bottom: 10px;
            color: #475569;
        }

        .report-content li::marker {
            color: #3b82f6;
        }

        .report-content strong {
            font-weight: 600;
            color: #1e293b;
            background: #eff6ff;
            padding: 2px 8px;
            border-radius: 4px;
        }

        .report-content blockquote {
            border-left: 4px solid #3b82f6;
            padding: 16px 20px;
            margin: 24px 8px;
            background: #f8fafc;
            border-radius: 0 10px 10px 0;
            font-style: italic;
            color: #64748b;
        }

        .report-content table {
            width: calc(100% - 16px);
            border-collapse: collapse;
            margin: 24px 8px;
            font-size: 14px;
            border-radius: 10px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }

        .report-content th {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            font-weight: 600;
            color: white;
            padding: 12px 16px;
            text-align: left;
            font-size: 13px;
        }

        .report-content td {
            padding: 12px 16px;
            border-bottom: 1px solid #e2e8f0;
            color: #475569;
        }

        .report-content tr:nth-child(even) {
            background: #f8fafc;
        }

        .report-content tr:hover {
            background: #f1f5f9;
        }

        .report-content h2:first-of-type {
            margin-top: 0;
        }
    </style>
</div>
