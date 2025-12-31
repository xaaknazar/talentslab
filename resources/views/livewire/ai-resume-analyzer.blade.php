<div class="min-h-screen" style="background-color: #F8FAFF;">
    <div class="mx-auto px-4 lg:px-6 py-4" style="max-width: 1920px;">
        <!-- Header -->
        <div class="mb-4 flex items-center justify-between">
            <div>
                <a href="{{ route('dashboard') }}" class="inline-flex items-center text-xs font-medium transition-colors group" style="color: #3B82F6;">
                    <svg class="w-3.5 h-3.5 mr-1 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Dashboard
                </a>
                <h1 class="text-lg font-bold mt-1" style="color: #0F172A;">AI Анализ кандидата</h1>
            </div>
        </div>

        <!-- Main Layout - Horizontal -->
        <div class="flex gap-4">
            <!-- LEFT: Report Area (MAIN - takes most width) -->
            <div class="flex-1 min-w-0">
                <div class="bg-white rounded-xl shadow-sm h-full" style="border: 1px solid #E5E7EB; min-height: calc(100vh - 120px);">
                    <!-- Report Header -->
                    <div class="px-5 py-3 flex items-center justify-between rounded-t-xl" style="border-bottom: 1px solid #E5E7EB; background: linear-gradient(135deg, #F8FAFF 0%, #EEF2FF 100%);">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%);">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <span class="ml-3 text-sm font-semibold" style="color: #0F172A;">Аналитический отчет</span>
                            @if($selectedCandidate)
                                <span class="ml-3 px-2 py-0.5 text-[10px] font-medium rounded-full" style="background-color: #DBEAFE; color: #1D4ED8;">
                                    {{ $selectedCandidate->full_name }}
                                </span>
                            @endif
                        </div>
                        @if($report)
                            <button wire:click="clearReport" class="text-xs font-medium flex items-center hover:opacity-70" style="color: #64748B;">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Новый анализ
                            </button>
                        @endif
                    </div>

                    <!-- Report Content -->
                    <div class="p-6 overflow-auto" style="max-height: calc(100vh - 180px);">
                        @if($isLoading)
                            <div class="flex flex-col items-center justify-center py-32">
                                <div class="relative">
                                    <div class="w-12 h-12 rounded-full" style="border: 3px solid #E5E7EB;"></div>
                                    <div class="w-12 h-12 rounded-full animate-spin absolute top-0 left-0" style="border: 3px solid #3B82F6; border-top-color: transparent;"></div>
                                </div>
                                <p class="mt-4 text-sm font-medium" style="color: #0F172A;">Формируем анализ...</p>
                                <p class="mt-1 text-xs" style="color: #64748B;">1-2 минуты</p>
                            </div>
                        @elseif($error)
                            <div class="rounded-lg p-4" style="background-color: #FEF2F2; border: 1px solid #FECACA;">
                                <div class="flex items-start">
                                    <svg class="w-5 h-5 flex-shrink-0" style="color: #DC2626;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="ml-2 text-sm" style="color: #DC2626;">{{ $error }}</p>
                                </div>
                            </div>
                        @elseif($report)
                            <article class="report-content">
                                {!! \Illuminate\Support\Str::markdown($report) !!}
                            </article>
                        @else
                            <div class="flex flex-col items-center justify-center py-24 text-center">
                                <div class="w-20 h-20 rounded-2xl flex items-center justify-center mb-6" style="background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%); box-shadow: 0 8px 24px rgba(59, 130, 246, 0.3);">
                                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 3.104v5.714a2.25 2.25 0 01-.659 1.591L5 14.5M9.75 3.104c-.251.023-.501.05-.75.082m.75-.082a24.301 24.301 0 014.5 0m0 0v5.714c0 .597.237 1.17.659 1.591L19.8 15.3M14.25 3.104c.251.023.501.05.75.082M19.8 15.3l-1.57.393A9.065 9.065 0 0112 15a9.065 9.065 0 00-6.23.693L5 14.5m14.8.8l1.402 1.402c1.232 1.232.65 3.318-1.067 3.611A48.309 48.309 0 0112 21c-2.773 0-5.491-.235-8.135-.687-1.718-.293-2.3-2.379-1.067-3.61L5 14.5"></path>
                                    </svg>
                                </div>
                                <h3 class="text-xl font-bold mb-3" style="color: #0F172A;">Готов к анализу</h3>
                                <p class="text-sm leading-relaxed max-w-sm mb-6" style="color: #64748B;">
                                    Выберите кандидата из базы и загрузите PDF отчет для глубинного анализа личности
                                </p>
                                <div class="flex items-center gap-6 text-xs" style="color: #94A3B8;">
                                    <div class="flex items-center">
                                        <div class="w-6 h-6 rounded-full flex items-center justify-center mr-2" style="background-color: #EEF2FF;">
                                            <span style="color: #3B82F6; font-weight: 600;">1</span>
                                        </div>
                                        Выберите кандидата
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-6 h-6 rounded-full flex items-center justify-center mr-2" style="background-color: #EEF2FF;">
                                            <span style="color: #3B82F6; font-weight: 600;">2</span>
                                        </div>
                                        Загрузите PDF
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-6 h-6 rounded-full flex items-center justify-center mr-2" style="background-color: #EEF2FF;">
                                            <span style="color: #3B82F6; font-weight: 600;">3</span>
                                        </div>
                                        Получите отчет
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- RIGHT: Controls Sidebar (NARROW - fixed width) -->
            <div class="w-72 flex-shrink-0 space-y-3">
                <!-- Candidate Search -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden" style="border: 1px solid #E5E7EB;">
                    <div class="px-4 py-2.5" style="background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%);">
                        <h3 class="text-xs font-semibold text-white flex items-center">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Кандидат
                        </h3>
                    </div>
                    <div class="p-3">
                        @if($selectedCandidate)
                            <div class="rounded-lg p-3" style="background-color: #F0FDF4; border: 1px solid #BBF7D0;">
                                <div class="flex items-start justify-between">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-xs font-semibold truncate" style="color: #166534;">{{ $selectedCandidate->full_name }}</p>
                                        <p class="text-[10px] mt-0.5 truncate" style="color: #16A34A;">{{ $selectedCandidate->email }}</p>
                                        @if($selectedCandidate->current_city)
                                            <p class="text-[10px] mt-0.5" style="color: #64748B;">{{ $selectedCandidate->current_city }}</p>
                                        @endif
                                    </div>
                                    <button wire:click="clearCandidate" class="p-1 rounded hover:bg-red-50" style="color: #64748B;">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="relative flex items-center">
                                <input
                                    type="text"
                                    wire:model.live.debounce.300ms="candidateSearch"
                                    placeholder="Поиск по имени..."
                                    class="w-full rounded-lg pl-3 pr-9 py-2 text-xs focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    style="border: 1px solid #E5E7EB; color: #334155;"
                                >
                                <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none" style="color: #94A3B8;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>

                                @if($showCandidateDropdown && count($candidateResults) > 0)
                                    <div class="absolute z-50 w-full top-full mt-1 bg-white rounded-lg shadow-lg max-h-48 overflow-y-auto" style="border: 1px solid #E5E7EB;">
                                        @foreach($candidateResults as $result)
                                            <button
                                                wire:click="selectCandidate({{ $result['id'] }})"
                                                class="w-full px-3 py-2 text-left hover:bg-blue-50 transition-colors border-b last:border-b-0"
                                                style="border-color: #F1F5F9;"
                                            >
                                                <p class="text-xs font-medium truncate" style="color: #0F172A;">{{ $result['full_name'] }}</p>
                                                <p class="text-[10px] truncate" style="color: #64748B;">{{ $result['email'] }}</p>
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- PDF Upload -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden" style="border: 1px solid #E5E7EB;">
                    <div class="px-4 py-2.5" style="border-bottom: 1px solid #E5E7EB; background: linear-gradient(135deg, #F8FAFF 0%, #EEF2FF 100%);">
                        <h3 class="text-xs font-semibold flex items-center" style="color: #0F172A;">
                            <svg class="w-3.5 h-3.5 mr-1.5" style="color: #3B82F6;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                            </svg>
                            PDF отчет
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
                                <input type="file" wire:model="pdfFile" accept=".pdf" class="hidden" x-ref="fileInput">
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
                            <div class="rounded-lg p-2.5" style="background-color: #F8FAFF; border: 1px solid #E5E7EB;">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center min-w-0 flex-1">
                                        <div class="w-8 h-8 rounded-md flex items-center justify-center flex-shrink-0" style="background-color: #FEE2E2;">
                                            <svg class="w-4 h-4" style="color: #DC2626;" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <p class="ml-2 text-xs font-medium truncate" style="color: #0F172A;">{{ $fileName }}</p>
                                    </div>
                                    <button wire:click="removePdf" class="p-1.5 rounded hover:bg-red-50" style="color: #64748B;">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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

                <!-- Context -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden" style="border: 1px solid #E5E7EB;">
                    <div class="px-4 py-2" style="border-bottom: 1px solid #E5E7EB; background: linear-gradient(135deg, #F8FAFF 0%, #EEF2FF 100%);">
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
                            class="w-full rounded-lg px-3 py-2 text-xs resize-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
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
                    class="w-full h-11 text-white text-sm font-semibold rounded-xl transition-all flex items-center justify-center disabled:opacity-50 disabled:cursor-not-allowed"
                    style="background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%); box-shadow: 0 4px 12px 0 rgba(59, 130, 246, 0.35);"
                >
                    <span wire:loading.remove wire:target="generateReport" class="flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Сформировать отчет
                    </span>
                    <span wire:loading wire:target="generateReport" class="flex items-center">
                        <svg class="animate-spin mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Анализ...
                    </span>
                </button>

                <!-- Debug -->
                @if($extractedText)
                    <div class="rounded-lg overflow-hidden" style="border: 1px solid #FDE68A;">
                        <button
                            wire:click="toggleExtractedText"
                            class="w-full px-3 py-2 text-left text-[10px] font-medium flex items-center justify-between"
                            style="background: #FEF3C7; color: #92400E;"
                        >
                            <span>Raw данные ({{ number_format(strlen($extractedText)) }} симв.)</span>
                            <svg class="w-3 h-3 transition-transform {{ $showExtractedText ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        @if($showExtractedText)
                            <div class="p-2 bg-white">
                                <pre class="text-[9px] p-2 rounded overflow-auto max-h-32 whitespace-pre-wrap break-words" style="background-color: #FFFBEB; color: #78350F; font-family: monospace;">{{ $extractedText }}</pre>
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
        }

        .report-content h3 {
            font-size: 14px;
            font-weight: 600;
            color: #1E293B;
            margin-top: 20px;
            margin-bottom: 10px;
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
        }

        .report-content li::marker {
            color: #3B82F6;
        }

        .report-content strong {
            font-weight: 600;
            color: #0F172A;
            background: linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 100%);
            padding: 1px 6px;
            border-radius: 4px;
        }

        .report-content blockquote {
            border-left: 4px solid #3B82F6;
            padding: 14px 18px;
            margin: 20px 0;
            background: #F8FAFF;
            border-radius: 0 8px 8px 0;
            font-style: italic;
            color: #475569;
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
            padding: 10px 14px;
            text-align: left;
            font-size: 12px;
        }

        .report-content td {
            padding: 10px 14px;
            border-bottom: 1px solid #E5E7EB;
            color: #334155;
        }

        .report-content tr:nth-child(even) {
            background: #F8FAFF;
        }

        .report-content h2:first-of-type {
            margin-top: 0;
        }
    </style>
</div>
