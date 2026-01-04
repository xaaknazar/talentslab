@if($currentStep === 3)
<div class="step">
    <h2 class="text-2xl font-bold mb-6">{{ __('Education and Work') }}</h2>

    <div class="grid grid-cols-1 gap-6">
        <!-- Школа -->
        <div>
            <label class="block text-base font-medium text-gray-700 mb-3">{{ __('School') }} <span class="text-red-500">*</span></label>
            <div class="p-4 bg-gray-50 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            {{ __('School Name') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               wire:model="school_name"
                               placeholder="{{ app()->getLocale() == 'ar' ? 'المدرسة رقم 25' : (app()->getLocale() == 'en' ? 'School #25' : 'Школа №25') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('school_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            {{ __('City') }} <span class="text-red-500">*</span>
                        </label>
                        <input type="text"
                               wire:model="school_city"
                               placeholder="{{ app()->getLocale() == 'ar' ? 'الرياض' : (app()->getLocale() == 'en' ? 'London' : 'Актау') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @error('school_city') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">
                            {{ __('Graduation Year') }} <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="school_graduation_year"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="" selected disabled>{{ __('Select year') }}</option>
                            @for($year = 1970; $year <= 2035; $year++)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endfor
                        </select>
                        @error('school_graduation_year') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Университеты -->
        <div>
            <label class="block text-base font-medium text-gray-700">{{ __('University | College') }}</label>
            <div class="space-y-4">
                @foreach($universities as $index => $university)
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ __('University or College Name') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       wire:model="universities.{{ $index }}.name"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error("universities.{$index}.name") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ __('City') }}
                                </label>
                                <input type="text"
                                       wire:model="universities.{{ $index }}.city"
                                       placeholder="{{ app()->getLocale() == 'ar' ? 'دبي' : (app()->getLocale() == 'en' ? 'New York' : 'Алматы') }}"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error("universities.{$index}.city") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ __('Graduation Year') }} <span class="text-red-500">*</span>
                                </label>
                                <select wire:model="universities.{{ $index }}.graduation_year"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="" selected disabled>{{ __('Select year') }}</option>
                                    @for($year = 1970; $year <= 2035; $year++)
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endfor
                                </select>
                                @error("universities.{$index}.graduation_year") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ __('Specialization') }} <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       wire:model="universities.{{ $index }}.speciality"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                @error("universities.{$index}.speciality") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ __('Degree') }}
                                </label>
                                <select wire:model="universities.{{ $index }}.degree"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">{{ __('Select degree') }}</option>
                                    <option value="Средне-специальное">{{ __('Vocational') }}</option>
                                    <option value="Бакалавр">{{ __('Bachelor') }}</option>
                                    <option value="Магистратура">{{ __('Master') }}</option>
                                    <option value="PhD">PhD</option>
                                </select>
                                @error("universities.{$index}.degree") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">
                                    {{ __('GPA') }} <span class="text-gray-500">({{ __('optional') }})</span>
                                </label>
                                <input type="number"
                                       wire:model="universities.{{ $index }}.gpa"
                                       name="universities[{{ $index }}][gpa]"
                                       min="0"
                                       max="4.0"
                                       step="0.01"
                                       placeholder="3.75"
                                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                <p class="mt-1 text-xs text-gray-500">{{ __('Enter value from 0 to 4.0') }}</p>
                                @error("universities.{$index}.gpa") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="button" wire:click="removeUniversity({{ $index }})" class="text-red-600 hover:text-red-800">
                                {{ __('Delete') }}
                            </button>
                        </div>
                    </div>
                @endforeach

                <button type="button"
                        wire:click="addUniversity"
                        class="mt-4 inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('Add') }}
                </button>
            </div>
        </div>

        <!-- Опыт работы -->
        <div>
            <label class="block text-base font-medium text-gray-700">{{ __('Work Experience') }}</label>
            <div class="space-y-4">
                @foreach($work_experience as $index => $experience)
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Левая колонка: Период работы -->
                            <div>
                                <div class="bg-white border border-gray-200 p-4 rounded-lg">
                                    <div class="flex items-center justify-between mb-4">
                                        <span class="text-sm font-medium text-gray-700">{{ __('Work Period') }}</span>
                                        <span id="period-display-{{ $index }}" class="px-3 py-1 bg-gray-100 text-gray-700 text-sm font-medium rounded">
                                            {{ $experience['years'] ?? __('Select period') }}
                                        </span>
                                    </div>

                                    <!-- Начало работы -->
                                    <div class="mb-4">
                                        <div class="flex items-center justify-between mb-2">
                                            <label class="text-sm font-medium text-gray-700">{{ __('Start Date') }}</label>
                                            <span id="start-display-{{ $index }}" class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded">
                                                {{ __('not selected') }}
                                            </span>
                                        </div>

                                        <div class="grid grid-cols-2 gap-2">
                                            <div>
                                                <select wire:model="work_experience.{{ $index }}.start_month"
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                                        onchange="updatePeriodDisplay({{ $index }})">
                                                    <option value="" selected disabled>{{ __('Month') }}</option>
                                                    <option value="0">{{ __('January') }}</option>
                                                    <option value="1">{{ __('February') }}</option>
                                                    <option value="2">{{ __('March') }}</option>
                                                    <option value="3">{{ __('April') }}</option>
                                                    <option value="4">{{ __('May') }}</option>
                                                    <option value="5">{{ __('June') }}</option>
                                                    <option value="6">{{ __('July') }}</option>
                                                    <option value="7">{{ __('August') }}</option>
                                                    <option value="8">{{ __('September') }}</option>
                                                    <option value="9">{{ __('October') }}</option>
                                                    <option value="10">{{ __('November') }}</option>
                                                    <option value="11">{{ __('December') }}</option>
                                                </select>
                                            </div>

                                            <div>
                                                <select wire:model="work_experience.{{ $index }}.start_year"
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                                        onchange="updatePeriodDisplay({{ $index }})">
                                                    <option value="" selected disabled>{{ __('Year') }}</option>
                                                    @for($year = 1990; $year <= 2025; $year++)
                                                        <option value="{{ $year }}">{{ $year }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Окончание работы -->
                                    <div class="mb-3">
                                        <div class="flex items-center justify-between mb-2">
                                            <label class="text-sm font-medium text-gray-700">{{ __('End Date') }}</label>
                                            <span id="end-display-{{ $index }}" class="px-2 py-1 bg-gray-100 text-gray-600 text-xs font-medium rounded">
                                                {{ __('not selected') }}
                                            </span>
                                        </div>

                                        <div class="grid grid-cols-2 gap-2" id="end-period-selects-{{ $index }}">
                                            <div>
                                                <select wire:model="work_experience.{{ $index }}.end_month"
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                                        onchange="updatePeriodDisplay({{ $index }})">
                                                    <option value="" selected disabled>{{ __('Month') }}</option>
                                                    <option value="0">{{ __('January') }}</option>
                                                    <option value="1">{{ __('February') }}</option>
                                                    <option value="2">{{ __('March') }}</option>
                                                    <option value="3">{{ __('April') }}</option>
                                                    <option value="4">{{ __('May') }}</option>
                                                    <option value="5">{{ __('June') }}</option>
                                                    <option value="6">{{ __('July') }}</option>
                                                    <option value="7">{{ __('August') }}</option>
                                                    <option value="8">{{ __('September') }}</option>
                                                    <option value="9">{{ __('October') }}</option>
                                                    <option value="10">{{ __('November') }}</option>
                                                    <option value="11">{{ __('December') }}</option>
                                                </select>
                                            </div>

                                            <div>
                                                <select wire:model="work_experience.{{ $index }}.end_year"
                                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                                        onchange="updatePeriodDisplay({{ $index }})">
                                                    <option value="" selected disabled>{{ __('Year') }}</option>
                                                    @for($year = 1990; $year <= 2025; $year++)
                                                        <option value="{{ $year }}">{{ $year }}</option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Текущая работа -->
                                <div class="flex items-center mt-3">
                                    <input type="checkbox"
                                           wire:model="work_experience.{{ $index }}.is_current"
                                           class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                                           onchange="toggleCurrentWork({{ $index }})">
                                    <label class="ml-2 text-sm text-gray-700">{{ __('Currently Working') }}</label>
                                </div>

                                <input type="hidden"
                                       wire:model="work_experience.{{ $index }}.years"
                                       id="period-hidden-{{ $index }}">

                                @error("work_experience.{$index}.years") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <!-- Правая колонка: Информация о компании -->
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        {{ __('Position') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                           wire:model="work_experience.{{ $index }}.position"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error("work_experience.{$index}.position") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        {{ __('City') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                           wire:model="work_experience.{{ $index }}.city"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error("work_experience.{$index}.city") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        {{ __('Company Name') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text"
                                           wire:model="work_experience.{{ $index }}.company"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    @error("work_experience.{$index}.company") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        {{ __('Field of Activity') }}
                                    </label>
                                    <select wire:model="work_experience.{{ $index }}.activity_sphere"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">{{ __('Select field of activity') }}</option>
                                        @foreach($activitySpheres as $key => $sphere)
                                            <option value="{{ $sphere }}">{{ __($sphere) }}</option>
                                        @endforeach
                                    </select>
                                    @error("work_experience.{$index}.activity_sphere") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Основные задачи -->
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <label class="block text-sm font-medium text-gray-700 mb-3">
                                {{ __('Main Tasks') }} <span class="text-red-500">*</span>
                                <span class="text-xs text-gray-500 font-normal ml-2">({{ __('minimum 3, maximum 8') }})</span>
                            </label>
                            <div class="space-y-2">
                                @php
                                    $tasks = $experience['main_tasks'] ?? ['', '', ''];
                                @endphp
                                @foreach($tasks as $taskIndex => $task)
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm text-gray-500 w-6 text-center">{{ $taskIndex + 1 }}.</span>
                                        <input type="text"
                                               wire:model="work_experience.{{ $index }}.main_tasks.{{ $taskIndex }}"
                                               class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                               placeholder="{{ __('Describe one task in one sentence') }}">
                                        @if(count($tasks) > 3)
                                            <button type="button"
                                                    wire:click="removeWorkTask({{ $index }}, {{ $taskIndex }})"
                                                    class="text-red-500 hover:text-red-700 p-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            @if(count($tasks) < 8)
                                <button type="button"
                                        wire:click="addWorkTask({{ $index }})"
                                        class="mt-2 inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                    </svg>
                                    {{ __('Add task') }}
                                </button>
                            @endif
                            @error("work_experience.{$index}.main_tasks") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mt-4 pt-2">
                            <button type="button" wire:click="removeWorkExperience({{ $index }})" class="text-red-600 hover:text-red-800">
                                {{ __('Delete') }}
                            </button>
                        </div>
                    </div>
                @endforeach

                <button type="button"
                        wire:click="addWorkExperience"
                        class="mt-4 inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('Add') }}
                </button>
            </div>
        </div>

        <!-- Общий стаж и удовлетворенность работой -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('Total Work Experience (years)') }} <span class="text-red-500">*</span>
                </label>
                <input type="number"
                       wire:model="total_experience_years"
                       name="total_experience_years"
                       min="0"
                       max="50"
                       step="1"
                       placeholder="5"
                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('total_experience_years') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    {{ __('Job Satisfaction (1-5)') }}
                </label>
                <input type="number"
                       wire:model="job_satisfaction"
                       name="job_satisfaction"
                       min="1"
                       max="5"
                       step="1"
                       placeholder="{{ __('From 1 to 5') }}"
                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                @error('job_satisfaction') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
            </div>
        </div>

        <!-- Языковые навыки -->
        <div class="mb-6">
            <label class="block text-base font-medium text-gray-700">{{ __('Language Skills') }} <span class="text-red-500">*</span></label>
            <div class="space-y-4">
                @foreach($language_skills as $index => $skill)
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('Language') }}</label>
                                <div wire:ignore>
                                    <select id="language-select-{{ $index }}"
                                            class="language-select-field mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                            data-index="{{ $index }}">
                                        <option value="">{{ __('Select language') }}</option>
                                        @foreach($languages ?? [] as $language)
                                            <option value="{{ $language }}" {{ ($skill['language'] ?? '') == $language ? 'selected' : '' }}>
                                                {{ $language }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error("language_skills.{$index}.language") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">{{ __('Level') }}</label>
                                <select wire:model="language_skills.{{ $index }}.level"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                    <option value="">{{ __('Select level') }}</option>
                                    <option value="Начальный">{{ __('Beginner') }}</option>
                                    <option value="Средний">{{ __('Intermediate') }}</option>
                                    <option value="Выше среднего">{{ __('Upper Intermediate') }}</option>
                                    <option value="Продвинутый">{{ __('Advanced') }}</option>
                                    <option value="В совершенстве">{{ __('Fluent') }}</option>
                                </select>
                                @error("language_skills.{$index}.level") <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="mt-2">
                            <button type="button" wire:click="removeLanguage({{ $index }})" class="text-red-600 hover:text-red-800">
                                {{ __('Delete') }}
                            </button>
                        </div>
                    </div>
                @endforeach

                <button type="button"
                        wire:click="addLanguage"
                        class="mt-4 inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-medium text-sm text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    {{ __('Add') }}
                </button>
            </div>
        </div>

        <!-- Награды и достижения -->
        <div class="mb-6">
            <label class="block text-base font-medium text-gray-700 mb-3">
                {{ __('Awards and Achievements') }}
                <span class="text-gray-500 text-sm font-normal">({{ __('Optional') }})</span>
            </label>
            <p class="text-xs text-gray-500 mb-3">
                <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    {{ __('Example: Exceeded sales plan, Improved customer service, Optimized department workflow') }}
                </span>
            </p>
            <div class="space-y-2">
                @foreach($awards as $index => $award)
                    <div class="flex items-center gap-2">
                        <span class="text-sm text-gray-500 w-6 text-center">{{ $index + 1 }}.</span>
                        <input type="text"
                               wire:model="awards.{{ $index }}"
                               class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                               placeholder="{{ __('Describe your achievement') }}">
                        <button type="button"
                                wire:click="removeAward({{ $index }})"
                                class="text-red-500 hover:text-red-700 p-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>
                @endforeach
            </div>
            <button type="button"
                    wire:click="addAward"
                    class="mt-2 inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                {{ __('Add achievement') }}
            </button>
            @error('awards') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Желаемая должность и Ожидаемая зарплата -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-6">
            <!-- Желаемая должность (множественный выбор) -->
            <div>
                <label class="block text-base font-medium text-gray-700 mb-2">
                    {{ __('Desired Position') }} <span class="text-red-500">*</span>
                    <span class="text-gray-500 text-sm font-normal ml-2">({{ __('maximum 3') }})</span>
                </label>
                <div class="space-y-2">
                    @foreach($desired_positions as $index => $position)
                        <div class="flex items-center gap-2">
                            <span class="text-sm text-gray-500 w-6 text-center">{{ $index + 1 }}.</span>
                            <input type="text"
                                   wire:model="desired_positions.{{ $index }}"
                                   class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   placeholder="{{ __('Example: Financial Analyst') }}">
                            @if(count($desired_positions) > 1)
                                <button type="button"
                                        wire:click="removeDesiredPosition({{ $index }})"
                                        class="text-red-500 hover:text-red-700 p-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
                @if(count($desired_positions) < 3)
                    <button type="button"
                            wire:click="addDesiredPosition"
                            class="mt-2 inline-flex items-center text-sm text-blue-600 hover:text-blue-800">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        {{ __('Add') }}
                    </button>
                @endif
                @error('desired_positions') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                @error('desired_positions.*') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Ожидаемая зарплата (диапазон) -->
            <div>
                <label class="block text-base font-medium text-gray-700 mb-2">
                    {{ __('Expected Salary') }} <span class="text-red-500">*</span>
                </label>

                <!-- Выбор валюты -->
                <div class="mb-3">
                    <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('Currency') }}</label>
                    <select wire:model="salary_currency"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="KZT">₸ (KZT)</option>
                        <option value="USD">$ (USD)</option>
                    </select>
                    @error('salary_currency') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <!-- Диапазон зарплаты -->
                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('From') }} <span class="text-red-500">*</span></label>
                        <input type="text"
                               id="salary_from_formatted"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="400 000"
                               autocomplete="off"
                               oninput="formatSalaryFrom(this); validateSalaryFrom();"
                               onpaste="handleSalaryPasteFrom(event)"
                               onkeypress="return allowOnlyNumbers(event)">
                        <input type="hidden"
                               wire:model="expected_salary_from"
                               id="salary_from_hidden">
                        <div id="salary_from_warning" class="hidden text-orange-600 text-xs mt-1"></div>
                        @error('expected_salary_from') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">{{ __('To') }} <span class="text-red-500">*</span></label>
                        <input type="text"
                               id="salary_to_formatted"
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               placeholder="500 000"
                               autocomplete="off"
                               oninput="formatSalaryTo(this); validateSalaryTo();"
                               onpaste="handleSalaryPasteTo(event)"
                               onkeypress="return allowOnlyNumbers(event)">
                        <input type="hidden"
                               wire:model="expected_salary_to"
                               id="salary_to_hidden">
                        <div id="salary_to_warning" class="hidden text-orange-600 text-xs mt-1"></div>
                        @error('expected_salary_to') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <p class="mt-1 text-xs text-gray-500">{{ __('Enter salary range, for example: 400,000 - 500,000') }}</p>
            </div>
        </div>

        <!-- Компьютерные навыки и Требования к работодателю -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Компьютерные навыки -->
            <div>
                <label class="block text-base font-medium text-gray-700">{{ __('Computer Skills') }} <span class="text-red-500">*</span></label>
                <p class="text-xs text-gray-500 mt-1 mb-2">
                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        {{ __('Example: Word, Excel, PowerPoint, Photoshop, 1C, etc.') }}
                    </span>
                </p>
                <textarea wire:model="computer_skills"
                          rows="3"
                          oninput="capitalizeAfterComma(this)"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                @error('computer_skills') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>

            <!-- Пожелания на рабочем месте -->
            <div>
                <label class="block text-base font-medium text-gray-700">{{ __('Workplace Requirements') }} <span class="text-red-500">*</span></label>
                <p class="text-xs text-gray-500 mt-1 mb-2">
                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        {{ __('Example: Training, growth opportunities, flexible schedule, private office, prayer room, etc.') }}
                    </span>
                </p>
                <textarea wire:model="employer_requirements"
                          rows="3"
                          class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"></textarea>
                @error('employer_requirements') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>
