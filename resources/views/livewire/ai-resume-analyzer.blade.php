<div class="min-h-screen" style="background-color: #F8FAFF;">
    <div class="max-w-[1800px] mx-auto px-4 lg:px-8 py-6">
        <!-- Header -->
        <div class="mb-5">
            <a href="{{ route('dashboard') }}" class="inline-flex items-center text-sm font-medium transition-colors group" style="color: #3B82F6;">
                <svg class="w-4 h-4 mr-1.5 group-hover:-translate-x-0.5 transition-transform" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Dashboard
            </a>
            <h1 class="mt-2 text-xl font-bold" style="color: #0F172A;">AI Анализ кандидата</h1>
        </div>

        <!-- Main Grid - Fixed proportions -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-5">

            <!-- Left Column - AI Report (9 columns = 75%) -->
            <div class="lg:col-span-9 order-2 lg:order-1">
                <div class="bg-white rounded-xl shadow-sm" style="border: 1px solid #E5E7EB; min-height: 700px;">
                    <!-- Report Header -->
                    <div class="px-5 py-3.5 flex items-center justify-between rounded-t-xl" style="border-bottom: 1px solid #E5E7EB; background: linear-gradient(135deg, #F8FAFF 0%, #EEF2FF 100%);">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%);">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <span class="ml-3 text-sm font-semibold" style="color: #0F172A;">Аналитический отчет</span>
                        </div>
                        @if($report)
                            <button wire:click="clearReport" class="text-xs font-medium flex items-center hover:opacity-70" style="color: #64748B;">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                </svg>
                                Новый
                            </button>
                        @endif
                    </div>

                    <!-- Report Content -->
                    <div class="p-5 lg:p-6">
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
                            <div class="flex flex-col items-center justify-center py-32 text-center">
                                <div class="w-14 h-14 rounded-xl flex items-center justify-center mb-4" style="background: linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 100%);">
                                    <svg class="w-7 h-7" style="color: #3B82F6;" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-base font-semibold mb-1.5" style="color: #0F172A;">Готов к анализу</h3>
                                <p class="text-xs leading-relaxed max-w-xs" style="color: #64748B;">
                                    Выберите кандидата и загрузите PDF отчет для глубинного анализа
                                </p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Column - Controls (3 columns = 25%) -->
            <div class="lg:col-span-3 order-1 lg:order-2 space-y-3">

                <!-- Candidate Search Card -->
                <div class="bg-white rounded-xl shadow-sm overflow-hidden" style="border: 1px solid #E5E7EB;">
                    <div class="px-4 py-2.5" style="border-bottom: 1px solid #E5E7EB; background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%);">
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
                                    <button wire:click="clearCandidate" class="p-1 rounded hover:bg-red-50 transition-colors" style="color: #64748B;">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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
                                    placeholder="Поиск по имени..."
                                    class="w-full rounded-lg px-3 py-2 text-xs transition-all focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    style="border: 1px solid #E5E7EB; color: #334155;"
                                >
                                <svg class="w-4 h-4 absolute right-3 top-2" style="color: #94A3B8;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>

                                @if($showCandidateDropdown && count($candidateResults) > 0)
                                    <div class="absolute z-50 w-full mt-1 bg-white rounded-lg shadow-lg max-h-48 overflow-y-auto" style="border: 1px solid #E5E7EB;">
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

                <!-- Upload Card -->
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
                                    <div class="w-9 h-9 rounded-lg flex items-center justify-center mb-2" style="background: linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 100%);">
                                        <svg class="w-4 h-4" style="color: #3B82F6;" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
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
                                        <div class="w-7 h-7 rounded-md flex items-center justify-center flex-shrink-0" style="background-color: #FEE2E2;">
                                            <svg class="w-3.5 h-3.5" style="color: #DC2626;" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <p class="ml-2 text-xs font-medium truncate" style="color: #0F172A;">{{ $fileName }}</p>
                                    </div>
                                    <button wire:click="removePdf" class="p-1 rounded hover:bg-red-50" style="color: #64748B;">
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

                <!-- Context Card -->
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
                            placeholder="Вакансия или фокус анализа..."
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
                    <span wire:loading.remove wire:target="generateReport" class="flex items-center">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        Сформировать отчет
                    </span>
                    <span wire:loading wire:target="generateReport" class="flex items-center">
                        <svg class="animate-spin mr-1.5 h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Анализ...
                    </span>
                </button>

                <!-- Debug: Extracted Text -->
                @if($extractedText)
                    <div class="rounded-xl overflow-hidden" style="border: 1px solid #E5E7EB;">
                        <button
                            wire:click="toggleExtractedText"
                            class="w-full px-3 py-2 text-left text-[10px] font-medium flex items-center justify-between"
                            style="background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%); color: #92400E;"
                        >
                            <span class="flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path>
                                </svg>
                                Raw данные ({{ strlen($extractedText) }} симв.)
                            </span>
                            <svg class="w-3 h-3 transition-transform {{ $showExtractedText ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        @if($showExtractedText)
                            <div class="p-2 bg-white">
                                <pre class="text-[9px] p-2 rounded overflow-auto max-h-40 whitespace-pre-wrap break-words" style="background-color: #F8FAFF; border: 1px solid #E5E7EB; color: #334155; font-family: monospace;">{{ $extractedText }}</pre>
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
            line-height: 1.7;
        }

        .report-content h1 {
            font-size: 20px;
            font-weight: 700;
            color: #0F172A;
            margin-top: 0;
            margin-bottom: 16px;
            padding-bottom: 10px;
            border-bottom: 2px solid #3B82F6;
        }

        .report-content h2 {
            font-size: 15px;
            font-weight: 700;
            color: #0F172A;
            margin-top: 28px;
            margin-bottom: 12px;
            padding: 8px 12px;
            background: linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 100%);
            border-left: 3px solid #3B82F6;
            border-radius: 0 6px 6px 0;
        }

        .report-content h3 {
            font-size: 14px;
            font-weight: 600;
            color: #1E293B;
            margin-top: 18px;
            margin-bottom: 8px;
        }

        .report-content p {
            margin-bottom: 12px;
            color: #334155;
        }

        .report-content ul, .report-content ol {
            margin-bottom: 14px;
            padding-left: 18px;
        }

        .report-content li {
            margin-bottom: 6px;
            color: #334155;
        }

        .report-content li::marker {
            color: #3B82F6;
        }

        .report-content strong {
            font-weight: 600;
            color: #0F172A;
            background: linear-gradient(135deg, #EEF2FF 0%, #E0E7FF 100%);
            padding: 1px 5px;
            border-radius: 3px;
        }

        .report-content blockquote {
            border-left: 3px solid #3B82F6;
            padding: 12px 16px;
            margin: 16px 0;
            background: #F8FAFF;
            border-radius: 0 6px 6px 0;
            font-style: italic;
            color: #475569;
        }

        .report-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 16px 0;
            font-size: 13px;
            border-radius: 6px;
            overflow: hidden;
            border: 1px solid #E5E7EB;
        }

        .report-content th {
            background: linear-gradient(135deg, #3B82F6 0%, #1D4ED8 100%);
            font-weight: 600;
            color: white;
            padding: 8px 12px;
            text-align: left;
            font-size: 11px;
            text-transform: uppercase;
        }

        .report-content td {
            padding: 8px 12px;
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
