<div class="py-6 sm:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-6 sm:mb-8">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Dashboard
            </a>
        </div>

        <!-- Main Grid -->
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Left Column - AI Report (78%) -->
            <div class="w-full lg:w-[78%] order-2 lg:order-1">
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm min-h-[500px] sm:min-h-[600px]">
                    <!-- Report Header -->
                    <div class="px-5 sm:px-6 py-4 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <h2 class="text-lg sm:text-xl font-semibold text-gray-800">Аналитический отчет</h2>
                        @if($report)
                            <button
                                wire:click="clearReport"
                                class="self-start sm:self-auto text-sm text-gray-500 hover:text-gray-700 transition-colors flex items-center"
                            >
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Очистить
                            </button>
                        @endif
                    </div>

                    <!-- Report Content -->
                    <div class="p-5 sm:p-6">
                        @if($isLoading)
                            <div class="flex flex-col items-center justify-center py-12 sm:py-20">
                                <div class="relative">
                                    <div class="w-12 h-12 sm:w-16 sm:h-16 border-4 border-indigo-100 rounded-full"></div>
                                    <div class="w-12 h-12 sm:w-16 sm:h-16 border-4 border-indigo-600 rounded-full border-t-transparent animate-spin absolute top-0 left-0"></div>
                                </div>
                                <p class="mt-4 text-sm sm:text-base text-gray-600 font-medium">Анализируем резюме...</p>
                                <p class="mt-1 text-xs sm:text-sm text-gray-400">Это может занять 1-2 минуты</p>
                            </div>
                        @elseif($error)
                            <div class="bg-red-50 border border-red-100 rounded-lg p-4">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-medium text-red-800">Ошибка</h3>
                                        <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
                                    </div>
                                </div>
                            </div>
                        @elseif($report)
                            <div class="prose prose-sm sm:prose max-w-none prose-headings:text-gray-800 prose-headings:font-semibold prose-p:text-gray-600 prose-li:text-gray-600 prose-strong:text-gray-700">
                                {!! \Illuminate\Support\Str::markdown($report) !!}
                            </div>
                        @else
                            <div class="flex flex-col items-center justify-center py-12 sm:py-20 text-center">
                                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 sm:w-10 sm:h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-base sm:text-lg font-medium text-gray-700 mb-2">Отчет пока не сформирован</h3>
                                <p class="text-sm text-gray-500 max-w-sm px-4">
                                    Загрузите PDF-резюме и нажмите «Сформировать отчет» для получения AI-анализа
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column - Upload & Settings (22%) -->
            <div class="w-full lg:w-[22%] space-y-5 order-1 lg:order-2">
                <!-- Upload Card -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                    <div class="px-5 sm:px-6 py-4 border-b border-gray-100">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-800">Загрузка резюме</h3>
                    </div>
                    <div class="p-5 sm:p-6">
                        @if(!$fileName)
                            <div
                                x-data="{ isDragging: false }"
                                x-on:dragover.prevent="isDragging = true"
                                x-on:dragleave.prevent="isDragging = false"
                                x-on:drop.prevent="isDragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'));"
                                :class="{ 'border-indigo-400 bg-indigo-50': isDragging }"
                                class="border-2 border-dashed border-gray-200 rounded-xl p-6 sm:p-8 text-center cursor-pointer hover:border-indigo-300 hover:bg-gray-50 transition-all"
                                x-on:click="$refs.fileInput.click()"
                            >
                                <input
                                    type="file"
                                    wire:model="pdfFile"
                                    accept=".pdf"
                                    class="hidden"
                                    x-ref="fileInput"
                                >
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 sm:w-14 sm:h-14 bg-gray-100 rounded-full flex items-center justify-center mb-3 sm:mb-4">
                                        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                    </div>
                                    <p class="text-sm text-gray-600 mb-1">
                                        <span class="hidden sm:inline">Перетащите файл сюда или </span>
                                        <span class="text-indigo-600 font-medium">выберите файл</span>
                                    </p>
                                    <p class="text-xs text-gray-400">PDF до 20 МБ</p>
                                </div>
                            </div>
                        @else
                            <div class="border border-gray-200 rounded-xl p-4 bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center min-w-0 flex-1">
                                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-3 min-w-0">
                                            <p class="text-sm font-medium text-gray-800 truncate">{{ $fileName }}</p>
                                            <p class="text-xs text-gray-500">PDF документ</p>
                                        </div>
                                    </div>
                                    <button
                                        wire:click="removePdf"
                                        class="ml-2 p-1.5 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors flex-shrink-0"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endif

                        @error('pdfFile')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Comments Card -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm">
                    <div class="px-5 sm:px-6 py-4 border-b border-gray-100">
                        <h3 class="text-base sm:text-lg font-semibold text-gray-800">Комментарии</h3>
                    </div>
                    <div class="p-5 sm:p-6">
                        <textarea
                            wire:model="comment"
                            rows="4"
                            class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm text-gray-700 placeholder-gray-400 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 resize-none transition-colors"
                            placeholder="Добавьте комментарии, контекст или запрос к анализу. Например: «Оцените потенциал к управлению командой» или «Акцент на стратегические компетенции»..."
                        ></textarea>
                        <p class="mt-2 text-xs text-gray-400">Необязательно. Дополнительный контекст для более точного анализа.</p>
                    </div>
                </div>

                <!-- Generate Button -->
                <button
                    wire:click="generateReport"
                    wire:loading.attr="disabled"
                    wire:target="generateReport"
                    :disabled="$wire.isLoading || !$wire.fileName"
                    class="w-full bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white font-medium py-3 sm:py-3.5 px-6 rounded-xl transition-colors shadow-sm flex items-center justify-center"
                >
                    <span wire:loading.remove wire:target="generateReport">
                        <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Сформировать отчет
                    </span>
                    <span wire:loading wire:target="generateReport" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Анализируем...
                    </span>
                </button>

                <!-- Info Card -->
                <div class="bg-indigo-50 border border-indigo-100 rounded-xl p-4 sm:p-5">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-indigo-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="ml-3">
                            <h4 class="text-sm font-medium text-indigo-800">Что анализирует система</h4>
                            <ul class="mt-2 text-xs text-indigo-700 space-y-1">
                                <li>• Профессиональный профиль и карьерный вектор</li>
                                <li>• Управленческий потенциал и стили</li>
                                <li>• Уровни мышления и поведенческие паттерны</li>
                                <li>• Сильные стороны и зоны роста</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