</div>
@endif

<script>
window.capitalizeAfterComma = function(textarea) {
    let text = textarea.value;
    let cursorPosition = textarea.selectionStart;

    let newText = text.replace(/,\s*([a-zа-яё])/gi, function(match, letter) {
        return ', ' + letter.toUpperCase();
    });

    if (newText.length > 0) {
        newText = newText.charAt(0).toUpperCase() + newText.slice(1);
    }

    if (text !== newText) {
        textarea.value = newText;
        textarea.setSelectionRange(cursorPosition, cursorPosition);
        textarea.dispatchEvent(new Event('input', { bubbles: true }));
    }
};

window.isBornAfter2000 = function() {
    const birthDateInput = document.querySelector('input[wire\\:model="birth_date"]');
    if (!birthDateInput || !birthDateInput.value) return false;
    const birthYear = new Date(birthDateInput.value).getFullYear();
    return birthYear >= 2000;
};

window.validateSalaryFrom = function() {
    const input = document.getElementById('salary_from_formatted');
    const warning = document.getElementById('salary_from_warning');
    if (!input || !warning) return;

    const numericValue = parseInt(input.value.replace(/\D/g, '')) || 0;
    const maxSalary = 2000000;

    if (isBornAfter2000() && numericValue > maxSalary) {
        warning.textContent = '{{ __("Maximum 2,000,000 KZT") }}';
        warning.classList.remove('hidden');
    } else {
        warning.classList.add('hidden');
    }
};

