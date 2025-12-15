@if($currentStep === 2)
<div class="step">
    <h2 class="text-2xl font-bold mb-6">Дополнительная информация</h2>

    <div class="space-y-6">
        
        <!-- Первые три поля в одной строке -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <!-- Водительские права -->
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Водительские права <span class="text-red-500">*</span>
                </label>
                <select wire:model="has_driving_license" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Выберите ответ</option>
                    <option value="1">Есть</option>
                    <option value="0">Нет</option>
                </select>
                @error('has_driving_license') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
            
            <!-- Религия -->
            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Вероисповедание <span class="text-red-500">*</span>
                </label>
                <select wire:model="religion" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Выберите вероисповедание</option>
                    @foreach($religions as $key => $value)
                        <option value="{{ $value }}">{{ $value }}</option>
                    @endforeach
                </select>
                @error('religion') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Практикующий</label>
                <select wire:model="is_practicing" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="">Выберите ответ</option>
                    <option value="1">Да</option>
                    <option value="0">Нет</option>
                </select>
                @error('is_practicing') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Новая структура семьи -->
        <div class="space-y-6">
            <!-- Родители -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Родители</label>
                <div class="space-y-4">
                    @foreach($parents as $index => $parent)
                        <div wire:key="parent-{{ $index }}" class="p-4 bg-gray-50 rounded-lg">
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Родство <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model.live="parents.{{ $index }}.relation" 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Выберите</option>
                                        <option value="Отец">Отец</option>
                                        <option value="Мать">Мать</option>
                                    </select>
                                    @error("parents.{$index}.relation") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Год рождения <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model.live="parents.{{ $index }}.birth_year" 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Год рождения</option>
                                        @foreach($familyYears as $year)
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endforeach
                                    </select>
                                    @error("parents.{$index}.birth_year") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Профессия <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           wire:model.live.debounce.500ms="parents.{{ $index }}.profession" 
                                           placeholder="Профессия"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error("parents.{$index}.profession") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="mt-2">
                                <button type="button" onclick="@this.call('removeParent', {{ $index }})" class="text-red-600 hover:text-red-800">
                                    Удалить
                                </button>
                            </div>
                        </div>
                    @endforeach

                    @if(count($parents) < 2)
                        <!-- Основная кнопка с @this вызовом -->
                        <button type="button" 
                                onclick="@this.call('addParent')"
                                class="group mt-4 inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 ease-in-out focus:outline-none focus:ring-4 focus:ring-green-300 focus:ring-opacity-50">
                            <svg class="w-5 h-5 mr-2 group-hover:rotate-90 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Добавить родителя
                        </button>
                    @else
                        <div class="mt-4 text-sm text-gray-500">
                            Максимум 2 родителя (текущее количество: {{ count($parents) }})
                        </div>
                    @endif
                </div>
            </div>

            <!-- Братья и сестры -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Братья и сестры</label>
                <div class="space-y-4">
                    @foreach($siblings as $index => $sibling)
                        <div wire:key="sibling-{{ $index }}" class="p-4 bg-gray-50 rounded-lg">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Родство <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model.live="siblings.{{ $index }}.relation" 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Выберите</option>
                                        <option value="Брат">Брат</option>
                                        <option value="Сестра">Сестра</option>
                                    </select>
                                    @error("siblings.{$index}.relation") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Год рождения <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model.live="siblings.{{ $index }}.birth_year" 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Год рождения</option>
                                        @foreach($familyYears as $year)
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endforeach
                                    </select>
                                    @error("siblings.{$index}.birth_year") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="mt-2">
                                <button type="button" onclick="@this.call('removeSibling', {{ $index }})" class="text-red-600 hover:text-red-800">
                                    Удалить
                                </button>
                            </div>
                        </div>
                    @endforeach

                    <button type="button" 
                            onclick="@this.call('addSibling')"
                            class="group mt-4 inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 ease-in-out focus:outline-none focus:ring-4 focus:ring-green-300 focus:ring-opacity-50">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Добавить брата/сестру
                    </button>
                </div>
            </div>

            <!-- Дети -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Дети</label>
                <div class="space-y-4">
                    @foreach($children as $index => $child)
                        <div wire:key="child-{{ $index }}" class="p-4 bg-gray-50 rounded-lg">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Имя ребенка <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           wire:model.live.debounce.500ms="children.{{ $index }}.name" 
                                           placeholder="Имя ребенка"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error("children.{$index}.name") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Год рождения <span class="text-red-500">*</span>
                                    </label>
                                    <select wire:model.live="children.{{ $index }}.birth_year" 
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">Год рождения</option>
                                        @foreach($familyYears as $year)
                                            <option value="{{ $year }}">{{ $year }}</option>
                                        @endforeach
                                    </select>
                                    @error("children.{$index}.birth_year") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            <div class="mt-2">
                                <button type="button" onclick="@this.call('removeChild', {{ $index }})" class="text-red-600 hover:text-red-800">
                                    Удалить
                                </button>
                            </div>
                        </div>
                    @endforeach

                    <button type="button" 
                            onclick="@this.call('addChild')"
                            class="group mt-4 inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 ease-in-out focus:outline-none focus:ring-4 focus:ring-green-300 focus:ring-opacity-50">
                        <svg class="w-5 h-5 mr-2 group-hover:bounce transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        Добавить ребенка
                    </button>
                </div>
            </div>
        </div>

        <!-- Хобби и интересы -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-700">Хобби</label>
                <textarea wire:model="hobbies" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                @error('hobbies') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">Интересы</label>
                <textarea wire:model="interests" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                @error('interests') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Посещенные страны и Любимые виды спорта в одном ряду -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Посещенные страны -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Посещенные страны</label>
                
                <!-- Выбранные страны (badges) -->
                @if(count($visited_countries) > 0)
                    <div class="flex flex-wrap gap-2 mb-3" id="selected-countries-badges">
                        @foreach($visited_countries as $country)
                            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-sm hover:shadow-md transition-shadow">
                                @php
                                    $countryData = collect($countries)->firstWhere('name_ru', $country);
                                @endphp
                                @if($countryData && isset($countryData['flag_url']))
                                    <img src="{{ $countryData['flag_url'] }}" 
                                         alt="flag" 
                                         class="w-5 h-4 mr-2 rounded border border-white/30 object-cover">
                                @endif
                                {{ $country }}
                                <button type="button" 
                                        wire:click="removeCountry('{{ $country }}')"
                                        class="ml-2 text-white/80 hover:text-white focus:outline-none">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </span>
                        @endforeach
                    </div>
                @endif
                
                <!-- Select2 для выбора стран -->
                <div wire:ignore>
                    <select id="country-select-2" class="block w-full rounded-lg border-gray-300 shadow-sm">
                        <option value="">Выберите страну для добавления</option>
                        @foreach($countries as $country)
                            <option value="{{ $country['name_ru'] }}" 
                                    data-flag="{{ $country['flag_url'] ?? '' }}">
                                {{ $country['name_ru'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('visited_countries') <span class="text-red-500 text-sm mt-1 block">{{ $message }}</span> @enderror
            </div>

            <!-- Спорт -->
            <div>
                <label class="block text-sm font-medium text-gray-700">Любимые виды спорта</label>
                <textarea wire:model="favorite_sports" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                @error('favorite_sports') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Время и чтение -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Книг в год -->
            <div>
                <label class="block text-sm text-gray-700 mb-1.5">Количество книг в год</label>
                <div class="flex items-center gap-3">
                    <input type="number"
                           wire:model="books_per_year_min"
                           min="0"
                           max="100"
                           placeholder="от"
                           class="w-20 px-2 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:border-gray-500">
                    <span class="text-gray-400">—</span>
                    <input type="number"
                           wire:model="books_per_year_max"
                           min="0"
                           max="100"
                           placeholder="до"
                           class="w-20 px-2 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:border-gray-500">
                </div>
                @error('books_per_year_min') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                @error('books_per_year_max') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Развлекательные видео -->
            <div>
                <label class="block text-sm text-gray-700 mb-1.5">Развлекательные видео (часов в неделю)</label>
                <input type="number"
                       wire:model="entertainment_hours_weekly"
                       min="0"
                       max="168"
                       placeholder="0"
                       class="w-24 px-2 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:border-gray-500">
                @error('entertainment_hours_weekly') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Образовательные видео -->
            <div>
                <label class="block text-sm text-gray-700 mb-1.5">Образовательные видео (часов в неделю)</label>
                <input type="number"
                       wire:model="educational_hours_weekly"
                       min="0"
                       max="168"
                       placeholder="0"
                       class="w-24 px-2 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:border-gray-500">
                @error('educational_hours_weekly') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>

            <!-- Социальные сети -->
            <div>
                <label class="block text-sm text-gray-700 mb-1.5">Соц. сети (часов в неделю)</label>
                <input type="number"
                       wire:model="social_media_hours_weekly"
                       min="0"
                       max="168"
                       placeholder="0"
                       class="w-24 px-2 py-1.5 text-sm border border-gray-300 rounded focus:outline-none focus:border-gray-500">
                @error('social_media_hours_weekly') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>
</div>
@endif

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- Select2 Кастомные стили -->
<style>
    /* Select2 контейнер */
    .select2-container--default .select2-selection--single {
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        height: 42px;
        padding: 4px 8px;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 32px;
        padding-left: 8px;
        color: #374151;
    }
    
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
        right: 8px;
    }
    
    /* Dropdown */
    .select2-container--default .select2-results__option {
        padding: 10px 12px;
    }
    
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #eff6ff;
        color: #1e40af;
    }
    
    .select2-container--default .select2-results__option[aria-selected=true] {
        background-color: #dbeafe;
    }
    
    /* Search field */
    .select2-search--dropdown .select2-search__field {
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 8px 12px;
    }
    
    .select2-search--dropdown .select2-search__field:focus {
        border-color: #3b82f6;
        outline: none;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }
    
    /* Dropdown container */
    .select2-dropdown {
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    
    /* Флаги в опциях */
    .select2-results__option img {
        margin-right: 8px;
        vertical-align: middle;
    }
</style>


<script>
// Отладочный скрипт для проверки Livewire синхронизации
document.addEventListener('DOMContentLoaded', function() {
    console.log('🔍 Step2 Family Debug Script Loaded');
    
    // Отслеживаем все изменения в полях семьи
    document.addEventListener('input', function(e) {
        if (e.target && e.target.getAttribute && e.target.getAttribute('wire:model.blur')) {
            const wireModel = e.target.getAttribute('wire:model.blur');
            if (wireModel && (wireModel.includes('parents') || wireModel.includes('siblings') || wireModel.includes('children'))) {
                console.log('📝 Family field changed:', {
                    field: wireModel,
                    value: e.target.value,
                    type: e.target.tagName
                });
            }
        }
    }, true); // capture phase
    
    // Отслеживаем blur события
    document.addEventListener('blur', function(e) {
        if (e.target && e.target.getAttribute && e.target.getAttribute('wire:model.blur')) {
            const wireModel = e.target.getAttribute('wire:model.blur');
            if (wireModel && (wireModel.includes('parents') || wireModel.includes('siblings') || wireModel.includes('children'))) {
                console.log('💨 Family field blur (sync triggered):', {
                    field: wireModel,
                    value: e.target.value
                });
            }
        }
    }, true); // capture phase
    
    // Проверяем состояние Livewire компонента
    window.debugFamilyData = function() {
        console.log('🔍 Debugging Livewire Family Data...');
        const componentEl = document.querySelector('[wire\\:id]');
        if (componentEl) {
            const componentId = componentEl.getAttribute('wire:id');
            console.log('Component ID:', componentId);
            
            if (window.Livewire) {
                const component = window.Livewire.find(componentId);
                if (component) {
                    console.log('📊 Current family data in Livewire:', {
                        parents: component.get('parents'),
                        siblings: component.get('siblings'),
                        children: component.get('children')
                    });
                } else {
                    console.error('❌ Livewire component not found');
                }
            } else {
                console.error('❌ Livewire not available');
            }
        } else {
            console.error('❌ Component element not found');
        }
    };
    
    console.log('✅ Family debug script ready. Use window.debugFamilyData() to check state');
});
</script>

<!-- jQuery (требуется для Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('🌍 Select2 Country Selector initialization started');
    
    function initSelect2() {
        const selectElement = $('#country-select-2');
        
        if (!selectElement.length) {
            console.log('ℹ️ Country select element not found (probably not on step 2)');
            return false;
        }
        
        // Уничтожаем предыдущий экземпляр если есть
        if (selectElement.hasClass('select2-hidden-accessible')) {
            console.log('⚠️ Select2 already initialized, skipping');
            return true; // Уже инициализирован
        }
        
        try {
            console.log('✨ Initializing Select2');
            
            // Инициализируем Select2
            selectElement.select2({
                placeholder: 'Начните вводить название страны...',
                allowClear: true,
                width: '100%',
                templateResult: formatCountryOption,
                templateSelection: formatCountrySelection,
                language: {
                    noResults: function() {
                        return "Страна не найдена";
                    },
                    searching: function() {
                        return "Поиск...";
                    }
                }
            });
            
            // Убираем старые обработчики чтобы не было дублей
            selectElement.off('select2:select');
            
            // Обработчик выбора страны
            selectElement.on('select2:select', function(e) {
                const country = e.params.data.id;
                console.log('📍 Country selected:', country);
                
                if (country) {
                    // Вызываем Livewire метод
                    @this.call('addCountry', country).then(() => {
                        console.log('✅ Country added via Livewire');
                        // Сбрасываем Select2
                        selectElement.val(null).trigger('change');
                    }).catch((error) => {
                        console.error('❌ Error adding country:', error);
                    });
                }
            });
            
            console.log('✅ Select2 initialized successfully');
            return true;
        } catch (error) {
            console.error('❌ Error initializing Select2:', error);
            return false;
        }
    }
    
    // Форматирование опций с флагами
    function formatCountryOption(country) {
        if (!country.id) {
            return country.text;
        }
        
        const $country = $(
            '<span><img src="' + $(country.element).data('flag') + '" class="inline-block w-6 h-4 mr-2 rounded" onerror="this.style.display=\'none\'" /> ' + country.text + '</span>'
        );
        
        return $country;
    }
    
    // Форматирование выбранной опции
    function formatCountrySelection(country) {
        return country.text || 'Выберите страну...';
    }
    
    // Пытаемся инициализировать при загрузке
    initSelect2();
    
    // Слушаем Livewire событие смены шага - ГЛАВНЫЙ механизм
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('step-changed', (event) => {
            console.log('🔄 Step changed event received (Livewire):', event);
            
            const step = event.step || event[0]?.step || event[0];
            console.log('📍 Current step:', step);
            
            if (step === 2) {
                console.log('✅ Moved to step 2, will initialize Select2');
                
                // Пробуем несколько раз с увеличивающейся задержкой
                setTimeout(() => initSelect2(), 100);
                setTimeout(() => initSelect2(), 300);
                setTimeout(() => initSelect2(), 500);
            }
        });
        
        console.log('✅ Livewire event listener registered');
    });
    
    // Переинициализация при обновлении Livewire (ловит все изменения, включая клики на индикаторы)
    Livewire.hook('message.processed', (message, component) => {
        // Пробуем множество раз с разными задержками для надежности
        const delays = [50, 100, 200, 300, 500];
        
        delays.forEach(delay => {
            setTimeout(() => {
                const selectElement = $('#country-select-2');
                
                // Если элемент есть и не инициализирован - инициализируем
                if (selectElement.length && !selectElement.hasClass('select2-hidden-accessible')) {
                    console.log(`🔄 Livewire message.processed (delay ${delay}ms): Initializing Select2`);
                    initSelect2();
                }
            }, delay);
        });
    });
    
    // Дополнительный механизм: следим за изменениями DOM постоянно
    setInterval(() => {
        const selectElement = $('#country-select-2');
        if (selectElement.length && !selectElement.hasClass('select2-hidden-accessible')) {
            console.log('⏰ Interval check: Found uninitialized Select2, initializing...');
            initSelect2();
        }
    }, 1000); // Проверяем каждую секунду
    
    console.log('✅ Select2 script loaded and ready');
});
</script>
 