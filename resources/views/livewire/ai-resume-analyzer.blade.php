<div
    class="min-h-screen"
    style="background: linear-gradient(180deg, #0F172A 0%, #1E293B 100%);"
    x-data="{ ready: false }"
    x-init="setTimeout(() => ready = true, 100)"
>
    <!-- Subtle grid pattern overlay -->
    <div class="absolute inset-0 opacity-[0.02]" style="background-image: url('data:image/svg+xml,%3Csvg width=&quot;60&quot; height=&quot;60&quot; viewBox=&quot;0 0 60 60&quot; xmlns=&quot;http://www.w3.org/2000/svg&quot;%3E%3Cg fill=&quot;none&quot; fill-rule=&quot;evenodd&quot;%3E%3Cg fill=&quot;%23ffffff&quot; fill-opacity=&quot;1&quot;%3E%3Cpath d=&quot;M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z&quot;/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>

    <div class="relative mx-auto px-6 lg:px-8 py-6" style="max-width: 1600px;">
        <!-- Header -->
        <div
            class="mb-6 flex items-center justify-between transition-all duration-500"
            :class="ready ? 'opacity-100 translate-y-0' : 'opacity-0 -translate-y-2'"
        >
            <div class="flex items-center gap-6">
                <a href="{{ route('dashboard') }}" class="group flex items-center gap-2 px-3 py-1.5 rounded-lg transition-all duration-200 hover:bg-white/5">
                    <svg class="w-4 h-4 text-slate-400 group-hover:text-white group-hover:-translate-x-0.5 transition-all" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    <span class="text-sm text-slate-400 group-hover:text-white transition-colors">Dashboard</span>
                </a>
                <div class="h-6 w-px bg-slate-700"></div>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%); box-shadow: 0 0 20px rgba(99, 102, 241, 0.4);">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456zM16.894 20.567L16.5 21.75l-.394-1.183a2.25 2.25 0 00-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 001.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 001.423 1.423l1.183.394-1.183.394a2.25 2.25 0 00-1.423 1.423z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-semibold text-white tracking-tight">AI Анализ</h1>
                        <p class="text-xs text-slate-500">Глубинная аналитика кандидатов</p>
                    </div>
                </div>
            </div>

            <!-- Status indicator -->
            <div class="flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-800/50 border border-slate-700/50">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span class="text-xs text-slate-400">GPT-4o Ready</span>
            </div>
        </div>

        <!-- Main Layout -->
        <div
            class="flex gap-6 transition-all duration-700 delay-100"
            :class="ready ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-4'"
        >
            <!-- LEFT: Report Area -->
            <div class="flex-1 min-w-0">
                <div
                    class="rounded-2xl overflow-hidden h-full backdrop-blur-sm"
                    style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06); min-height: calc(100vh - 160px);"
                >
                    <!-- Report Header -->
                    <div class="px-6 py-4 flex items-center justify-between" style="border-bottom: 1px solid rgba(255,255,255,0.06); background: rgba(255,255,255,0.02);">
                        <div class="flex items-center gap-4">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-indigo-500"></div>
                                <span class="text-sm font-medium text-white">Аналитический отчет</span>
                            </div>
                            @if($selectedCandidate)
                                <div class="flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-500/10 border border-indigo-500/20">
                                    <svg class="w-3.5 h-3.5 text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    <span class="text-xs font-medium text-indigo-300">{{ $selectedCandidate->full_name }}</span>
                                </div>
                            @endif
                        </div>
                        @if($report)
                            <button
                                wire:click="clearReport"
                                class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-white/5 transition-all duration-200"
                            >
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
                            <!-- Premium Loading State -->
                            <div class="flex flex-col items-center justify-center py-24">
                                <div class="relative mb-8">
                                    <!-- Outer ring -->
                                    <div class="w-20 h-20 rounded-full" style="border: 2px solid rgba(99, 102, 241, 0.1);"></div>
                                    <!-- Spinning ring -->
                                    <div class="w-20 h-20 rounded-full animate-spin absolute top-0 left-0" style="border: 2px solid transparent; border-top-color: #6366F1;"></div>
                                    <!-- Inner glow -->
                                    <div class="absolute inset-3 rounded-full flex items-center justify-center" style="background: linear-gradient(135deg, rgba(99, 102, 241, 0.2) 0%, rgba(79, 70, 229, 0.1) 100%);">
                                        <svg class="w-6 h-6 text-indigo-400 animate-pulse" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"></path>
                                        </svg>
                                    </div>
                                </div>
                                <h3 class="text-lg font-semibold text-white mb-2">Анализируем данные</h3>
                                <p class="text-sm text-slate-500 mb-6">AI формирует глубинный психологический профиль</p>
                                <div class="flex items-center gap-4 text-xs text-slate-600">
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-1.5 h-1.5 rounded-full bg-indigo-500 animate-pulse"></div>
                                        <span>Обработка PDF</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-1.5 h-1.5 rounded-full bg-slate-600"></div>
                                        <span>Синтез данных</span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <div class="w-1.5 h-1.5 rounded-full bg-slate-600"></div>
                                        <span>Генерация отчета</span>
                                    </div>
                                </div>
                            </div>
                        @elseif($error)
                            <!-- Error State -->
                            <div class="flex flex-col items-center justify-center py-16">
                                <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-6" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2);">
                                    <svg class="w-8 h-8 text-red-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"></path>
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-white mb-2">Произошла ошибка</h3>
                                <p class="text-sm text-red-400/80 text-center max-w-md">{{ $error }}</p>
                            </div>
                        @elseif($report)
                            <!-- Report Content -->
                            <article class="report-content">
                                {!! \Illuminate\Support\Str::markdown($report) !!}
                            </article>
                        @else
                            <!-- Empty State - Premium -->
                            <div class="flex flex-col items-center justify-center py-16 text-center">
                                <!-- Animated icon -->
                                <div class="relative mb-8">
                                    <div class="absolute inset-0 rounded-3xl blur-2xl opacity-30" style="background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%);"></div>
                                    <div class="relative w-24 h-24 rounded-3xl flex items-center justify-center" style="background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%); box-shadow: 0 20px 40px rgba(99, 102, 241, 0.3);">
                                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z"></path>
                                        </svg>
                                    </div>
                                </div>

                                <h2 class="text-2xl font-semibold text-white mb-3 tracking-tight">Готов к анализу</h2>
                                <p class="text-slate-400 max-w-md mb-10 leading-relaxed">
                                    Загрузите PDF-отчет кандидата для получения глубинного психологического анализа на основе AI
                                </p>

                                <!-- Steps -->
                                <div class="flex items-center gap-3">
                                    <div class="group flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-300 cursor-default" style="background: rgba(99, 102, 241, 0.1); border: 1px solid rgba(99, 102, 241, 0.2);">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-sm font-semibold text-indigo-400" style="background: rgba(99, 102, 241, 0.2);">1</div>
                                        <span class="text-sm text-slate-300">Выберите кандидата</span>
                                    </div>
                                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                    <div class="flex items-center gap-3 px-4 py-3 rounded-xl" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06);">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-sm font-semibold text-slate-500" style="background: rgba(255,255,255,0.05);">2</div>
                                        <span class="text-sm text-slate-500">Загрузите PDF</span>
                                    </div>
                                    <svg class="w-4 h-4 text-slate-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                    <div class="flex items-center gap-3 px-4 py-3 rounded-xl" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06);">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center text-sm font-semibold text-slate-500" style="background: rgba(255,255,255,0.05);">3</div>
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
                <!-- Candidate Search Card -->
                <div
                    class="rounded-2xl overflow-hidden backdrop-blur-sm transition-all duration-300 hover:border-indigo-500/30"
                    style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06);"
                >
                    <div class="px-5 py-4 flex items-center justify-between" style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%);">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                            </div>
                            <span class="text-sm font-medium text-white">Кандидат</span>
                        </div>
                        @if($selectedCandidate)
                            <span class="flex items-center gap-1.5 text-xs text-emerald-400">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                Выбран
                            </span>
                        @endif
                    </div>
                    <div class="p-4">
                        @if($selectedCandidate)
                            <div class="rounded-xl p-4 transition-all" style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2);">
                                <div class="flex items-start justify-between">
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-medium text-emerald-300 truncate">{{ $selectedCandidate->full_name }}</p>
                                        <p class="text-xs text-emerald-400/60 mt-1 truncate">{{ $selectedCandidate->email }}</p>
                                        @if($selectedCandidate->current_city)
                                            <p class="text-xs text-slate-500 mt-1">{{ $selectedCandidate->current_city }}</p>
                                        @endif
                                    </div>
                                    <button
                                        wire:click="clearCandidate"
                                        class="p-1.5 rounded-lg text-slate-500 hover:text-red-400 hover:bg-red-400/10 transition-all"
                                    >
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
                                    placeholder="Поиск по имени или email..."
                                    class="w-full rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                                    style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);"
                                >

                                @if($showCandidateDropdown && count($candidateResults) > 0)
                                    <div
                                        class="absolute z-50 w-full top-full mt-2 rounded-xl overflow-hidden shadow-2xl"
                                        style="background: #1E293B; border: 1px solid rgba(255,255,255,0.1);"
                                    >
                                        @foreach($candidateResults as $result)
                                            <button
                                                wire:click="selectCandidate({{ $result['id'] }})"
                                                class="w-full px-4 py-3 text-left transition-all duration-150 hover:bg-indigo-500/10 border-b border-white/5 last:border-b-0"
                                            >
                                                <p class="text-sm font-medium text-white truncate">{{ $result['full_name'] }}</p>
                                                <p class="text-xs text-slate-500 truncate mt-0.5">{{ $result['email'] }}</p>
                                            </button>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- PDF Upload Card -->
                <div
                    class="rounded-2xl overflow-hidden backdrop-blur-sm transition-all duration-300"
                    style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06);"
                >
                    <div class="px-5 py-4 flex items-center gap-3" style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: rgba(239, 68, 68, 0.15);">
                            <svg class="w-4 h-4 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-white">PDF Отчет</span>
                    </div>
                    <div class="p-4">
                        @if(!$fileName)
                            <div
                                x-data="{ isDragging: false }"
                                x-on:dragover.prevent="isDragging = true"
                                x-on:dragleave.prevent="isDragging = false"
                                x-on:drop.prevent="isDragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'));"
                                :class="{ 'border-indigo-500 bg-indigo-500/10': isDragging }"
                                class="border-2 border-dashed rounded-xl p-6 text-center cursor-pointer transition-all duration-300 hover:border-indigo-500/50 hover:bg-indigo-500/5 group"
                                style="border-color: rgba(255,255,255,0.1);"
                                x-on:click="$refs.fileInput.click()"
                            >
                                <input type="file" wire:model="pdfFile" accept=".pdf" class="hidden" x-ref="fileInput">
                                <div class="flex flex-col items-center">
                                    <div class="w-12 h-12 rounded-xl flex items-center justify-center mb-3 transition-transform duration-300 group-hover:scale-110" style="background: rgba(99, 102, 241, 0.1);">
                                        <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5"></path>
                                        </svg>
                                    </div>
                                    <p class="text-sm text-slate-300 mb-1">
                                        <span class="font-medium text-indigo-400">Нажмите</span> или перетащите
                                    </p>
                                    <p class="text-xs text-slate-600">PDF до 20 МБ</p>
                                </div>
                            </div>
                        @else
                            <div class="rounded-xl p-3 transition-all" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center min-w-0 flex-1 gap-3">
                                        <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background: rgba(239, 68, 68, 0.15);">
                                            <svg class="w-5 h-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                            </svg>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-medium text-white truncate">{{ $fileName }}</p>
                                            <p class="text-xs text-slate-500">Готов к анализу</p>
                                        </div>
                                    </div>
                                    <button wire:click="removePdf" class="p-2 rounded-lg text-slate-500 hover:text-red-400 hover:bg-red-400/10 transition-all">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @endif
                        @error('pdfFile')
                            <p class="mt-3 text-xs text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Context Card -->
                <div
                    class="rounded-2xl overflow-hidden backdrop-blur-sm"
                    style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.06);"
                >
                    <div class="px-5 py-4 flex items-center gap-3" style="border-bottom: 1px solid rgba(255,255,255,0.06);">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background: rgba(99, 102, 241, 0.15);">
                            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                            </svg>
                        </div>
                        <span class="text-sm font-medium text-white">Контекст</span>
                        <span class="text-xs text-slate-600">(опционально)</span>
                    </div>
                    <div class="p-4">
                        <textarea
                            wire:model="comment"
                            rows="2"
                            class="w-full rounded-xl px-4 py-3 text-sm text-white placeholder-slate-500 resize-none transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/50"
                            style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);"
                            placeholder="Вакансия, позиция или фокус анализа..."
                        ></textarea>
                    </div>
                </div>

                <!-- Generate Button -->
                <button
                    wire:click="generateReport"
                    wire:loading.attr="disabled"
                    wire:target="generateReport"
                    :disabled="$wire.isLoading || !$wire.fileName"
                    class="group relative w-full h-14 text-white text-sm font-semibold rounded-2xl transition-all duration-300 flex items-center justify-center overflow-hidden disabled:opacity-40 disabled:cursor-not-allowed disabled:hover:scale-100 hover:scale-[1.02] active:scale-[0.98]"
                    style="background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%); box-shadow: 0 8px 32px rgba(99, 102, 241, 0.4);"
                >
                    <!-- Shimmer effect -->
                    <div class="absolute inset-0 -translate-x-full group-hover:translate-x-full transition-transform duration-1000 bg-gradient-to-r from-transparent via-white/10 to-transparent"></div>

                    <span wire:loading.remove wire:target="generateReport" class="relative flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z"></path>
                        </svg>
                        Сформировать отчет
                    </span>
                    <span wire:loading wire:target="generateReport" class="relative flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Анализируем...
                    </span>
                </button>

                <!-- Debug Panel -->
                @if($extractedText)
                    <div class="rounded-xl overflow-hidden" style="background: rgba(251, 191, 36, 0.1); border: 1px solid rgba(251, 191, 36, 0.2);">
                        <button
                            wire:click="toggleExtractedText"
                            class="w-full px-4 py-3 text-left text-xs font-medium flex items-center justify-between text-amber-400/80 hover:text-amber-300 transition-colors"
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
                                <pre class="text-[10px] p-3 rounded-lg overflow-auto max-h-40 whitespace-pre-wrap break-words text-amber-200/70" style="background: rgba(0,0,0,0.3); font-family: 'JetBrains Mono', monospace;">{{ $extractedText }}</pre>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>
    </div>

    <style>
        /* Premium Report Styles - Dark Theme */
        .report-content {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: #CBD5E1;
            font-size: 15px;
            line-height: 1.8;
        }

        .report-content h1 {
            font-size: 28px;
            font-weight: 700;
            color: #F8FAFC;
            margin-top: 0;
            margin-bottom: 24px;
            padding-bottom: 16px;
            border-bottom: 2px solid #6366F1;
            letter-spacing: -0.025em;
        }

        .report-content h2 {
            font-size: 18px;
            font-weight: 600;
            color: #F1F5F9;
            margin-top: 40px;
            margin-bottom: 16px;
            padding: 14px 18px;
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.15) 0%, rgba(79, 70, 229, 0.1) 100%);
            border-left: 4px solid #6366F1;
            border-radius: 0 12px 12px 0;
        }

        .report-content h3 {
            font-size: 16px;
            font-weight: 600;
            color: #E2E8F0;
            margin-top: 24px;
            margin-bottom: 12px;
        }

        .report-content p {
            margin-bottom: 16px;
            color: #94A3B8;
        }

        .report-content ul, .report-content ol {
            margin-bottom: 20px;
            padding-left: 24px;
        }

        .report-content li {
            margin-bottom: 10px;
            color: #94A3B8;
        }

        .report-content li::marker {
            color: #6366F1;
        }

        .report-content strong {
            font-weight: 600;
            color: #E2E8F0;
            background: rgba(99, 102, 241, 0.15);
            padding: 2px 8px;
            border-radius: 6px;
        }

        .report-content blockquote {
            border-left: 4px solid #6366F1;
            padding: 16px 20px;
            margin: 24px 0;
            background: rgba(99, 102, 241, 0.08);
            border-radius: 0 12px 12px 0;
            font-style: italic;
            color: #A5B4FC;
        }

        .report-content table {
            width: 100%;
            border-collapse: collapse;
            margin: 24px 0;
            font-size: 14px;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .report-content th {
            background: linear-gradient(135deg, #6366F1 0%, #4F46E5 100%);
            font-weight: 600;
            color: white;
            padding: 14px 18px;
            text-align: left;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .report-content td {
            padding: 14px 18px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            color: #94A3B8;
        }

        .report-content tr:nth-child(even) {
            background: rgba(255,255,255,0.02);
        }

        .report-content tr:hover {
            background: rgba(99, 102, 241, 0.05);
        }

        .report-content h2:first-of-type {
            margin-top: 0;
        }

        /* Smooth scrollbar */
        .report-content::-webkit-scrollbar {
            width: 6px;
        }

        .report-content::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.02);
            border-radius: 3px;
        }

        .report-content::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.1);
            border-radius: 3px;
        }

        .report-content::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.15);
        }
    </style>
</div>