window.validateSalaryTo = function() {
    const input = document.getElementById('salary_to_formatted');
    const warning = document.getElementById('salary_to_warning');
    if (!input || !warning) return;

    const numericValue = parseInt(input.value.replace(/\D/g, '')) || 0;
    const maxSalary = 2000000;

    if (isBornAfter2000() && numericValue > maxSalary) {
        warning.textContent = '{{ __("Maximum 2,000,000 KZT") }}';
        warning.classList.remove('hidden');
    } else {
        warning.classList.add('hidden');
    }
};

window.formatSalaryFrom = function(input) {
    let value = input.value.replace(/\D/g, '');
    if (value) {
        input.value = parseInt(value).toLocaleString('ru-RU');
        document.getElementById('salary_from_hidden').value = value;
        document.getElementById('salary_from_hidden').dispatchEvent(new Event('input', { bubbles: true }));
    }
};

window.formatSalaryTo = function(input) {
    let value = input.value.replace(/\D/g, '');
    if (value) {
        input.value = parseInt(value).toLocaleString('ru-RU');
        document.getElementById('salary_to_hidden').value = value;
        document.getElementById('salary_to_hidden').dispatchEvent(new Event('input', { bubbles: true }));
    }
};

window.handleSalaryPasteFrom = function(event) {
    event.preventDefault();
    let pastedText = (event.clipboardData || window.clipboardData).getData('text');
    let numericValue = pastedText.replace(/\D/g, '');
    if (numericValue) {
        event.target.value = parseInt(numericValue).toLocaleString('ru-RU');
        document.getElementById('salary_from_hidden').value = numericValue;
        document.getElementById('salary_from_hidden').dispatchEvent(new Event('input', { bubbles: true }));
    }
};

