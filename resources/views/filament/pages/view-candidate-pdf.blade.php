<x-filament::page>
    {{-- Панель перевода для анкеты --}}
    @if(str_contains(strtolower($this->type), 'anketa'))
    <div class="mb-4 p-4 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
        <div class="flex flex-wrap items-center gap-4">
            {{-- Информация об исходном языке --}}
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500 dark:text-gray-400">Исходный язык:</span>
                <span class="px-2 py-1 text-sm font-medium bg-gray-100 dark:bg-gray-700 rounded">
                    @switch($this->sourceLanguage)
                        @case('ru')
                            🇷🇺 Русский
                            @break
                        @case('en')
                            🇬🇧 English
                            @break
                        @case('ar')
                            🇸🇦 العربية
                            @break
                    @endswitch
                </span>
            </div>

            {{-- Разделитель --}}
            <div class="h-6 w-px bg-gray-300 dark:bg-gray-600"></div>

            {{-- Выбор языка перевода --}}
            <div class="flex items-center gap-2">
                <label for="language-select" class="text-sm text-gray-500 dark:text-gray-400">Перевести на:</label>
                <select
                    id="language-select"
                    wire:model="selectedLanguage"
                    class="rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500"
                >
                    <option value="">Выберите язык</option>
                    @foreach($this->availableLanguages as $code => $name)
                        <option value="{{ $code }}">
                            @switch($code)
                                @case('ru')
                                    🇷🇺 {{ $name }}
                                    @break
                                @case('en')
                                    🇬🇧 {{ $name }}
                                    @break
                                @case('ar')
                                    🇸🇦 {{ $name }}
                                    @break
                                @default
                                    {{ $name }}
                            @endswitch
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Кнопка перевода --}}
            <button
                wire:click="translateAndDownload"
                wire:loading.attr="disabled"
                wire:loading.class="opacity-50 cursor-wait"
                wire:target="translateAndDownload"
                @if(!$this->selectedLanguage) disabled @endif
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-primary-600 rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors"
            >
                <span wire:loading.remove wire:target="translateAndDownload">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129"/>
                    </svg>
                </span>
                <span wire:loading wire:target="translateAndDownload">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </span>
                <span wire:loading.remove wire:target="translateAndDownload">Перевести</span>
                <span wire:loading wire:target="translateAndDownload">Переводим...</span>
            </button>

            {{-- Кнопка скачивания перевода (если готов) --}}
            @if($this->translatedUrl)
            <a
                href="{{ $this->translatedUrl }}"
                target="_blank"
                download
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-green-600 rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Скачать перевод
            </a>
            @endif

            {{-- Кнопка скачивания оригинала --}}
            <a
                href="{{ $this->url }}"
                target="_blank"
                download
                class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition-colors"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Скачать оригинал
            </a>
        </div>

        {{-- Подсказка --}}
        <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">
            💡 Перевод выполняется с помощью AI (GPT-4o). Время генерации ~30-60 секунд.
        </p>
    </div>
    @endif

    {{-- PDF viewer --}}
    <iframe
        src="{{ $this->translatedUrl ?? $this->url }}"
        style="width: 100%; height: calc(100vh - 200px); border: none; border-radius: 8px;"
    ></iframe>

    {{-- JavaScript для скачивания --}}
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('download-file', (event) => {
                const link = document.createElement('a');
                link.href = event.url;
                link.download = '';
                link.target = '_blank';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            });
        });
    </script>
</x-filament::page>
