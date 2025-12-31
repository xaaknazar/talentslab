<div class="min-h-screen" style="background-color: #F8FAFF;">
    <div class="max-w-[1600px] mx-auto px-6 lg:px-10 py-8">
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium transition-colors group" style="color: #3B82F6;">
                <svg class="w-4 h-4 mr-1.5 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Вернуться в Dashboard
            </a>
            <div class="mt-3">
                <h1 class="text-2xl font-bold" style="color: #0F172A;">AI Анализ кандидата</h1>
                <p class="mt-1 text-sm" style="color: #64748B;">Глубинный анализ личности на основе комплексной психодиагностики</p>
            </div>
        </div>

        <!-- Main Grid -->
        <div class="flex flex-col lg:flex-row gap-5">
            <!-- Left Column - AI Report -->
            <div class="w-full lg:w-[82%] order-2 lg:order-1">
                <div class="bg-white rounded-2xl shadow-sm min-h-[800px]" style="border: 1px solid #E5E7EB;">
                    <!-- Report Header -->
                    <div class="px-6 py-4 flex items-center justify-between rounded-t-2xl" style="border-bottom: 1px solid #E5E7EB; background: linear-gradient(135deg, #F8FAFF 0%, #EEF2FF 100%);">
                        <div class="flex items-center">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%);">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h2 class="text-base font-semibold" style="color: #0F172A;">Аналитический отчет</h2>
                                <p class="text-xs" style="color: #64748B;">Executive-консультация</p>
                            </div>
                        </div>
                        @if($report)
                            <button
                                wire:click="clearReport"
                                class="text-xs font-medium transition-colors flex items-center hover:opacity-70"
                                style="color: #64748B;"
                            >
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Новый анализ
                            </button>
                        @endif
                    </div>

                    <!-- Report Content -->
                    <div class="p-6 lg:p-8">
                        @if($isLoading)
                            <div class="flex flex-col items-center justify-center py-40">
                                <div class="relative">
                                    <div class="w-14 h-14 rounded-full" style="border: 3px solid #E5E7EB;"></div>
                                    <div class="w-14 h-14 rounded-full border-t-transparent animate-spin absolute top-0 left-0" style="border: 3px solid #3B82F6; border-top-color: transparent;"></div>
                                </div>
                                <p class="mt-5 text-base font-medium" style="color: #0F172A;">Формируем глубинный анализ...</p>
                                <p class="mt-1.5 text-sm" style="color: #64748B;">Это может занять 1-2 минуты</p>
                            </div>
                        @elseif($error)
                            <div class="rounded-xl p-5" style="background-color: #FEF2F2; border: 1px solid #FECACA;">
                                <div class="flex items-start">
                                    <div class="w-9 h-9 rounded-full flex items-center justify-center flex-shrink-0" style="background-color: #FEE2E2;">
                                        <svg class="w-4 h-4" style="color: #DC2626;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-semibold" style="color: #991B1B;">Ошибка анализа</h3>
                                        <p class="mt-1 text-sm leading-relaxed" style="color: #DC2626;">{{ $error }}</p>
                                    </div>
                                </div>
                            </div>
                        @elseif($report)
                            <article class="report-content">
                                {!! \Illuminate\Support\Str::markdown($report) !!}
                            </article>
                        @else
                            <div class="flex flex-col items-center justify-center py-44 text-center">
                                <div class="w-16 h-16 rounded-xl flex items-center justify-center mb-5" style="background: linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 100%);">
                                    <svg class="w-8 h-8" style="color: #3B82F6;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold mb-2" style="color: #0F172A;">Готов к анализу</h3>
                                <p class="text-sm leading-relaxed px-4" style="color: #64748B; max-width: 380px;">
                                    Загрузите PDF с результатами тестирования для получения глубинного анализа личности, рекомендаций по карьере и идеальной рабочей среде
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column - Upload & Settings -->
            <div class="w-full lg:w-[18%] space-y-3 order-1 lg:order-2">
                <!-- Upload Card -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden" style="border: 1px solid #E5E7EB;">
                    <div class="px-4 py-3" style="border-bottom: 1px solid #E5E7EB; background: linear-gradient(135deg, #F8FAFF 0%, #EEF2FF 100%);">
                        <h3 class="text-xs font-semibold flex items-center" style="color: #0F172A;">
                            <svg class="w-3.5 h-3.5 mr-1.5" style="color: #3B82F6;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            Загрузка PDF
                        </h3>
                    </div>
                    <div class="p-3">
                        @if(!$fileName)
                            <div
                                x-data="{ isDragging: false }"
                                x-on:dragover.prevent="isDragging = true"
                                x-on:dragleave.prevent="isDragging = false"
                                x-on:drop.prevent="isDragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'));"
                                :class="{ 'border-blue-400 bg-blue-50': isDragging }"
                                class="border-2 border-dashed rounded-lg p-4 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50/50 transition-all"
                                style="border-color: #E5E7EB;"
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
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center mb-2" style="background: linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 100%);">
                                        <svg class="w-5 h-5" style="color: #3B82F6;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                    </div>
                                    <p class="text-xs" style="color: #334155;">
                                        <span class="font-semibold" style="color: #3B82F6;">Выберите</span> или перетащите
                                    </p>
                                    <p class="text-[10px] mt-1" style="color: #64748B;">PDF до 20 МБ</p>
                                </div>
                            </div>
                        @else
                            <div class="rounded-lg p-3" style="background-color: #F8FAFF; border: 1px solid #E5E7EB;">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center min-w-0 flex-1">
                                        <div class="w-8 h-8 rounded-md flex items-center justify-center flex-shrink-0" style="background-color: #FEE2E2;">
                                            <svg class="w-4 h-4" style="color: #DC2626;" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-2 min-w-0">
                                            <p class="text-xs font-medium truncate" style="color: #0F172A;">{{ $fileName }}</p>
                                            <p class="text-[10px]" style="color: #64748B;">Готов</p>
                                        </div>
                                    </div>
                                    <button
                                        wire:click="removePdf"
                                        class="ml-1 p-1.5 rounded-md transition-colors hover:bg-red-50"
                                        style="color: #64748B;"
                                    >
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endif

                        @error('pdfFile')
                            <p class="mt-2 text-[10px]" style="color: #DC2626;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Comments Card -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden" style="border: 1px solid #E5E7EB;">
                    <div class="px-4 py-2.5" style="border-bottom: 1px solid #E5E7EB; background: linear-gradient(135deg, #F8FAFF 0%, #EEF2FF 100%);">
                        <h3 class="text-xs font-semibold flex items-center" style="color: #0F172A;">
                            <svg class="w-3.5 h-3.5 mr-1.5" style="color: #3B82F6;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                            </svg>
                            Контекст
                        </h3>
                    </div>
                    <div class="p-3">
                        <textarea
                            wire:model="comment"
                            rows="2"
                            class="w-full rounded-lg px-3 py-2 text-xs resize-none transition-all focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            style="border: 1px solid #E5E7EB; color: #334155;"
                            placeholder="Вакансия или фокус..."
                        ></textarea>
                    </div>
                </div>

                <!-- Generate Button -->
                <button
                    wire:click="generateReport"
                    wire:loading.attr="disabled"
                    wire:target="generateReport"
                    :disabled="$wire.isLoading || !$wire.fileName"
                    class="w-full h-10 text-white text-xs font-semibold rounded-lg transition-all flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed"
                    style="background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%); box-shadow: 0 2px 8px 0 rgba(59, 130, 246, 0.3);"
                >
                    <span wire:loading.remove wire:target="generateReport">
                        <svg class="w-4 h-4 mr-1.5 inline-block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Сформировать отчет
                    </span>
                    <span wire:loading wire:target="generateReport" class="flex items-center">
                        <svg class="animate-spin mr-1.5 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Анализ...
                    </span>
                </button>

                <!-- Info Card -->
                <div class="rounded-xl p-4" style="background: linear-gradient(135deg, #F8FAFF 0%, #EEF2FF 100%); border: 1px solid #E5E7EB;">
                    <div class="flex items-start">
                        <div class="w-7 h-7 rounded-md flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%);">
                            <svg class="w-3.5 h-3.5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h4 class="text-[11px] font-semibold" style="color: #0F172A;">Что включает анализ</h4>
                            <ul class="mt-1.5 text-[10px] space-y-1 leading-relaxed" style="color: #64748B;">
                                <li>• Глубинный портрет</li>
                                <li>• Психотипы</li>
                                <li>• Идеальная профессия</li>
                                <li>• Рекомендации</li>
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
            font-size: 22px;
            font-weight: 700;
            color: #0F172A;
            margin-top: 0;
            margin-bottom: 20px;
            padding-bottom: 12px;
            border-bottom: 2px solid #3B82F6;
            line-height: 1.3;
        }

        .report-content h2 {
            font-size: 16px;
            font-weight: 700;
            color: #0F172A;
            margin-top: 32px;
            margin-bottom: 14px;
            padding: 10px 14px;
            background: linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 100%);
            border-left: 4px solid #3B82F6;
            border-radius: 0 8px 8px 0;
            line-height: 1.4;
        }

        .report-content h3 {
            font-size: 14px;
            font-weight: 600;
            color: #1E293B;
            margin-top: 20px;
            margin-bottom: 10px;
            line-height: 1.4;
        }

        .report-content p {
            margin-bottom: 14px;
            color: #334155;
        }

        .report-content ul, .report-content ol {
            margin-bottom: 16px;
            padding-left: 20px;
        }

        .report-content li {
            margin-bottom: 8px;
            color: #334155;
            padding-left: 4px;
        }

        .report-content li::marker {
            color: #3B82F6;
            font-weight: 600;
        }

        .report-content strong {
            font-weight: 600;
            color: #0F172A;
        }

        .report-content em {
            font-style: italic;
            color: #64748B;
        }

        .report-content blockquote {
            border-left: 4px solid #3B82F6;
            padding: 14px 18px;
            margin: 20px 0;
            color: #334155;
            background: #F8FAFF;
            border-radius: 0 8px 8px 0;
            font-style: italic;
        }

        .report-content blockquote strong {
            color: #1D4ED8;
            font-style: normal;
        }

        .report-content hr {
            border: none;
            border-top: 1px solid #E5E7EB;
            margin: 28px 0;
        }

        .report-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 13px;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #E5E7EB;
        }

        .report-content th {
            background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%);
            font-weight: 600;
            color: white;
            text-align: left;
            padding: 10px 14px;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .report-content td {
            padding: 10px 14px;
            border-bottom: 1px solid #E5E7EB;
            color: #334155;
        }

        .report-content tr:last-child td {
            border-bottom: none;
        }

        .report-content tr:nth-child(even) {
            background: #F8FAFF;
        }

        .report-content code {
            background: #EEF2FF;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 12px;
            color: #3B82F6;
            font-weight: 500;
        }

        .report-content h2:first-of-type {
            margin-top: 0;
        }

        /* Percentage highlighting */
        .report-content p strong,
        .report-content li strong {
            background: linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 100%);
            padding: 1px 6px;
            border-radius: 4px;
        }
    </style>
</div>