window.handleSalaryPasteTo = function(event) {
    event.preventDefault();
    let pastedText = (event.clipboardData || window.clipboardData).getData('text');
    let numericValue = pastedText.replace(/\D/g, '');
    if (numericValue) {
        event.target.value = parseInt(numericValue).toLocaleString('ru-RU');
        document.getElementById('salary_to_hidden').value = numericValue;
        document.getElementById('salary_to_hidden').dispatchEvent(new Event('input', { bubbles: true }));
    }
};

window.allowOnlyNumbers = function(event) {
    const charCode = event.which ? event.which : event.keyCode;
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false;
    }
    return true;
};

window.updatePeriodDisplay = function(index) {
    const months = {
        0: '{{ __("January") }}', 1: '{{ __("February") }}', 2: '{{ __("March") }}', 3: '{{ __("April") }}',
        4: '{{ __("May") }}', 5: '{{ __("June") }}', 6: '{{ __("July") }}', 7: '{{ __("August") }}',
        8: '{{ __("September") }}', 9: '{{ __("October") }}', 10: '{{ __("November") }}', 11: '{{ __("December") }}'
    };
    const shortMonths = {
        0: 'Янв', 1: 'Фев', 2: 'Мар', 3: 'Апр',
        4: 'Май', 5: 'Июн', 6: 'Июл', 7: 'Авг',
        8: 'Сен', 9: 'Окт', 10: 'Ноя', 11: 'Дек'
    };

    const startMonthSelect = document.querySelector(`select[wire\\:model="work_experience.${index}.start_month"]`);
    const startYearSelect = document.querySelector(`select[wire\\:model="work_experience.${index}.start_year"]`);
    const endMonthSelect = document.querySelector(`select[wire\\:model="work_experience.${index}.end_month"]`);
    const endYearSelect = document.querySelector(`select[wire\\:model="work_experience.${index}.end_year"]`);
    const isCurrentCheckbox = document.querySelector(`input[wire\\:model="work_experience.${index}.is_current"]`);
    const periodDisplay = document.getElementById('period-display-' + index);
    const startDisplay = document.getElementById('start-display-' + index);
    const endDisplay = document.getElementById('end-display-' + index);
    const hiddenInput = document.getElementById('period-hidden-' + index);

    if (!startMonthSelect || !startYearSelect) return;

    const startMonth = startMonthSelect.value;
    const startYear = startYearSelect.value;
    const endMonth = endMonthSelect ? endMonthSelect.value : '';
    const endYear = endYearSelect ? endYearSelect.value : '';
    const isCurrent = isCurrentCheckbox ? isCurrentCheckbox.checked : false;

    // Update start display
    if (startMonth !== '' && startYear) {
        const startMonthName = months[parseInt(startMonth)] || '';
        if (startDisplay) startDisplay.textContent = startMonthName + ' ' + startYear;
    }

    // Update end display
    if (isCurrent) {
        if (endDisplay) endDisplay.textContent = '{{ __("Present") }}';
    } else if (endMonth !== '' && endYear) {
        const endMonthName = months[parseInt(endMonth)] || '';
        if (endDisplay) endDisplay.textContent = endMonthName + ' ' + endYear;
    }

    // Update period display and hidden input
    if (startMonth !== '' && startYear) {
        const startShort = shortMonths[parseInt(startMonth)] + ' ' + startYear;
        let endShort = '';

        if (isCurrent) {
            endShort = '{{ __("Present") }}';
        } else if (endMonth !== '' && endYear) {
            endShort = shortMonths[parseInt(endMonth)] + ' ' + endYear;
        }

        const periodValue = endShort ? (startShort + ' — ' + endShort) : startShort;
        if (periodDisplay) periodDisplay.textContent = periodValue;
        if (hiddenInput) {
            hiddenInput.value = periodValue;
            hiddenInput.dispatchEvent(new Event('input', { bubbles: true }));
        }
    }
};

window.toggleCurrentWork = function(index) {
    const endSelects = document.getElementById('end-period-selects-' + index);
    const checkbox = document.querySelector(`input[wire\\:model="work_experience.${index}.is_current"]`);
    if (endSelects && checkbox) {
        if (checkbox.checked) {
            endSelects.style.opacity = '0.5';
            endSelects.style.pointerEvents = 'none';
        } else {
            endSelects.style.opacity = '1';
            endSelects.style.pointerEvents = 'auto';
        }
    }
    // Update period display after toggling
    updatePeriodDisplay(index);
};
</script>
