<div class="min-h-screen bg-[#F8FAFF]">
    <div class="max-w-[1400px] mx-auto px-8 py-10">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium text-[#3B82F6] hover:text-[#2563EB] transition-colors">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Dashboard
            </a>
        </div>

        <!-- Main Grid -->
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Left Column - AI Report (70%) -->
            <div class="w-full lg:w-[70%] order-2 lg:order-1">
                <div class="bg-white rounded-xl border border-[#EAECEF] shadow-[0_1px_2px_rgba(0,0,0,0.04)] min-h-[800px]">
                    <!-- Report Header -->
                    <div class="px-6 py-5 border-b border-[#EAECEF] flex items-center justify-between">
                        <h2 class="text-[20px] font-semibold text-[#0F172A] leading-7">Аналитический отчет</h2>
                        @if($report)
                            <button
                                wire:click="clearReport"
                                class="text-[13px] font-medium text-[#64748B] hover:text-[#334155] transition-colors flex items-center"
                            >
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Очистить
                            </button>
                        @endif
                    </div>

                    <!-- Report Content -->
                    <div class="p-6 lg:p-8">
                        @if($isLoading)
                            <div class="flex flex-col items-center justify-center py-32">
                                <div class="relative">
                                    <div class="w-14 h-14 border-[3px] border-[#E5E7EB] rounded-full"></div>
                                    <div class="w-14 h-14 border-[3px] border-[#3B82F6] rounded-full border-t-transparent animate-spin absolute top-0 left-0"></div>
                                </div>
                                <p class="mt-5 text-[15px] text-[#334155] font-medium">Анализируем резюме...</p>
                                <p class="mt-1.5 text-[13px] text-[#64748B]">Это может занять 1-2 минуты</p>
                            </div>
                        @elseif($error)
                            <div class="bg-red-50 border border-red-100 rounded-lg p-5">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 text-red-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div class="ml-3">
                                        <h3 class="text-[14px] font-semibold text-red-800">Ошибка</h3>
                                        <p class="mt-1 text-[13px] text-red-600 leading-5">{{ $error }}</p>
                                    </div>
                                </div>
                            </div>
                        @elseif($report)
                            <article class="report-content">
                                {!! \Illuminate\Support\Str::markdown($report) !!}
                            </article>
                        @else
                            <div class="flex flex-col items-center justify-center py-32 text-center">
                                <div class="w-16 h-16 bg-[#F1F5F9] rounded-full flex items-center justify-center mb-5">
                                    <svg class="w-8 h-8 text-[#94A3B8]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-[16px] font-semibold text-[#1E293B] mb-2">Отчет пока не сформирован</h3>
                                <p class="text-[13px] text-[#64748B] max-w-sm leading-5">
                                    Загрузите PDF-резюме и нажмите «Сформировать отчет» для получения AI-анализа
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column - Upload & Settings (30%) -->
            <div class="w-full lg:w-[30%] space-y-6 order-1 lg:order-2">
                <!-- Upload Card -->
                <div class="bg-white rounded-xl border border-[#EAECEF] shadow-[0_1px_2px_rgba(0,0,0,0.04)]">
                    <div class="px-5 py-4 border-b border-[#EAECEF]">
                        <h3 class="text-[15px] font-semibold text-[#0F172A]">Загрузка резюме</h3>
                    </div>
                    <div class="p-5">
                        @if(!$fileName)
                            <div
                                x-data="{ isDragging: false }"
                                x-on:dragover.prevent="isDragging = true"
                                x-on:dragleave.prevent="isDragging = false"
                                x-on:drop.prevent="isDragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'));"
                                :class="{ 'border-[#3B82F6] bg-blue-50': isDragging }"
                                class="border-2 border-dashed border-[#CBD5E1] rounded-xl p-8 text-center cursor-pointer hover:border-[#3B82F6] hover:bg-[#F8FAFF] transition-all"
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
                                    <div class="w-12 h-12 bg-[#F1F5F9] rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-6 h-6 text-[#64748B]" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                    </div>
                                    <p class="text-[13px] text-[#475569] mb-1">
                                        <span class="text-[#3B82F6] font-medium">Выберите файл</span>
                                        <span class="hidden sm:inline"> или перетащите сюда</span>
                                    </p>
                                    <p class="text-[12px] text-[#94A3B8]">PDF до 20 МБ</p>
                                </div>
                            </div>
                        @else
                            <div class="border border-[#E5E7EB] rounded-xl p-4 bg-[#F8FAFC]">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center min-w-0 flex-1">
                                        <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-3 min-w-0">
                                            <p class="text-[13px] font-medium text-[#0F172A] truncate">{{ $fileName }}</p>
                                            <p class="text-[12px] text-[#64748B]">PDF документ</p>
                                        </div>
                                    </div>
                                    <button
                                        wire:click="removePdf"
                                        class="ml-2 p-1.5 text-[#94A3B8] hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors flex-shrink-0"
                                    >
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endif

                        @error('pdfFile')
                            <p class="mt-2 text-[13px] text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Comments Card -->
                <div class="bg-white rounded-xl border border-[#EAECEF] shadow-[0_1px_2px_rgba(0,0,0,0.04)]">
                    <div class="px-5 py-4 border-b border-[#EAECEF]">
                        <h3 class="text-[15px] font-semibold text-[#0F172A]">Комментарии</h3>
                    </div>
                    <div class="p-5">
                        <textarea
                            wire:model="comment"
                            rows="4"
                            class="w-full border border-[#E5E7EB] rounded-lg px-4 py-3 text-[13px] text-[#334155] placeholder-[#94A3B8] focus:ring-2 focus:ring-[#3B82F6] focus:border-[#3B82F6] resize-none transition-colors"
                            placeholder="Добавьте контекст или запрос к анализу..."
                        ></textarea>
                        <p class="mt-2 text-[12px] text-[#94A3B8]">Необязательно</p>
                    </div>
                </div>

                <!-- Generate Button -->
                <button
                    wire:click="generateReport"
                    wire:loading.attr="disabled"
                    wire:target="generateReport"
                    :disabled="$wire.isLoading || !$wire.fileName"
                    class="w-full h-11 bg-[#3B82F6] hover:bg-[#2563EB] disabled:bg-[#E5E7EB] disabled:cursor-not-allowed text-white text-[14px] font-medium rounded-lg transition-colors flex items-center justify-center"
                >
                    <span wire:loading.remove wire:target="generateReport">
                        <svg class="w-4 h-4 mr-2 inline-block" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Сформировать отчет
                    </span>
                    <span wire:loading wire:target="generateReport" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Анализируем...
                    </span>
                </button>

                <!-- Info Card -->
                <div class="bg-[#F8FAFF] border border-[#E0E7FF] rounded-xl p-5">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-[#3B82F6] mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="ml-3">
                            <h4 class="text-[13px] font-semibold text-[#1E293B]">Что анализирует система</h4>
                            <ul class="mt-2 text-[12px] text-[#475569] space-y-1 leading-5">
                                <li>Психотипы и MBTI</li>
                                <li>Профессиональные функции</li>
                                <li>Потенциал к управлению</li>
                                <li>Уровни мышления</li>
                                <li>Стили менеджмента</li>
                                <li>Метапрограммы и Гарднер</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Report Typography - Inter Font */
        .report-content {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: #334155;
            font-size: 14px;
            line-height: 1.7;
        }

        .report-content h1 {
            font-size: 22px;
            font-weight: 600;
            color: #0F172A;
            margin-top: 0;
            margin-bottom: 20px;
            line-height: 1.3;
        }

        .report-content h2 {
            font-size: 16px;
            font-weight: 600;
            color: #1E293B;
            margin-top: 32px;
            margin-bottom: 12px;
            padding-bottom: 8px;
            border-bottom: 1px solid #E5E7EB;
            line-height: 1.4;
        }

        .report-content h3 {
            font-size: 14px;
            font-weight: 600;
            color: #334155;
            margin-top: 24px;
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .report-content p {
            margin-bottom: 16px;
            color: #475569;
        }

        .report-content ul, .report-content ol {
            margin-bottom: 16px;
            padding-left: 20px;
        }

        .report-content li {
            margin-bottom: 8px;
            color: #475569;
        }

        .report-content strong {
            font-weight: 600;
            color: #1E293B;
        }

        .report-content em {
            font-style: italic;
            color: #64748B;
        }

        .report-content blockquote {
            border-left: 3px solid #3B82F6;
            padding-left: 16px;
            margin: 20px 0;
            color: #475569;
            font-style: italic;
        }

        .report-content hr {
            border: none;
            border-top: 1px solid #E5E7EB;
            margin: 32px 0;
        }

        .report-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 13px;
        }

        .report-content th {
            background: #F8FAFC;
            font-weight: 600;
            color: #1E293B;
            text-align: left;
            padding: 12px;
            border: 1px solid #E5E7EB;
        }

        .report-content td {
            padding: 12px;
            border: 1px solid #E5E7EB;
            color: #475569;
        }

        .report-content code {
            background: #F1F5F9;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 13px;
            color: #334155;
        }

        /* Percentage highlights */
        .report-content strong:has(+ em),
        .report-content strong {
            color: #0F172A;
        }

        /* Section dividers */
        .report-content h2:first-of-type {
            margin-top: 0;
        }
    </style>
</div>
