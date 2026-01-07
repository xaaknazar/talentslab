@if($currentStep === 1)
<div class="step">
    <h2 class="text-2xl font-bold mb-6">{{ __('Basic Information') }}</h2>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Фото -->
        <div class="col-span-1 lg:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">
                {{ __('Photo') }} <span class="text-red-500">*</span>
            </label>

            <!-- Предпросмотр фото -->
            <div id="photo-preview" class="mb-4 {{ $photoPreview ? '' : 'hidden' }}">
                <div class="relative w-32 h-40 mx-auto">
                    <img id="preview-image"
                         src="{{ $photoPreview }}"
                         alt="{{ __('Photo') }}"
                         class="w-full h-full object-cover rounded-lg shadow-md">
                    <button type="button"
                            onclick="removePhoto()"
                            class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full p-1 hover:bg-red-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Модальное окно для обрезки -->
            <div id="crop-modal" class="fixed inset-0 z-[9999] hidden">
                <div class="flex items-center justify-center min-h-screen px-4">
                    <div class="fixed inset-0 bg-black opacity-50"></div>
                    <div class="relative bg-white rounded-lg max-w-4xl w-full mx-auto z-[10000]">
                        <div class="px-6 py-4 border-b">
                            <h3 class="text-lg font-semibold">{{ __('Crop Photo') }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ __('Select photo area (aspect ratio 3:4)') }}</p>
                        </div>
                        <div class="p-6">
                            <div class="max-h-[60vh] overflow-hidden">
                                <img id="crop-image" class="max-w-full">
                            </div>
                        </div>
                        <div class="px-6 py-4 border-t flex justify-end space-x-3">
                            <button type="button"
                                    onclick="cancelCrop()"
                                    class="px-4 py-2 text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300">
                                {{ __('Cancel') }}
                            </button>
                            <button type="button"
                                    onclick="saveCrop()"
                                    class="px-4 py-2 text-white bg-blue-600 rounded-md hover:bg-blue-700">
                                {{ __('Save') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Область загрузки -->
            <div id="upload-area" class="{{ $photoPreview ? 'hidden' : '' }}">
                <label for="photo-input" class="block cursor-pointer">
                    <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-gray-400 transition-colors">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <p class="mt-2 text-sm text-gray-600">
                            <span class="font-semibold">{{ __('Click to upload') }}</span> {{ __('or drag and drop') }}
                        </p>
                        <p class="text-xs text-gray-500 mt-1">{{ __('PNG, JPG up to 20MB (aspect ratio 3:4)') }}</p>
                    </div>
                    <input id="photo-input"
                           type="file"
                           accept="image/png,image/jpeg,image/jpg"
                           class="hidden">
                    <!-- Скрытый input для Livewire как fallback -->
                    <input id="photo-livewire-fallback"
                           type="file"
                           wire:model="photo"
                           accept="image/png,image/jpeg,image/jpg"
                           class="hidden"
                           style="display: none !important;">
                </label>
            </div>

            <!-- Индикатор загрузки -->
            <div id="loading-indicator" class="mt-2 hidden">
                <div class="flex items-center justify-center space-x-2">
                    <div class="animate-spin rounded-full h-4 w-4 border-b-2 border-blue-600"></div>
                    <span class="text-sm text-gray-600">{{ __('Processing...') }}</span>
                </div>
            </div>

            @error('photo')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror

            <!-- Скрытая кнопка для удаления фото (fallback) -->
            <button id="hidden-remove-photo-btn"
                    wire:click="removePhoto"
                    type="button"
                    style="display: none !important;">
            </button>
        </div>

        <!-- ФИО -->
        <div class="col-span-1 lg:col-span-2">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- Имя -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        {{ __('First Name') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="first-name-input"
                           wire:model="first_name"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 capitalize"
                           placeholder="{{ app()->getLocale() == 'ar' ? 'أحمد' : (app()->getLocale() == 'en' ? 'John' : 'Иван') }}">
                    @error('first_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Фамилия -->
                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        {{ __('Last Name') }} <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           id="last-name-input"
                           wire:model="last_name"
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 capitalize"
                           placeholder="{{ app()->getLocale() == 'ar' ? 'محمد' : (app()->getLocale() == 'en' ? 'Smith' : 'Иванов') }}">
                    @error('last_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>

        <!-- Email -->
        <div>
            <label class="block text-sm font-medium text-gray-700">
                {{ __('Email') }} <span class="text-red-500">*</span>
            </label>
            <input type="email"
                   wire:model="email"
                   readonly
                   class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 cursor-not-allowed">
            @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Инстаграм -->
        <div>
            <label class="block text-sm font-medium text-gray-700">
                {{ __('Instagram') }} <span class="text-gray-500">({{ __('in format @username') }})</span>
            </label>
            <input type="text"
                   wire:model="instagram"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                   placeholder="{{ __('username (@ sign will be added automatically)') }}">
            @error('instagram') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Телефон -->
        <div>
            <label class="block text-sm font-medium text-gray-700">
                {{ __('Phone') }} <span class="text-red-500">*</span><span class="text-gray-500"> ({{ __('in format +77081992519') }})</span>
            </label>
            <input type="text"
                   wire:model.lazy="phone"
                   id="phone-input"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                   placeholder="+77081992519">
            @error('phone') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Пол -->
        <div>
            <label class="block text-sm font-medium text-gray-700">
                {{ __('Gender') }} <span class="text-red-500">*</span>
            </label>
            <select wire:model="gender" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">{{ __('Select gender') }}</option>
                <option value="Мужской">{{ __('Male') }}</option>
                <option value="Женский">{{ __('Female') }}</option>
            </select>
            @error('gender') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Семейное положение -->
        <div>
            <label class="block text-sm font-medium text-gray-700">
                {{ __('Marital Status') }} <span class="text-red-500">*</span>
            </label>
            <select wire:model="marital_status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">{{ __('Select marital status') }}</option>
                <option value="Холост/Не замужем">{{ __('Single') }}</option>
                <option value="Женат/Замужем">{{ __('Married') }}</option>
                <option value="Разведен(а)">{{ __('Divorced') }}</option>
                <option value="Вдовец/Вдова">{{ __('Widowed') }}</option>
            </select>
            @error('marital_status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Дата рождения -->
        <div>
            <label class="block text-sm font-medium text-gray-700">
                {{ __('Date of Birth') }} <span class="text-red-500">*</span>
            </label>
            <input type="date"
                   wire:model="birth_date"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
            @error('birth_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Место рождения -->
        <div>
            <label class="block text-sm font-medium text-gray-700">
                {{ __('Place of Birth') }} <span class="text-red-500">*</span>
            </label>
            <input type="text"
                   id="birth-place-input"
                   wire:model="birth_place"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 capitalize"
                   placeholder="{{ app()->getLocale() == 'ar' ? 'الرياض' : (app()->getLocale() == 'en' ? 'New York' : 'Москва') }}">
            @error('birth_place') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Гражданство -->
        <div>
            <label class="block text-sm font-medium text-gray-700">
                {{ __('Citizenship') }}
            </label>
            <select wire:model="citizenship"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">{{ __('Select country') }}</option>
                @foreach($countries as $country)
                    <option value="{{ $country['name_ru'] }}">{{ $country['display_name'] ?? $country['name_ru'] }}</option>
                @endforeach
            </select>
            @error('citizenship') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Разрешение на работу -->
        <div>
            <label class="block text-sm font-medium text-gray-700">
                {{ __('Work Permit') }}
            </label>
            <div x-data="{
                open: false,
                search: '',
                get filteredCountries() {
                    if (!this.search) return $wire.countries;
                    return $wire.countries.filter(c =>
                        (c.display_name || c.name_ru).toLowerCase().includes(this.search.toLowerCase())
                    );
                }
            }" class="relative">
                <div @click="open = !open"
                     class="mt-1 block w-full rounded-md border border-gray-300 shadow-sm bg-white cursor-pointer min-h-[42px] p-2">
                    <div class="flex flex-wrap gap-1">
                        @forelse($work_permits as $permit)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                {{ $permit }}
                                <button type="button"
                                        wire:click="removeWorkPermit('{{ $permit }}')"
                                        @click.stop
                                        class="ml-1 text-blue-600 hover:text-blue-800">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </span>
                        @empty
                            <span class="text-gray-400 text-sm">{{ __('Select countries') }}</span>
                        @endforelse
                    </div>
                </div>

                <div x-show="open"
                     @click.outside="open = false"
                     x-transition
                     class="absolute z-50 mt-1 w-full bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-auto">
                    <div class="sticky top-0 bg-white p-2 border-b">
                        <input type="text"
                               x-model="search"
                               @click.stop
                               placeholder="{{ __('Search...') }}"
                               class="w-full px-3 py-2 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <template x-for="country in filteredCountries" :key="country.name_ru">
                        <div @click="$wire.addWorkPermit(country.name_ru); search = ''"
                             class="px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm"
                             :class="{ 'bg-blue-50': $wire.work_permits.includes(country.name_ru) }">
                            <span x-text="country.display_name || country.name_ru"></span>
                            <template x-if="$wire.work_permits.includes(country.name_ru)">
                                <svg class="w-4 h-4 inline ml-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
            @error('work_permits') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Текущий город -->
        <div>
            <label class="block text-sm font-medium text-gray-700">
                {{ __('Current City') }} <span class="text-red-500">*</span>
            </label>
            <input type="text"
                   id="current-city-input"
                   wire:model="current_city"
                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 capitalize"
                   placeholder="{{ app()->getLocale() == 'ar' ? 'دبي' : (app()->getLocale() == 'en' ? 'London' : 'Москва') }}">
            @error('current_city') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Готов к переезду -->
        <div>
            <label class="block text-sm font-medium text-gray-700">
                {{ __('Ready to Relocate') }}
            </label>
            <div class="mt-2">
                <label class="inline-flex items-center">
                    <input type="checkbox"
                           wire:model="ready_to_relocate"
                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <span class="ml-2 text-sm text-gray-700">{{ __('Yes, ready to relocate') }}</span>
                </label>
            </div>
            @error('ready_to_relocate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
console.log('Step1 template loaded');
</script>
@endpush
