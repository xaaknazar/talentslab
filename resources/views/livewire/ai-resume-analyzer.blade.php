<div class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <div class="max-w-[1400px] mx-auto px-6 lg:px-8 py-8">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-blue-600 hover:text-blue-700 transition-colors group">
                <svg class="w-4 h-4 mr-1.5 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Dashboard
            </a>
        </div>

        <!-- Main Grid -->
        <div class="flex flex-col lg:flex-row gap-5">
            <!-- Left Column - AI Report -->
            <div class="w-full lg:w-[72%] order-2 lg:order-1">
                <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm shadow-slate-200/50 min-h-[750px]">
                    <!-- Report Header -->
                    <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-gradient-to-r from-blue-600 to-indigo-600 rounded-t-2xl">
                        <h2 class="text-lg font-semibold text-white flex items-center">
                            <svg class="w-5 h-5 mr-2 opacity-90" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Аналитический отчет
                        </h2>
                        @if($report)
                            <button
                                wire:click="clearReport"
                                class="text-[13px] font-medium text-white/80 hover:text-white transition-colors flex items-center"
                            >
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Очистить
                            </button>
                        @endif
                    </div>

                    <!-- Report Content -->
                    <div class="p-6 lg:p-8">
                        @if($isLoading)
                            <div class="flex flex-col items-center justify-center py-28">
                                <div class="relative">
                                    <div class="w-16 h-16 border-4 border-blue-100 rounded-full"></div>
                                    <div class="w-16 h-16 border-4 border-blue-600 rounded-full border-t-transparent animate-spin absolute top-0 left-0"></div>
                                </div>
                                <p class="mt-5 text-base text-slate-700 font-medium">Анализируем резюме...</p>
                                <p class="mt-1 text-sm text-slate-500">Это может занять 1-2 минуты</p>
                            </div>
                        @elseif($error)
                            <div class="bg-red-50 border border-red-200 rounded-xl p-5">
                                <div class="flex items-start">
                                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
                                        <h3 class="text-sm font-semibold text-red-800">Ошибка</h3>
                                        <p class="mt-1 text-sm text-red-600 leading-relaxed">{{ $error }}</p>
                                    </div>
                                </div>
                            </div>
                        @elseif($report)
                            <article class="report-content">
                                {!! \Illuminate\Support\Str::markdown($report) !!}
                            </article>
                        @else
                            <div class="flex flex-col items-center justify-center py-28 text-center">
                                <div class="w-20 h-20 bg-gradient-to-br from-blue-100 to-indigo-100 rounded-2xl flex items-center justify-center mb-5 shadow-sm">
                                    <svg class="w-10 h-10 text-blue-600" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-slate-800 mb-2">Отчет пока не сформирован</h3>
                                <p class="text-sm text-slate-500 max-w-sm leading-relaxed">
                                    Загрузите PDF-резюме и нажмите «Сформировать отчет» для получения AI-анализа
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column - Upload & Settings -->
            <div class="w-full lg:w-[28%] space-y-4 order-1 lg:order-2">
                <!-- Upload Card -->
                <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm shadow-slate-200/50 overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-100 bg-gradient-to-r from-blue-600 to-indigo-600">
                        <h3 class="text-sm font-semibold text-white flex items-center">
                            <svg class="w-4 h-4 mr-2 opacity-90" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            Загрузка резюме
                        </h3>
                    </div>
                    <div class="p-4">
                        @if(!$fileName)
                            <div
                                x-data="{ isDragging: false }"
                                x-on:dragover.prevent="isDragging = true"
                                x-on:dragleave.prevent="isDragging = false"
                                x-on:drop.prevent="isDragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'));"
                                :class="{ 'border-blue-500 bg-blue-50': isDragging }"
                                class="border-2 border-dashed border-slate-200 rounded-xl p-5 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50/50 transition-all"
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
                                    <div class="w-11 h-11 bg-blue-100 rounded-xl flex items-center justify-center mb-3">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                    </div>
                                    <p class="text-sm text-slate-600">
                                        <span class="text-blue-600 font-semibold">Выберите</span> или перетащите
                                    </p>
                                    <p class="text-xs text-slate-400 mt-1">PDF до 20 МБ</p>
                                </div>
                            </div>
                        @else
                            <div class="border border-slate-200 rounded-xl p-3 bg-slate-50">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center min-w-0 flex-1">
                                        <div class="w-9 h-9 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-4 h-4 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-3 min-w-0">
                                            <p class="text-sm font-medium text-slate-800 truncate">{{ $fileName }}</p>
                                            <p class="text-xs text-slate-500">PDF</p>
                                        </div>
                                    </div>
                                    <button
                                        wire:click="removePdf"
                                        class="ml-2 p-1.5 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endif

                        @error('pdfFile')
                            <p class="mt-2 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Comments Card -->
                <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm shadow-slate-200/50 overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-100 bg-gradient-to-r from-slate-600 to-slate-700">
                        <h3 class="text-sm font-semibold text-white flex items-center">
                            <svg class="w-4 h-4 mr-2 opacity-90" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                            </svg>
                            Комментарии
                        </h3>
                    </div>
                    <div class="p-4">
                        <textarea
                            wire:model="comment"
                            rows="3"
                            class="w-full border border-slate-200 rounded-xl px-3 py-2.5 text-sm text-slate-700 placeholder-slate-400 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none transition-all"
                            placeholder="Контекст или запрос к анализу..."
                        ></textarea>
                    </div>
                </div>

                <!-- Generate Button -->
                <button
                    wire:click="generateReport"
                    wire:loading.attr="disabled"
                    wire:target="generateReport"
                    :disabled="$wire.isLoading || !$wire.fileName"
                    class="w-full h-12 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 disabled:from-slate-300 disabled:to-slate-300 disabled:cursor-not-allowed text-white text-sm font-semibold rounded-xl transition-all shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 disabled:shadow-none flex items-center justify-center"
                >
                    <span wire:loading.remove wire:target="generateReport">
                        <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Сформировать отчет
                    </span>
                    <span wire:loading wire:target="generateReport" class="flex items-center">
                        <svg class="animate-spin mr-2 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Анализируем...
                    </span>
                </button>

                <!-- Info Card -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 border border-blue-200/50 rounded-2xl p-4">
                    <div class="flex items-start">
                        <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-xs font-semibold text-slate-700">Анализ включает</h4>
                            <ul class="mt-1.5 text-xs text-slate-600 space-y-0.5 leading-relaxed">
                                <li>• Психотипы, MBTI, Гарднер</li>
                                <li>• Профессиональные функции</li>
                                <li>• Потенциал и стили управления</li>
                                <li>• Метапрограммы</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .report-content {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: #334155;
            font-size: 14px;
            line-height: 1.75;
        }

        .report-content h1 {
            font-size: 24px;
            font-weight: 700;
            color: #0f172a;
            margin-top: 0;
            margin-bottom: 24px;
            line-height: 1.3;
        }

        .report-content h2 {
            font-size: 17px;
            font-weight: 600;
            color: #1e40af;
            margin-top: 36px;
            margin-bottom: 14px;
            padding-bottom: 10px;
            border-bottom: 2px solid #dbeafe;
            line-height: 1.4;
        }

        .report-content h3 {
            font-size: 15px;
            font-weight: 600;
            color: #1e293b;
            margin-top: 24px;
            margin-bottom: 10px;
            line-height: 1.4;
        }

        .report-content p {
            margin-bottom: 16px;
            color: #475569;
        }

        .report-content ul, .report-content ol {
            margin-bottom: 18px;
            padding-left: 22px;
        }

        .report-content li {
            margin-bottom: 10px;
            color: #475569;
        }

        .report-content strong {
            font-weight: 600;
            color: #1e293b;
        }

        .report-content em {
            font-style: italic;
            color: #64748b;
        }

        .report-content blockquote {
            border-left: 4px solid #3b82f6;
            padding-left: 18px;
            margin: 24px 0;
            color: #475569;
            background: #f8fafc;
            padding: 16px 18px;
            border-radius: 0 8px 8px 0;
        }

        .report-content hr {
            border: none;
            border-top: 2px solid #e2e8f0;
            margin: 36px 0;
        }

        .report-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 24px 0;
            font-size: 13px;
            border-radius: 8px;
            overflow: hidden;
        }

        .report-content th {
            background: linear-gradient(to right, #3b82f6, #6366f1);
            font-weight: 600;
            color: white;
            text-align: left;
            padding: 12px 14px;
        }

        .report-content td {
            padding: 12px 14px;
            border-bottom: 1px solid #e2e8f0;
            color: #475569;
        }

        .report-content tr:last-child td {
            border-bottom: none;
        }

        .report-content tr:nth-child(even) {
            background: #f8fafc;
        }

        .report-content code {
            background: #eff6ff;
            padding: 3px 8px;
            border-radius: 6px;
            font-size: 13px;
            color: #1e40af;
            font-weight: 500;
        }

        .report-content h2:first-of-type {
            margin-top: 0;
        }
    </style>
</div>
