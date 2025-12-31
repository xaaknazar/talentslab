<div class="min-h-screen" style="background-color: #F8FAFF;">
    <div class="max-w-[1600px] mx-auto px-6 lg:px-10 py-8">
        <!-- Header -->
        <div class="mb-8">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium transition-colors group" style="color: #3B82F6;">
                <svg class="w-4 h-4 mr-1.5 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Вернуться в Dashboard
            </a>
            <div class="mt-4">
                <h1 class="text-2xl font-bold" style="color: #0F172A;">AI Анализ кандидата</h1>
                <p class="mt-1 text-sm" style="color: #64748B;">Глубинный анализ личности на основе комплексной психодиагностики</p>
            </div>
        </div>

        <!-- Main Grid -->
        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Left Column - AI Report -->
            <div class="w-full lg:w-[78%] order-2 lg:order-1">
                <div class="bg-white rounded-2xl shadow-sm min-h-[850px]" style="border: 1px solid #E5E7EB;">
                    <!-- Report Header -->
                    <div class="px-8 py-5 flex items-center justify-between rounded-t-2xl" style="border-bottom: 1px solid #E5E7EB; background: linear-gradient(135deg, #F8FAFF 0%, #EEF2FF 100%);">
                        <div class="flex items-center">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%);">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h2 class="text-lg font-semibold" style="color: #0F172A;">Аналитический отчет</h2>
                                <p class="text-xs" style="color: #64748B;">Executive-консультация по кандидату</p>
                            </div>
                        </div>
                        @if($report)
                            <button
                                wire:click="clearReport"
                                class="text-sm font-medium transition-colors flex items-center hover:opacity-70"
                                style="color: #64748B;"
                            >
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Новый анализ
                            </button>
                        @endif
                    </div>

                    <!-- Report Content -->
                    <div class="p-8 lg:p-10">
                        @if($isLoading)
                            <div class="flex flex-col items-center justify-center py-32">
                                <div class="relative">
                                    <div class="w-16 h-16 rounded-full" style="border: 4px solid #E5E7EB;"></div>
                                    <div class="w-16 h-16 rounded-full border-t-transparent animate-spin absolute top-0 left-0" style="border: 4px solid #3B82F6; border-top-color: transparent;"></div>
                                </div>
                                <p class="mt-6 text-base font-medium" style="color: #0F172A;">Формируем глубинный анализ...</p>
                                <p class="mt-2 text-sm" style="color: #64748B;">Это может занять 1-2 минуты</p>
                                <div class="mt-6 flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full animate-pulse" style="background-color: #3B82F6;"></div>
                                    <div class="w-2 h-2 rounded-full animate-pulse" style="background-color: #3B82F6; animation-delay: 0.2s;"></div>
                                    <div class="w-2 h-2 rounded-full animate-pulse" style="background-color: #3B82F6; animation-delay: 0.4s;"></div>
                                </div>
                            </div>
                        @elseif($error)
                            <div class="rounded-xl p-6" style="background-color: #FEF2F2; border: 1px solid #FECACA;">
                                <div class="flex items-start">
                                    <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0" style="background-color: #FEE2E2;">
                                        <svg class="w-5 h-5" style="color: #DC2626;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    <div class="ml-4">
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
                            <div class="flex flex-col items-center justify-center py-32 text-center">
                                <div class="w-20 h-20 rounded-2xl flex items-center justify-center mb-6" style="background: linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 100%);">
                                    <svg class="w-10 h-10" style="color: #3B82F6;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-semibold mb-3" style="color: #0F172A;">Готов к анализу</h3>
                                <p class="text-sm max-w-md leading-relaxed" style="color: #64748B;">
                                    Загрузите PDF с результатами тестирования для получения глубинного анализа личности, рекомендаций по карьере и идеальной рабочей среде
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column - Upload & Settings -->
            <div class="w-full lg:w-[22%] space-y-5 order-1 lg:order-2">
                <!-- Upload Card -->
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #E5E7EB;">
                    <div class="px-5 py-4" style="border-bottom: 1px solid #E5E7EB; background: linear-gradient(135deg, #F8FAFF 0%, #EEF2FF 100%);">
                        <h3 class="text-sm font-semibold flex items-center" style="color: #0F172A;">
                            <svg class="w-4 h-4 mr-2" style="color: #3B82F6;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            Загрузка PDF
                        </h3>
                    </div>
                    <div class="p-4">
                        @if(!$fileName)
                            <div
                                x-data="{ isDragging: false }"
                                x-on:dragover.prevent="isDragging = true"
                                x-on:dragleave.prevent="isDragging = false"
                                x-on:drop.prevent="isDragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'));"
                                :class="{ 'border-blue-400 bg-blue-50': isDragging }"
                                class="border-2 border-dashed rounded-xl p-6 text-center cursor-pointer hover:border-blue-400 hover:bg-blue-50/50 transition-all"
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
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-3" style="background: linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 100%);">
                                        <svg class="w-6 h-6" style="color: #3B82F6;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                        </svg>
                                    </div>
                                    <p class="text-sm" style="color: #334155;">
                                        <span class="font-semibold" style="color: #3B82F6;">Выберите файл</span>
                                        <br>или перетащите сюда
                                    </p>
                                    <p class="text-xs mt-2" style="color: #64748B;">PDF до 20 МБ</p>
                                </div>
                            </div>
                        @else
                            <div class="rounded-xl p-4" style="background-color: #F8FAFF; border: 1px solid #E5E7EB;">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center min-w-0 flex-1">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background-color: #FEE2E2;">
                                            <svg class="w-5 h-5" style="color: #DC2626;" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-3 min-w-0">
                                            <p class="text-sm font-medium truncate" style="color: #0F172A;">{{ $fileName }}</p>
                                            <p class="text-xs" style="color: #64748B;">Готов к анализу</p>
                                        </div>
                                    </div>
                                    <button
                                        wire:click="removePdf"
                                        class="ml-2 p-2 rounded-lg transition-colors hover:bg-red-50"
                                        style="color: #64748B;"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endif

                        @error('pdfFile')
                            <p class="mt-3 text-xs" style="color: #DC2626;">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Comments Card -->
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden" style="border: 1px solid #E5E7EB;">
                    <div class="px-5 py-4" style="border-bottom: 1px solid #E5E7EB; background: linear-gradient(135deg, #F8FAFF 0%, #EEF2FF 100%);">
                        <h3 class="text-sm font-semibold flex items-center" style="color: #0F172A;">
                            <svg class="w-4 h-4 mr-2" style="color: #3B82F6;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                            </svg>
                            Контекст
                        </h3>
                    </div>
                    <div class="p-4">
                        <textarea
                            wire:model="comment"
                            rows="3"
                            class="w-full rounded-xl px-4 py-3 text-sm resize-none transition-all focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            style="border: 1px solid #E5E7EB; color: #334155;"
                            placeholder="Вакансия, требования или фокус анализа..."
                        ></textarea>
                    </div>
                </div>

                <!-- Generate Button -->
                <button
                    wire:click="generateReport"
                    wire:loading.attr="disabled"
                    wire:target="generateReport"
                    :disabled="$wire.isLoading || !$wire.fileName"
                    class="w-full h-14 text-white text-sm font-semibold rounded-xl transition-all flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed"
                    style="background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%); box-shadow: 0 4px 14px 0 rgba(59, 130, 246, 0.35);"
                    onmouseover="this.style.boxShadow='0 6px 20px 0 rgba(59, 130, 246, 0.45)'"
                    onmouseout="this.style.boxShadow='0 4px 14px 0 rgba(59, 130, 246, 0.35)'"
                >
                    <span wire:loading.remove wire:target="generateReport">
                        <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5"></path>
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
                <div class="rounded-2xl p-5" style="background: linear-gradient(135deg, #F8FAFF 0%, #EEF2FF 100%); border: 1px solid #E5E7EB;">
                    <div class="flex items-start">
                        <div class="w-9 h-9 rounded-lg flex items-center justify-center flex-shrink-0" style="background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%);">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h4 class="text-xs font-semibold" style="color: #0F172A;">Что включает анализ</h4>
                            <ul class="mt-2 text-xs space-y-1 leading-relaxed" style="color: #64748B;">
                                <li>• Глубинный портрет личности</li>
                                <li>• Психотипы и метапрограммы</li>
                                <li>• Идеальная профессия</li>
                                <li>• Рекомендации по среде</li>
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
            font-size: 15px;
            line-height: 1.8;
        }

        .report-content h1 {
            font-size: 26px;
            font-weight: 700;
            color: #0F172A;
            margin-top: 0;
            margin-bottom: 28px;
            line-height: 1.3;
        }

        .report-content h2 {
            font-size: 18px;
            font-weight: 600;
            color: #0F172A;
            margin-top: 40px;
            margin-bottom: 16px;
            padding-bottom: 12px;
            border-bottom: 2px solid #E5E7EB;
            line-height: 1.4;
        }

        .report-content h2::before {
            content: '';
            display: inline-block;
            width: 4px;
            height: 20px;
            background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%);
            border-radius: 2px;
            margin-right: 12px;
            vertical-align: middle;
        }

        .report-content h3 {
            font-size: 16px;
            font-weight: 600;
            color: #1E293B;
            margin-top: 28px;
            margin-bottom: 12px;
            line-height: 1.4;
        }

        .report-content p {
            margin-bottom: 16px;
            color: #334155;
        }

        .report-content ul, .report-content ol {
            margin-bottom: 20px;
            padding-left: 24px;
        }

        .report-content li {
            margin-bottom: 10px;
            color: #334155;
        }

        .report-content li::marker {
            color: #3B82F6;
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
            padding: 20px 24px;
            margin: 28px 0;
            color: #334155;
            background: #F8FAFF;
            border-radius: 0 12px 12px 0;
        }

        .report-content hr {
            border: none;
            border-top: 2px solid #E5E7EB;
            margin: 40px 0;
        }

        .report-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 28px 0;
            font-size: 14px;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #E5E7EB;
        }

        .report-content th {
            background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%);
            font-weight: 600;
            color: white;
            text-align: left;
            padding: 14px 18px;
        }

        .report-content td {
            padding: 14px 18px;
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
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 13px;
            color: #3B82F6;
            font-weight: 500;
        }

        .report-content h2:first-of-type {
            margin-top: 0;
        }

        /* Highlight boxes for key insights */
        .report-content blockquote strong {
            color: #1D4ED8;
        }
    </style>
</div>
