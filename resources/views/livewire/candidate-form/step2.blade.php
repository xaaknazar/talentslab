@if($currentStep === 2)
<div class="step">
    <h2 class="text-2xl font-bold mb-6">{{ __('Additional Information') }}</h2>

    <div class="grid grid-cols-1 gap-6">
        <!-- Основная информация -->
        <div>
            <label class="block text-base font-medium text-gray-700 mb-3">{{ __('Basic Information') }}</label>
            <div class="p-4 bg-gray-50 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Водительские права -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            {{ __('Driving License') }} <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="has_driving_license" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">{{ __('Select answer') }}</option>
                            <option value="1">{{ __('Available') }}</option>
                            <option value="0">{{ __('Not available') }}</option>
                        </select>
                        @error('has_driving_license') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Религия -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            {{ __('Religion') }} <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="religion" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">{{ __('Select religion') }}</option>
                            @foreach($religions as $key => $value)
                                <option value="{{ $value }}">{{ __($value) }}</option>
                            @endforeach
                        </select>
                        @error('religion') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Практикующий -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Practicing') }}</label>
                        <select wire:model="is_practicing" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="">{{ __('Select answer') }}</option>
                            <option value="1">{{ __('Yes') }}</option>
                            <option value="0">{{ __('No') }}</option>
                        </select>
                        @error('is_practicing') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Родители -->
        <div>
            <label class="block text-base font-medium text-gray-700 mb-3">{{ __('Parents') }}</label>
            <div class="space-y-4">
                @foreach($parents as $index => $parent)
                    <div wire:key="parent-{{ $index }}" class="p-4 bg-gray-50 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ __('Relationship') }} <span class="text-red-500">*</span>
                                </label>
                                <select wire:model.live="parents.{{ $index }}.relation"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">{{ __('Select') }}</option>
                                    <option value="Отец">{{ __('Father') }}</option>
                                    <option value="Мать">{{ __('Mother') }}</option>
                                </select>
                                @error("parents.{$index}.relation") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ __('Year of Birth') }} <span class="text-red-500">*</span>
                                </label>
                                <select wire:model.live="parents.{{ $index }}.birth_year"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">{{ __('Year of Birth') }}</option>
                                    @foreach($familyYears as $year)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </select>
                                @error("parents.{$index}.birth_year") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ __("Parent's Profession") }} <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       wire:model.live.debounce.500ms="parents.{{ $index }}.profession"
                                       placeholder="{{ __('Profession') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error("parents.{$index}.profession") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="button" onclick="@this.call('removeParent', {{ $index }})" class="text-red-600 hover:text-red-800 text-sm">
                                {{ __('Delete') }}
                            </button>
                        </div>
                    </div>
                @endforeach

                @if(count($parents) < 2)
                    <button type="button"
                            onclick="@this.call('addParent')"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        {{ __('Add') }}
                    </button>
                @endif
            </div>
        </div>

        <!-- Братья и сестры -->
        <div>
            <label class="block text-base font-medium text-gray-700 mb-3">{{ __('Siblings') }}</label>
            <div class="space-y-4">
                @foreach($siblings as $index => $sibling)
                    <div wire:key="sibling-{{ $index }}" class="p-4 bg-gray-50 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ __('Relationship') }} <span class="text-red-500">*</span>
                                </label>
                                <select wire:model.live="siblings.{{ $index }}.relation"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">{{ __('Select') }}</option>
                                    <option value="Брат">{{ __('Brother') }}</option>
                                    <option value="Сестра">{{ __('Sister') }}</option>
                                </select>
                                @error("siblings.{$index}.relation") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ __('Year of Birth') }} <span class="text-red-500">*</span>
                                </label>
                                <select wire:model.live="siblings.{{ $index }}.birth_year"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">{{ __('Year of Birth') }}</option>
                                    @foreach($familyYears as $year)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </select>
                                @error("siblings.{$index}.birth_year") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="button" onclick="@this.call('removeSibling', {{ $index }})" class="text-red-600 hover:text-red-800 text-sm">
                                {{ __('Delete') }}
                            </button>
                        </div>
                    </div>
                @endforeach

                <button type="button"
                        onclick="@this.call('addSibling')"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('Add') }}
                </button>
            </div>
        </div>

        <!-- Дети -->
        <div>
            <label class="block text-base font-medium text-gray-700 mb-3">{{ __('Children') }}</label>
            <div class="space-y-4">
                @foreach($children as $index => $child)
                    <div wire:key="child-{{ $index }}" class="p-4 bg-gray-50 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ __('Gender') }} <span class="text-red-500">*</span>
                                </label>
                                <select wire:model.live="children.{{ $index }}.gender"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">{{ __('Select') }}</option>
                                    <option value="М">{{ __('Son') }}</option>
                                    <option value="Ж">{{ __('Daughter') }}</option>
                                </select>
                                @error("children.{$index}.gender") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ __('Year of Birth') }} <span class="text-red-500">*</span>
                                </label>
                                <select wire:model.live="children.{{ $index }}.birth_year"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">{{ __('Year of Birth') }}</option>
                                    @foreach($familyYears as $year)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </select>
                                @error("children.{$index}.birth_year") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="mt-3">
                            <button type="button" onclick="@this.call('removeChild', {{ $index }})" class="text-red-600 hover:text-red-800 text-sm">
                                {{ __('Delete') }}
                            </button>
                        </div>
                    </div>
                @endforeach

                <button type="button"
                        onclick="@this.call('addChild')"
                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('Add') }}
                </button>
            </div>
        </div>

        <!-- Интересы и увлечения -->
        <div>
            <label class="block text-base font-medium text-gray-700 mb-3">{{ __('Interests & Hobbies') }}</label>
            <div class="p-4 bg-gray-50 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            {{ __('Hobbies') }} <span class="text-red-500">*</span>
                        </label>
                        <textarea wire:model="hobbies" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="{{ __('Enter your hobbies') }}"></textarea>
                        @error('hobbies') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            {{ __('Interests') }} <span class="text-red-500">*</span>
                        </label>
                        <textarea wire:model="interests" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="{{ __('Enter your interests') }}"></textarea>
                        @error('interests') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            {{ __('Favorite Sports') }} <span class="text-red-500">*</span>
                        </label>
                        <textarea wire:model="favorite_sports" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="{{ __('Enter your favorite sports') }}"></textarea>
                        @error('favorite_sports') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            {{ __('Books per Year') }}
                        </label>
                        <div class="flex items-center gap-3 mt-1">
                            <input type="number"
                                   wire:model="books_per_year_min"
                                   min="0"
                                   max="100"
                                   placeholder="{{ __('from') }}"
                                   class="w-24 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <span class="text-gray-400">—</span>
                            <input type="number"
                                   wire:model="books_per_year_max"
                                   min="0"
                                   max="100"
                                   placeholder="{{ __('To') }}"
                                   class="w-24 px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        </div>
                        @error('books_per_year_min') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        @error('books_per_year_max') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Посещённые страны -->
        <div>
            <label class="block text-base font-medium text-gray-700 mb-3">{{ __('Visited Countries') }} <span class="text-red-500">*</span></label>
            <div class="p-4 bg-gray-50 rounded-lg">
                <!-- Выбранные страны (badges) -->
                @if(count($visited_countries) > 0)
                    <div class="flex flex-wrap gap-2 mb-4" id="selected-countries-badges">
                        @foreach($visited_countries as $country)
                            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium bg-gradient-to-r from-blue-500 to-blue-600 text-white shadow-sm hover:shadow-md transition-shadow">
                                @php
                                    $countryData = collect($countries)->firstWhere('name_ru', $country);
                                    $displayName = $countryData['display_name'] ?? $country;
                                @endphp
                                @if($countryData && isset($countryData['flag_url']))
                                    <img src="{{ $countryData['flag_url'] }}"
                                         alt="flag"
                                         class="w-5 h-4 mr-2 rounded border border-white/30 object-cover">
                                @endif
                                {{ $displayName }}
                                <button type="button"
                                        wire:click.prevent="removeCountry('{{ $country }}')"
                                        wire:loading.attr="disabled"
                                        wire:target="removeCountry('{{ $country }}')"
                                        class="ml-2 text-white/80 hover:text-white focus:outline-none disabled:opacity-50">
                                    <svg wire:loading.remove wire:target="removeCountry('{{ $country }}')" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    <svg wire:loading wire:target="removeCountry('{{ $country }}')" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </button>
                            </span>
                        @endforeach
                    </div>
                @endif

                <!-- Select2 для выбора стран -->
                <div wire:ignore>
                    <select id="country-select-2" class="block w-full rounded-lg border-gray-300 shadow-sm">
                        <option value="">{{ __('Select country to add') }}</option>
                        @foreach($countries as $country)
                            <option value="{{ $country['name_ru'] }}"
                                    data-flag="{{ $country['flag_url'] ?? '' }}">
                                {{ $country['display_name'] ?? $country['name_ru'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @error('visited_countries') <span class="text-red-500 text-sm mt-2 block">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Медиа потребление -->
        <div>
            <label class="block text-base font-medium text-gray-700 mb-3">{{ __('Media Consumption') }}</label>
            <div class="p-4 bg-gray-50 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Развлекательные видео -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Entertainment Videos (hours per week)') }}</label>
                        <input type="number"
                               wire:model="entertainment_hours_weekly"
                               min="0"
                               max="168"
                               placeholder="0"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('entertainment_hours_weekly') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Образовательные видео -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Educational Videos (hours per week)') }}</label>
                        <input type="number"
                               wire:model="educational_hours_weekly"
                               min="0"
                               max="168"
                               placeholder="0"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('educational_hours_weekly') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <!-- Социальные сети -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700">{{ __('Social Media (hours per week)') }}</label>
                        <input type="number"
                               wire:model="social_media_hours_weekly"
                               min="0"
                               max="168"
                               placeholder="0"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('social_media_hours_weekly') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<!-- Select2 Кастомные стили -->
<style>
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

    .select2-dropdown {
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }

    .select2-results__option img {
        margin-right: 8px;
        vertical-align: middle;
    }
</style>

<!-- jQuery (требуется для Select2) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Select2 JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    function initSelect2() {
        const selectElement = $('#country-select-2');

        if (!selectElement.length) {
            return false;
        }

        if (selectElement.hasClass('select2-hidden-accessible')) {
            return true;
        }

        try {
            selectElement.select2({
                placeholder: '{{ __("Start typing country name...") }}',
                allowClear: true,
                width: '100%',
                templateResult: formatCountryOption,
                templateSelection: formatCountrySelection,
                language: {
                    noResults: function() {
                        return "{{ __('Country not found') }}";
                    },
                    searching: function() {
                        return "{{ __('Searching...') }}";
                    }
                }
            });

            selectElement.off('select2:select');

            selectElement.on('select2:select', function(e) {
                const country = e.params.data.id;

                if (country) {
                    @this.call('addCountry', country).then(() => {
                        selectElement.val(null).trigger('change');
                    });
                }
            });

            return true;
        } catch (error) {
            return false;
        }
    }

    function formatCountryOption(country) {
        if (!country.id) {
            return country.text;
        }

        const $country = $(
            '<span><img src="' + $(country.element).data('flag') + '" class="inline-block w-6 h-4 mr-2 rounded" onerror="this.style.display=\'none\'" /> ' + country.text + '</span>'
        );

        return $country;
    }

    function formatCountrySelection(country) {
        return country.text || '{{ __("Select country to add") }}';
    }

    initSelect2();

    document.addEventListener('livewire:initialized', () => {
        Livewire.on('step-changed', (event) => {
            const step = event.step || event[0]?.step || event[0];

            if (step === 2) {
                setTimeout(() => initSelect2(), 100);
                setTimeout(() => initSelect2(), 300);
                setTimeout(() => initSelect2(), 500);
            }
        });
    });

    Livewire.hook('message.processed', (message, component) => {
        const delays = [50, 100, 200, 300, 500];

        delays.forEach(delay => {
            setTimeout(() => {
                const selectElement = $('#country-select-2');

                if (selectElement.length && !selectElement.hasClass('select2-hidden-accessible')) {
                    initSelect2();
                }
            }, delay);
        });
    });

    setInterval(() => {
        const selectElement = $('#country-select-2');
        if (selectElement.length && !selectElement.hasClass('select2-hidden-accessible')) {
            initSelect2();
        }
    }, 1000);
});
</script>
