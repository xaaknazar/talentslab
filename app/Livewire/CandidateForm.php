<?php

namespace App\Livewire;

use App\Models\Candidate;
use App\Models\CandidateFile;
use App\Models\CandidateHistory;
use App\Models\CandidateStatus;
use App\Jobs\ProcessGallupFile;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Smalot\PdfParser\Parser;

class CandidateForm extends Component
{
    use WithFileUploads;

    protected $listeners = ['removePhoto' => 'removePhoto'];

    public $currentStep = 1;
    public $totalSteps = 4;
    public $candidate = null;
    public $isFirstLoad = true;

    // Step 1: Basic Information
    public $full_name;
    public $last_name;
    public $first_name;
    public $email;
    public $phone;
    public $gender;
    public $marital_status;
    public $birth_date;
    public $birth_place;
    public $current_city;
    public $ready_to_relocate;
    public $instagram;
    public $photo;
    public $photoPreview;

    // Step 2: Additional Information
    public $religion;
    public $is_practicing;
    public $family_members = [];
    // Новая структура для разделенных категорий семьи
    public $parents = [];
    public $siblings = [];
    public $children = [];
    public $hobbies;
    public $interests;
    public $visited_countries = [];
    public $newCountry = '';
    public $books_per_year;
    public $books_per_year_min = 0;
    public $books_per_year_max = 50;
    public $favorite_sports = [];
    public $entertainment_hours_weekly;
    public $educational_hours_weekly;
    public $social_media_hours_weekly;
    public $has_driving_license;

    // Step 3: Education and Work
    public $school;
    public $school_name;
    public $school_city;
    public $school_graduation_year;
    public $universities = [];
    public $language_skills = [];
    public $computer_skills;
    public $work_experience = [];
    public $total_experience_years;
    public $job_satisfaction;
    public $desired_positions = []; // Массив желаемых должностей (макс 3)
    public $activity_sphere; // Оставляем для обратной совместимости
    public $expected_salary;
    public $expected_salary_from;
    public $expected_salary_to;
    public $employer_requirements;
    public $awards = []; // Награды и достижения

    // Step 4: Tests
    public $gallup_pdf;
    public $mbti_type;
    public $gardner_test_completed = false;

    // Загружаем списки
    public $countries = [];
    public $languages = [];
    public $religions = [];
    public $sports = [];
    public $activitySpheres = [];

    public $familyYears = [];

    public function mount($candidateId = null)
    {
        try {
            // Устанавливаем значение по умолчанию для books_per_year
            $this->books_per_year = '0';
            $this->books_per_year_min = 0;
            $this->books_per_year_max = 0;
            $this->updateBooksPerYear();

            // Устанавливаем начальные значения для часов в неделю
            $this->entertainment_hours_weekly = 0;
            $this->educational_hours_weekly = 0;
            $this->social_media_hours_weekly = 0;

            // Загружаем списки из JSON файлов
            $jsonPath = base_path('resources/json/countries.json');
            logger()->debug('JSON path:', ['path' => $jsonPath, 'exists' => file_exists($jsonPath)]);

            if (!file_exists($jsonPath)) {
                throw new \Exception("JSON file not found at: " . $jsonPath);
            }

            $jsonContent = file_get_contents($jsonPath);
            logger()->debug('JSON content:', ['content' => substr($jsonContent, 0, 100)]);

            $countriesData = json_decode($jsonContent, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("JSON decode error: " . json_last_error_msg());
            }

            logger()->debug('Decoded countries:', [
                'count' => count($countriesData),
                'first_country' => $countriesData[0] ?? null
            ]);

            $locale = app()->getLocale();

            $this->countries = collect($countriesData)->map(function($country) use ($locale) {
                $data = [];

                // Проверяем наличие каждого ключа перед добавлением
                if (isset($country['name_ru'])) {
                    $data['name_ru'] = $country['name_ru'];
                }

                if (isset($country['flag_url'])) {
                    // Убедимся, что URL флага начинается с http:// или https://
                    $flagUrl = $country['flag_url'];
                    if (strpos($flagUrl, '//') === 0) {
                        $flagUrl = 'https:' . $flagUrl;
                    }
                    $data['flag_url'] = $flagUrl;
                }

                if (isset($country['iso_code2'])) {
                    $data['iso_code2'] = $country['iso_code2'];

                    // Добавляем локализованное название страны
                    $isoCode = $country['iso_code2'];
                    if (class_exists('Locale') && !empty($isoCode)) {
                        $localeMap = [
                            'ru' => 'ru_RU',
                            'en' => 'en_US',
                            'ar' => 'ar_SA'
                        ];
                        $intlLocale = $localeMap[$locale] ?? 'en_US';
                        $localizedName = \Locale::getDisplayRegion("-{$isoCode}", $intlLocale);
                        if (!empty($localizedName) && $localizedName !== $isoCode) {
                            $data['name_localized'] = $localizedName;
                        }
                    }
                }

                if (isset($country['iso_code3'])) {
                    $data['iso_code3'] = $country['iso_code3'];
                }

                // Устанавливаем display_name - используем локализованное или name_ru как fallback
                $data['display_name'] = $data['name_localized'] ?? $data['name_ru'] ?? '';

                return $data;
            })
            ->filter(function($country) {
                // Оставляем только страны, у которых есть как минимум name_ru
                return !empty($country['name_ru']);
            })
            ->values()
            ->all();

            logger()->debug('Final countries array:', [
                'count' => count($this->countries),
                'first_country' => $this->countries[0] ?? null,
                'keys' => array_keys($this->countries[0] ?? [])
            ]);

        } catch (\Exception $e) {
            logger()->error('Error loading countries:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $this->countries = [];
        }

        // Загружаем языки с обработкой ошибок
        try {
            $this->loadLanguages();
        } catch (\Exception $e) {
            logger()->error('Error loading languages: ' . $e->getMessage());
            // Fallback к базовым языкам
            $this->languages = ['Русский', 'Английский', 'Испанский', 'Французский', 'Немецкий', 'Китайский', 'Японский'];
        }

        $this->religions = config('lists.religions');
        $this->sports = config('lists.sports');
        $this->activitySpheres = config('lists.activity_spheres');


        // Инициализируем массивы
        $this->universities = [];
        $this->family_members = [];
        $this->parents = [];
        $this->siblings = [];
        $this->children = [];
        $this->visited_countries = [];
        $this->favorite_sports = '';
        $this->language_skills = [];
        $this->work_experience = [];
        $this->computer_skills = '';
        $this->desired_positions = ['']; // Инициализируем с одной пустой должностью
        $this->awards = []; // Инициализируем пустым массивом

        logger()->debug('Mount: work_experience initialized as empty array');

        // Инициализируем значения для step3 ползунков
        $this->total_experience_years = 0;
        $this->job_satisfaction = 1;
        $this->expected_salary = 0;

        // Устанавливаем email из авторизованного пользователя
        $this->email = auth()->user()->email;

        $this->familyYears = range(2025, 1920);

        if ($candidateId) {
            $this->candidate = Candidate::findOrFail($candidateId);

            // Дополнительная проверка прав доступа на уровне Livewire компонента
            // Пользователь может редактировать только свои анкеты, администратор - любые
            if ($this->candidate->user_id !== auth()->id() && !auth()->user()->is_admin) {
                abort(403, 'У вас нет прав для редактирования этой анкеты.');
            }

            $this->loadCandidateData();
            // Если анкета завершена (step >= 5), показываем первый шаг для редактирования
            $this->currentStep = $this->candidate->step >= 5 ? 1 : $this->candidate->step;
        } else {
            // Проверяем, есть ли незавершенная анкета для текущего пользователя
            $userId = auth()->id();
            if ($userId) {
                $this->candidate = Candidate::where('user_id', $userId)
                    ->latest()
                    ->first();

                if ($this->candidate) {
                    $this->loadCandidateData();
                    // Если анкета завершена (step >= 5), показываем первый шаг для редактирования
                    $this->currentStep = $this->candidate->step >= 5 ? 1 : $this->candidate->step;
                } else {
                    // Инициализируем пустые массивы для нового кандидата
                    $this->family_members = [];
                    $this->parents = [];
                    $this->siblings = [];
                    $this->children = [];
                    // Убеждаемся что work_experience пустой массив
                    $this->work_experience = [];
                }
            }
        }

        // Инициализируем валидацию для текущего шага
        $this->reinitializeValidation();

        // Если загружаем существующего кандидата, это не первая загрузка
        if ($this->candidate) {
            $this->isFirstLoad = false;
        }

        // Проверяем, пройден ли тест Гарднера
        $this->checkGardnerTestStatus();
    }

    /**
     * Проверяет статус прохождения теста Гарднера для кандидата
     */
    public function checkGardnerTestStatus()
    {
        // Если редактируем существующего кандидата, проверяем его user_id
        if ($this->candidate && $this->candidate->user_id) {
            $this->gardner_test_completed = \App\Models\GardnerTestResult::where('user_id', $this->candidate->user_id)->exists();
        } elseif (auth()->user()) {
            // Для новых кандидатов проверяем залогиненного пользователя
            $this->gardner_test_completed = \App\Models\GardnerTestResult::where('user_id', auth()->id())->exists();
        } else {
            $this->gardner_test_completed = false;
        }
    }

    /**
     * Возвращает результаты теста Гарднера для кандидата
     */
    public function getGardnerTestResults()
    {
        // Если редактируем существующего кандидата, получаем его результаты
        if ($this->candidate && $this->candidate->user_id) {
            $result = \App\Models\GardnerTestResult::where('user_id', $this->candidate->user_id)->first();
            return $result ? $result->results : null;
        } elseif (auth()->user()) {
            // Для новых кандидатов используем залогиненного пользователя
            $result = \App\Models\GardnerTestResult::where('user_id', auth()->id())->first();
            return $result ? $result->results : null;
        }
        return null;
    }

    protected function loadCandidateData()
    {
        // Basic Information
        $this->full_name = $this->candidate->full_name;

        // Разделяем ФИО на части (сначала Имя, потом Фамилия)
        if ($this->full_name) {
            $nameParts = explode(' ', $this->full_name);
            $this->first_name = $nameParts[0] ?? '';
            $this->last_name = $nameParts[1] ?? '';
        }

        $this->email = $this->candidate->email;
        $this->phone = $this->candidate->phone;
        $this->gender = $this->candidate->gender;
        $this->marital_status = $this->candidate->marital_status;
        $this->birth_date = $this->candidate->birth_date?->format('Y-m-d');
        $this->birth_place = $this->candidate->birth_place;
        $this->current_city = $this->candidate->current_city;
        $this->ready_to_relocate = $this->candidate->ready_to_relocate;
        $this->instagram = $this->candidate->instagram;

        // Загружаем фото и создаем предпросмотр
        if ($this->candidate->photo) {
            $this->photo = $this->candidate->photo;
            $this->photoPreview = Storage::disk('public')->exists($this->photo)
                ? Storage::disk('public')->url($this->photo)
                : null;
        }

        // Additional Information
        $this->religion = $this->convertReligionToRussian($this->candidate->religion);
        logger()->debug('Loading candidate religion:', ['original' => $this->candidate->religion, 'converted' => $this->religion]);
        $this->is_practicing = $this->candidate->is_practicing !== null ? (int) $this->candidate->is_practicing : null;
        $this->family_members = $this->candidate->family_members ?? [];

        // Инициализируем новые категории семьи
        $this->loadFamilyCategories();

        $this->hobbies = $this->candidate->hobbies;
        $this->interests = $this->candidate->interests;
        $this->visited_countries = $this->candidate->visited_countries ?? [];
        $this->books_per_year = $this->candidate->books_per_year;
        // Парсим диапазон книг для двойного слайдера
        $this->parseBooksPerYear($this->candidate->books_per_year);
        // Обновляем books_per_year на основе диапазона
        $this->updateBooksPerYear();
        $this->favorite_sports = $this->candidate->favorite_sports ?? [];
        $this->entertainment_hours_weekly = $this->candidate->entertainment_hours_weekly;
        $this->educational_hours_weekly = $this->candidate->educational_hours_weekly;
        $this->social_media_hours_weekly = $this->candidate->social_media_hours_weekly;
        $this->has_driving_license = $this->candidate->has_driving_license !== null ? (int) $this->candidate->has_driving_license : null;

        // Education and Work
        // Разбираем поле school на три части
        if ($this->candidate->school) {
            $schoolParts = array_map('trim', explode('/', $this->candidate->school));
            $this->school_name = $schoolParts[0] ?? '';
            $this->school_city = $schoolParts[1] ?? '';
            $this->school_graduation_year = $schoolParts[2] ?? '';
        }
        // Нормализуем данные университетов для обратной совместимости
        $this->universities = collect($this->candidate->universities ?? [])->map(function ($uni) {
            return [
                'name' => $uni['name'] ?? '',
                'city' => $uni['city'] ?? '',
                'graduation_year' => $uni['graduation_year'] ?? '',
                'speciality' => $uni['speciality'] ?? '',
                'degree' => $uni['degree'] ?? '',
                'gpa' => $uni['gpa'] ?? '',
            ];
        })->toArray();
        $this->language_skills = $this->candidate->language_skills ?? [];
        $this->computer_skills = $this->candidate->computer_skills ?? '';
        $this->work_experience = $this->convertWorkExperienceFormat($this->candidate->work_experience ?? []);
        logger()->debug('Work experience loaded:', ['original' => $this->candidate->work_experience, 'converted' => $this->work_experience]);
        $this->total_experience_years = $this->candidate->total_experience_years;
        $this->job_satisfaction = $this->candidate->job_satisfaction;

        // Загружаем желаемые должности - если есть массив, используем его, иначе создаем из строки
        if (is_array($this->candidate->desired_positions) && !empty($this->candidate->desired_positions)) {
            $this->desired_positions = $this->candidate->desired_positions;
        } elseif (!empty($this->candidate->desired_position)) {
            // Обратная совместимость - преобразуем строку в массив
            $this->desired_positions = [$this->candidate->desired_position];
        } else {
            $this->desired_positions = [''];
        }

        $this->activity_sphere = $this->candidate->activity_sphere;
        $this->awards = $this->candidate->awards ?? [];
        $this->expected_salary = $this->candidate->expected_salary;
        $this->expected_salary_from = $this->candidate->expected_salary_from;
        $this->expected_salary_to = $this->candidate->expected_salary_to;
        $this->employer_requirements = $this->candidate->employer_requirements;

        // Tests
        if ($this->candidate->gallup_pdf) {
            $this->gallup_pdf = $this->candidate->gallup_pdf;
        }
        $this->mbti_type = $this->candidate->mbti_type;
    }

    protected function rules()
    {
        $rules = [
        // Step 1 validation rules
            'last_name' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
        'email' => 'required|email|max:255',
            // Универсальный международный формат: допускает +, цифры, пробелы, дефисы и скобки; 8-15 цифр всего
            'phone' => ['required', 'string', 'regex:/^(?=(?:.*\d){8,15})\+?[\d\s\-\(\)]{7,20}$/'],
            'gender' => 'required|in:Мужской,Женский',
            'marital_status' => 'required|in:Холост/Не замужем,Женат/Замужем,Разведен(а),Вдовец/Вдова',
            'birth_date' => 'required|date|before:today',
            'birth_place' => ['required', 'string', 'max:255'],
            'current_city' => ['required', 'string', 'max:255'],
            'ready_to_relocate' => 'nullable|boolean',
            'instagram' => 'nullable|string|max:255',
            'photo' => !$this->candidate?->photo ? 'required|image|max:20480' : 'nullable|image|max:20480',

            // Step 2 validation rules
            'religion' => 'required|string|in:' . implode(',', array_values(config('lists.religions'))),
            'is_practicing' => 'required|boolean',
            // Отключаем валидацию старой структуры family_members
            // 'family_members' => 'nullable|array|min:0',
            // 'family_members.*.type' => 'required|string|in:Отец,Мать,Брат,Сестра,Жена,Муж,Сын,Дочь',
            // 'family_members.*.birth_year' => 'required|integer|min:1900|max:' . date('Y'),
            // 'family_members.*.profession' => ['required', 'string', 'max:255'],

            // Новые правила валидации для категорий семьи
            'parents' => 'required|array|min:1|max:2',
            'parents.*.relation' => 'required|string|in:Отец,Мать',
            'parents.*.birth_year' => 'required|integer|min:1900|max:' . date('Y'),
            'parents.*.profession' => 'required|string|max:255',
            'siblings' => 'sometimes|array',
            'siblings.*.relation' => 'required|string|in:Брат,Сестра',
            'siblings.*.birth_year' => 'required|integer|min:1900|max:' . date('Y'),
            'children' => 'sometimes|array',
            'children.*.gender' => 'required_with:children|string|in:М,Ж',
            'children.*.birth_year' => 'required_with:children|integer|min:1900|max:' . date('Y'),
            'hobbies' => ['required', 'string', 'max:1000'],
            'interests' => ['required', 'string', 'max:1000'],
            'visited_countries' => 'required|array|min:1',
            'visited_countries.*' => 'required|string|in:' . implode(',', collect($this->countries)->pluck('name_ru')->all()),
            'books_per_year_min' => 'required|integer|min:0|max:100',
            'books_per_year_max' => 'required|integer|min:0|max:100|gte:books_per_year_min',
            'favorite_sports' => ['required', 'string', 'max:1000'],
            'entertainment_hours_weekly' => 'required|integer|min:0|max:168',
            'educational_hours_weekly' => 'required|integer|min:0|max:168',
            'social_media_hours_weekly' => 'required|integer|min:0|max:168',
            'has_driving_license' => 'required|boolean',

            // Step 3 validation rules
            'school_name' => ['required', 'string', 'max:255'],
            'school_city' => ['required', 'string', 'max:255'],
            'school_graduation_year' => 'required|integer|min:1970|max:2035',
            'universities' => 'nullable|array|min:0',
            'universities.*.name' => 'required|string|max:255',
            'universities.*.city' => 'nullable|string|max:255',
            'universities.*.graduation_year' => 'required|integer|min:1950',
            'universities.*.speciality' => 'required|string|max:255',
            'universities.*.degree' => 'nullable|in:Средне-специальное,Бакалавр,Магистратура,PhD',
            'universities.*.gpa' => 'nullable|numeric|min:0|max:4',
            'language_skills' => 'required|array|min:1',
            'language_skills.*.language' => 'required|string|max:255',
            'language_skills.*.level' => 'required|in:Начальный,Средний,Выше среднего,Продвинутый,В совершенстве',
            'computer_skills' => 'required|string',
            'work_experience' => 'nullable|array|min:0',
            'work_experience.*.years' => 'required|string|max:255',
            'work_experience.*.company' => 'required|string|max:255',
            'work_experience.*.city' => 'required|string|max:255',
            'work_experience.*.position' => 'required|string|max:255',
            'work_experience.*.start_month' => 'nullable|integer|min:0|max:11',
            'work_experience.*.start_year' => 'nullable|integer|min:1990|max:2025',
            'work_experience.*.end_month' => 'nullable|integer|min:0|max:11',
            'work_experience.*.end_year' => 'nullable|integer|min:1990|max:2025',
            'work_experience.*.start_period' => 'nullable|integer|min:0|max:420',
            'work_experience.*.end_period' => 'nullable|integer|min:0|max:420',
            'work_experience.*.is_current' => 'nullable|boolean',
            'work_experience.*.activity_sphere' => 'nullable|string|max:255',
            'work_experience.*.main_tasks' => 'nullable|array|min:3|max:8',
            'work_experience.*.main_tasks.*' => 'nullable|string|max:500',
            'total_experience_years' => 'required|integer|min:0',
            'job_satisfaction' => 'required|integer|min:1|max:5',
            'desired_positions' => ['required', 'array', 'min:1', 'max:3'],
            'desired_positions.*' => ['required', 'string', 'max:255'],
            'activity_sphere' => ['nullable', 'string', 'max:255'],
            'awards' => 'nullable|array',
            'awards.*' => 'nullable|string|max:500',
            'expected_salary' => 'nullable|numeric|min:0|max:999999999999',
            'expected_salary_from' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999999',
                function ($attribute, $value, $fail) {
                    if ($this->birth_date) {
                        $birthYear = (int) date('Y', strtotime($this->birth_date));
                        if ($birthYear >= 2000 && $value > 2000000) {
                            $fail(__("For candidates born after 2000, maximum salary 'From' is 2,000,000 tenge"));
                        }
                    }
                }
            ],
            'expected_salary_to' => [
                'required',
                'numeric',
                'min:0',
                'max:999999999999',
                'gte:expected_salary_from',
                function ($attribute, $value, $fail) {
                    if ($this->birth_date) {
                        $birthYear = (int) date('Y', strtotime($this->birth_date));
                        if ($birthYear >= 2000 && $value > 2000000) {
                            $fail(__("For candidates born after 2000, maximum salary 'To' is 2,000,000 tenge"));
                        }
                    }
                }
            ],
            'employer_requirements' => ['required', 'string', 'max:2000'],

            // Step 4 validation rules
            // Gallup файл (PDF или изображение) - необязательный (рекомендуется)
            'gallup_pdf' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:10240',
            'mbti_type' => [
                Rule::when($this->currentStep === 4, ['required', 'string']),
                Rule::when($this->currentStep !== 4, ['nullable']),
            ],
        ];

        // Проверка прохождения теста Гарднера на шаге 4
        if ($this->currentStep === 4) {
            $rules['gardner_test_completed'] = [
                function ($attribute, $value, $fail) {
                    // Если редактируем кандидата, проверяем его user_id
                    if ($this->candidate && $this->candidate->user_id) {
                        $hasGardnerResult = \App\Models\GardnerTestResult::where('user_id', $this->candidate->user_id)->exists();
                    } else {
                        // Для нового кандидата проверяем текущего пользователя
                        $hasGardnerResult = \App\Models\GardnerTestResult::where('user_id', auth()->id())->exists();
                    }

                    if (!$hasGardnerResult) {
                        $fail(__('You must complete the Gardner test to continue.'));
                    }
                }
            ];
        }

        return $rules;
    }

    protected function messages()
    {
        return [
            'last_name.required' => __('Last name is required'),
            'last_name.max' => __('Last name must not exceed 255 characters'),
            'first_name.required' => __('First name is required'),
            'first_name.max' => __('First name must not exceed 255 characters'),
            'birth_place.required' => __('Place of birth is required'),
            'current_city.required' => __('Enter current city'),
            'email.required' => __('Email is required'),
            'email.email' => __('Enter a valid email address'),
            'phone.required' => __('Phone is required'),
            'phone.regex' => __('Enter a valid phone number (+, digits, spaces, -, () allowed)'),
            'gender.required' => __('Select gender'),
            'marital_status.required' => __('Select marital status'),
            'birth_date.required' => __('Date of birth is required'),
            'birth_date.before' => __('Date of birth must be before today'),
            'photo.required' => __('Photo is required'),
            'photo.image' => __('Uploaded file must be an image (jpg, jpeg, png)'),
            'photo.max' => __('Image size must not exceed 20MB'),
            'gallup_pdf.required' => __('Gallup test results required'),
            'gallup_pdf.file' => __('File upload required'),
            'gallup_pdf.mimes' => __('File must be PDF or image (JPG, PNG, WebP)'),
            'gallup_pdf.max' => __('File size must not exceed 10MB'),
            'mbti_type.required' => __('MBTI personality type selection required'),
            'mbti_type.in' => __('Invalid MBTI personality type selected'),
            'expected_salary.required' => __('Expected salary is required'),
            'expected_salary.numeric' => __('Expected salary must be a number'),
            'expected_salary.min' => __('Expected salary must be greater than 0'),
            'expected_salary.max' => __('Expected salary cannot exceed 999,999,999,999'),
            'expected_salary_from.required' => __('Salary from is required'),
            'expected_salary_from.numeric' => __('Salary from must be a number'),
            'expected_salary_from.min' => __('Salary from must be greater than 0'),
            'expected_salary_to.required' => __('Salary to is required'),
            'expected_salary_to.numeric' => __('Salary to must be a number'),
            'expected_salary_to.min' => __('Salary to must be greater than 0'),
            'expected_salary_to.gte' => __('Salary to must be greater than or equal to salary from'),
            'desired_positions.required' => __('Desired position is required'),
            'desired_positions.min' => __('Add at least one desired position'),
            'desired_positions.max' => __('Maximum 3 desired positions'),
            'desired_positions.*.required' => __('Desired position is required'),
            'desired_positions.*.max' => __('Desired position must not exceed 255 characters'),
            'activity_sphere.required' => __('Field of activity is required'),
            'activity_sphere.max' => __('Field of activity must not exceed 255 characters'),
            'awards.*.max' => __('Award must not exceed 500 characters'),
            'work_experience.*.main_tasks.min' => __('Add at least 3 main tasks'),
            'work_experience.*.main_tasks.*.max' => __('Task must not exceed 500 characters'),
            'instagram.max' => __('Instagram must not exceed 255 characters'),

            // Дополнительные сообщения для обязательных полей
            'hobbies.required' => __('Hobbies are required'),
            'interests.required' => __('Interests are required'),
            'favorite_sports.required' => __('Favorite sports are required'),
            'books_per_year_min.required' => __('Minimum books per year is required'),
            'books_per_year_max.required' => __('Maximum books per year is required'),
            'books_per_year_max.gte' => __('Maximum books cannot be less than minimum'),
            'is_practicing.required' => __('Please indicate if you are practicing'),
            'visited_countries.required' => __('Add at least one country'),
            'visited_countries.min' => __('Add at least one country'),
            'visited_countries.*.required' => __('Select a country from the list'),
            'visited_countries.*.in' => __('Select a country from the list'),
            'family_members.required' => __('Add at least one family member'),
            'family_members.min' => __('Add at least one family member'),
            'parents.required' => __('Add at least one parent'),
            'parents.min' => __('Add at least one parent'),
            'parents.max' => __('Maximum two parents allowed'),
            'parents.*.relation.required' => __('Specify relationship'),
            'parents.*.birth_year.required' => __('Specify parent\'s birth year'),
            'parents.*.profession.required' => __('Specify parent\'s profession'),
            'siblings.required' => __('Siblings field is required (can be empty if none)'),
            'siblings.*.relation.required' => __('Specify relationship'),
            'siblings.*.birth_year.required' => __('Specify birth year'),
            'computer_skills.required' => __('Specify computer skills'),
            'universities.required' => __('Add at least one university'),
            'language_skills.required' => __('Add at least one language'),
            'work_experience.required' => __('Add at least one work experience'),
            'job_satisfaction.required' => __('Specify job satisfaction level'),
            'employer_requirements.required' => __('Specify workplace requirements'),
        ];
    }

    protected function validationAttributes()
    {
        return [
            // Step 1
            'last_name' => __('Last Name'),
            'first_name' => __('First Name'),
            'email' => __('Email'),
            'phone' => __('Phone'),
            'gender' => __('Gender'),
            'marital_status' => __('Marital Status'),
            'birth_date' => __('Date of Birth'),
            'birth_place' => __('Place of Birth'),
            'current_city' => __('Current City'),
            'ready_to_relocate' => __('Ready to Relocate'),
            'instagram' => __('Instagram'),
            'photo' => __('Photo'),

            // Step 2
            'has_driving_license' => __('Driving License'),
            'religion' => __('Religion'),
            'is_practicing' => __('Practicing'),
            'family_members' => __('Family Members'),
            'family_members.*.type' => __('Relationship Type'),
            'family_members.*.birth_year' => __('Birth Year'),
            'family_members.*.profession' => __('Profession'),

            // New family category attributes
            'parents' => __('Parents'),
            'parents.*.relation' => __('Relationship'),
            'parents.*.birth_year' => __('Birth Year'),
            'parents.*.profession' => __('Profession'),

            'siblings' => __('Siblings'),
            'siblings.*.relation' => __('Relationship'),
            'siblings.*.birth_year' => __('Birth Year'),

            'children' => __('Children'),
            'children.*.gender' => __('Child Gender'),
            'children.*.birth_year' => __('Birth Year'),
            'hobbies' => __('Hobbies'),
            'interests' => __('Interests'),
            'visited_countries' => __('Visited Countries'),
            'visited_countries.*' => __('Country'),
            'books_per_year_min' => __('Minimum Books per Year'),
            'books_per_year_max' => __('Maximum Books per Year'),
            'favorite_sports' => __('Favorite Sports'),
            'entertainment_hours_weekly' => __('Entertainment Hours per Week'),
            'educational_hours_weekly' => __('Educational Hours per Week'),
            'social_media_hours_weekly' => __('Social Media Hours per Week'),

            // Step 3
            'school_name' => __('School Name'),
            'school_city' => __('City (School)'),
            'school_graduation_year' => __('Graduation Year (School)'),
            'universities' => __('Universities'),
            'universities.*.name' => __('University Name'),
            'universities.*.graduation_year' => __('Graduation Year'),
            'universities.*.speciality' => __('Specialization'),
            'universities.*.gpa' => __('GPA'),
            'language_skills' => __('Language Skills'),
            'language_skills.*.language' => __('Language'),
            'language_skills.*.level' => __('Level'),
            'computer_skills' => __('Computer Skills'),
            'work_experience' => __('Work Experience'),
            'work_experience.*.years' => __('Period'),
            'work_experience.*.company' => __('Company Name'),
            'work_experience.*.city' => __('City'),
            'work_experience.*.position' => __('Position'),
            'work_experience.*.start_month' => __('Start Month'),
            'work_experience.*.start_year' => __('Start Year'),
            'work_experience.*.end_month' => __('End Month'),
            'work_experience.*.end_year' => __('End Year'),
            'work_experience.*.activity_sphere' => __('Field of Activity'),
            'work_experience.*.main_tasks' => __('Main Tasks'),
            'work_experience.*.main_tasks.*' => __('Task'),
            'total_experience_years' => __('Total Work Experience'),
            'job_satisfaction' => __('Job Satisfaction'),
            'desired_positions' => __('Desired Position'),
            'desired_positions.*' => __('Desired Position'),
            'activity_sphere' => __('Field of Activity'),
            'awards' => __('Awards and Achievements'),
            'awards.*' => __('Award'),
            'expected_salary' => __('Expected Salary'),
            'expected_salary_from' => __('Salary From'),
            'expected_salary_to' => __('Salary To'),
            'employer_requirements' => __('Workplace Requirements'),

            // Step 4
            'gallup_pdf' => __('Gallup PDF'),
            'mbti_type' => __('MBTI Personality Type'),
        ];
    }

    public function updated($propertyName)
    {
        // Проверяем, относится ли поле к текущему шагу
        if (!$this->isFieldInCurrentStep($propertyName)) {
            // Если поле не относится к текущему шагу, только очищаем его ошибки
            // но не прерываем выполнение - может быть это системное поле Livewire
            if ($this->getErrorBag()->has($propertyName)) {
                $this->resetErrorBag($propertyName);
            }

            // Если это не системное поле Livewire, прерываем выполнение
            if (!str_starts_with($propertyName, '_') && !in_array($propertyName, ['currentStep', 'totalSteps'])) {
                return;
            }
        }

        // Пропускаем валидацию для пустых полей при первой загрузке
        // Это предотвращает появление ошибок валидации сразу при открытии страницы
        if ($this->isFirstLoad && empty($this->{$propertyName}) && in_array($propertyName, ['phone', 'last_name', 'first_name', 'birth_date', 'birth_place', 'current_city'])) {
            // Если это первая загрузка и поле пустое, не валидируем его
            // Пользователь еще не начал вводить данные
            return;
        }

        // После первого взаимодействия с полем, сбрасываем флаг первой загрузки
        if ($this->isFirstLoad && !empty($this->{$propertyName})) {
            $this->isFirstLoad = false;
        }

        // Если обновляется поле университета
        if (strpos($propertyName, 'universities.') === 0) {
            $this->validateOnly($propertyName);
            return;
        }

        // Если обновляется поле члена семьи
        if (strpos($propertyName, 'family_members.') === 0) {
            $this->validateOnly($propertyName);
            return;
        }

        // Если обновляется поле новых категорий семьи - отключаем live валидацию
        if (strpos($propertyName, 'parents.') === 0 ||
            strpos($propertyName, 'siblings.') === 0 ||
            strpos($propertyName, 'children.') === 0) {
            // Логируем обновление данных семьи
            logger()->info('Family field updated', [
                'property' => $propertyName,
                'value' => $this->{explode('.', $propertyName)[0]} ?? 'not set',
                'parents_count' => count($this->parents),
                'siblings_count' => count($this->siblings),
                'children_count' => count($this->children)
            ]);
            // Просто сбрасываем ошибки для этого поля без валидации
            $this->resetErrorBag($propertyName);
            return;
        }

        // Если обновляется поле опыта работы
        if (strpos($propertyName, 'work_experience.') === 0) {
            // Извлекаем индекс из имени свойства
            if (preg_match('/work_experience\.(\d+)\./', $propertyName, $matches)) {
                $index = (int)$matches[1];
                // Проверяем, что элемент с таким индексом существует
                if (!isset($this->work_experience[$index])) {
                    logger()->warning('Attempted to access non-existent work experience index', [
                        'property' => $propertyName,
                        'index' => $index,
                        'work_experience_count' => count($this->work_experience)
                    ]);
                    return;
                }
                // Валидируем только если все обязательные поля заполнены
                $experience = $this->work_experience[$index];
                if (!empty($experience['years']) && !empty($experience['company']) &&
                    !empty($experience['city']) && !empty($experience['position'])) {
                    $this->validateOnly($propertyName);
                }
            }
            return;
        }

        // Если обновляется поле языка, проверяем только если оба поля заполнены
        if (strpos($propertyName, 'language_skills.') === 0) {
            // Валидируем языковые навыки без проверки списка языков
            $this->validateOnly($propertyName);
            return;
        }

        // Если обновляется поле desired_positions (массив желаемых должностей)
        if (strpos($propertyName, 'desired_positions.') === 0) {
            $this->resetErrorBag($propertyName);
            return;
        }

        // Если обновляется поле awards (массив наград)
        if (strpos($propertyName, 'awards.') === 0) {
            $this->resetErrorBag($propertyName);
            return;
        }

        // Валидируем только поля текущего шага
        $rules = collect($this->rules())->filter(function ($rule, $field) {
            return $this->isFieldInCurrentStep($field);
        })->toArray();



        $this->validateOnly($propertyName, $rules);

        // Сохраняем изменение в историю
        if ($this->candidate) {
            // Пропускаем запись для сложных массивных свойств
            if (strpos($propertyName, '.') !== false) {
                return;
            }

            $oldValue = $this->candidate->{$propertyName} ?? null;
            $newValue = $this->{$propertyName} ?? null;

            if ($oldValue !== $newValue) {
                CandidateHistory::create([
                    'candidate_id' => $this->candidate->id,
                    'field_name' => $propertyName,
                    'old_value' => is_array($oldValue) ? json_encode($oldValue) : $oldValue,
                    'new_value' => is_array($newValue) ? json_encode($newValue) : $newValue,
                    'changed_by' => auth()->user()?->name ?? 'Guest',
                    'ip_address' => request()->ip()
                ]);
            }
        }
    }

    protected function isFieldInCurrentStep($field)
    {
        // Извлекаем основное поле из составного имени (например, "universities.0.name" -> "universities")
        $baseField = explode('.', $field)[0];

        $step1Fields = ['last_name', 'first_name', 'email', 'phone', 'gender', 'marital_status', 'birth_date', 'birth_place', 'current_city', 'ready_to_relocate', 'instagram', 'photo'];
        $step2Fields = ['religion', 'is_practicing', 'family_members', 'parents', 'siblings', 'children', 'hobbies', 'interests', 'visited_countries', 'books_per_year_min', 'books_per_year_max', 'favorite_sports', 'entertainment_hours_weekly', 'educational_hours_weekly', 'social_media_hours_weekly', 'has_driving_license', 'newCountry'];
        $step3Fields = ['school_name', 'school_city', 'school_graduation_year', 'universities', 'language_skills', 'computer_skills', 'work_experience', 'total_experience_years', 'job_satisfaction', 'desired_positions', 'activity_sphere', 'awards', 'expected_salary', 'expected_salary_from', 'expected_salary_to', 'employer_requirements'];
        $step4Fields = ['gallup_pdf', 'mbti_type', 'gardner_test_completed'];

        return match($this->currentStep) {
            1 => in_array($baseField, $step1Fields),
            2 => in_array($baseField, $step2Fields),
            3 => in_array($baseField, $step3Fields),
            4 => in_array($baseField, $step4Fields),
            default => false,
        };
    }

    public function nextStep()
    {
        try {
            logger()->debug('Starting nextStep method');
            logger()->debug('Current step: ' . $this->currentStep);

            // Фильтруем пустые элементы семьи перед валидацией
            $this->filterEmptyFamilyElements();

            $rules = $this->getStepRules();

            logger()->debug('Validation rules for step ' . $this->currentStep . ':', $rules);
            logger()->debug('Current family data:', [
                'parents' => $this->parents,
                'siblings' => $this->siblings,
                'children' => $this->children
            ]);

            // Специальная обработка для фото на первом шаге
            if ($this->currentStep === 1) {
                // Если фото уже загружено в базу или есть предпросмотр, не требуем его
                if ($this->candidate?->photo || $this->photoPreview) {
                    unset($rules['photo']);
                }
            }

            logger()->debug('Validation rules:', $rules);

            $this->validate($rules);
            logger()->debug('Validation passed');

        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
                logger()->debug('New step: ' . $this->currentStep);

                // Переинициализируем валидацию для нового шага
                logger()->debug('About to call reinitializeValidation()');
                $this->reinitializeValidation();
                logger()->debug('reinitializeValidation() completed');

                $this->saveProgress();
                logger()->debug('Progress saved');
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            logger()->debug('Validation errors:', $e->errors());
            throw $e;
        } catch (\Exception $e) {
            logger()->error('Unexpected error in nextStep:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    public function previousStep()
    {
        try {
            logger()->debug('Starting previousStep method');
            logger()->debug('Current step: ' . $this->currentStep);

        if ($this->currentStep > 1) {
            $this->currentStep--;
                logger()->debug('New step: ' . $this->currentStep);

                // Переинициализируем валидацию для нового шага
                logger()->debug('About to call reinitializeValidation()');
                $this->reinitializeValidation();
                logger()->debug('reinitializeValidation() completed');

                $this->saveProgress();
                logger()->debug('Progress saved');
            }
        } catch (\Exception $e) {
            logger()->error('Error in previousStep:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    /**
     * Переход к конкретному шагу с сохранением прогресса
     * Используется при клике на номер шага в навигации
     */
    public function goToStep($step)
    {
        try {
            logger()->debug('Starting goToStep method', ['targetStep' => $step, 'currentStep' => $this->currentStep]);

            // Валидация номера шага
            if ($step < 1 || $step > $this->totalSteps) {
                return;
            }

            // Если уже на этом шаге, ничего не делаем
            if ($step === $this->currentStep) {
                return;
            }

            // Сохраняем текущий прогресс перед сменой шага
            $this->saveProgress();
            logger()->debug('Progress saved before step change');

            // Меняем шаг
            $this->currentStep = $step;
            logger()->debug('Step changed to: ' . $this->currentStep);

            // Переинициализируем валидацию для нового шага
            $this->reinitializeValidation();
            logger()->debug('Validation reinitialized for new step');

        } catch (\Exception $e) {
            logger()->error('Error in goToStep:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    protected function getStepRules()
    {
        $allRules = $this->rules();

        return match($this->currentStep) {
            1 => [
                'last_name' => $allRules['last_name'],
                'first_name' => $allRules['first_name'],
                'email' => $allRules['email'],
                'phone' => $allRules['phone'],
                'gender' => $allRules['gender'],
                'marital_status' => $allRules['marital_status'],
                'birth_date' => $allRules['birth_date'],
                'birth_place' => $allRules['birth_place'],
                'current_city' => $allRules['current_city'],
                'photo' => !$this->candidate?->photo ? 'required|image|max:20480' : 'nullable|image|max:20480',
            ],
            2 => [
                'religion' => $allRules['religion'],
                'is_practicing' => $allRules['is_practicing'],
                // Убираем валидацию старой структуры family_members

                // Добавляем новые правила валидации для категорий семьи
                'parents' => $allRules['parents'],
                'siblings' => $allRules['siblings'],
                'children' => $allRules['children'],

                'hobbies' => $allRules['hobbies'],
                'interests' => $allRules['interests'],
                'visited_countries' => $allRules['visited_countries'],
                'visited_countries.*' => $allRules['visited_countries.*'],
                'books_per_year_min' => $allRules['books_per_year_min'],
                'books_per_year_max' => $allRules['books_per_year_max'],
                'favorite_sports' => $allRules['favorite_sports'],
                'entertainment_hours_weekly' => $allRules['entertainment_hours_weekly'],
                'educational_hours_weekly' => $allRules['educational_hours_weekly'],
                'social_media_hours_weekly' => $allRules['social_media_hours_weekly'],
                'has_driving_license' => $allRules['has_driving_license'],
            ],
            3 => [
                'school_name' => $allRules['school_name'],
                'school_city' => $allRules['school_city'],
                'school_graduation_year' => $allRules['school_graduation_year'],
                'universities' => $allRules['universities'],
                'universities.*.name' => $allRules['universities.*.name'],
                'universities.*.graduation_year' => $allRules['universities.*.graduation_year'],
                'universities.*.speciality' => $allRules['universities.*.speciality'],
                'universities.*.gpa' => $allRules['universities.*.gpa'],
                'language_skills' => $allRules['language_skills'],
                'language_skills.*.language' => $allRules['language_skills.*.language'],
                'language_skills.*.level' => $allRules['language_skills.*.level'],
                'computer_skills' => $allRules['computer_skills'],
                'work_experience' => $allRules['work_experience'],
                'work_experience.*.years' => $allRules['work_experience.*.years'],
                'work_experience.*.company' => $allRules['work_experience.*.company'],
                'work_experience.*.city' => $allRules['work_experience.*.city'],
                'work_experience.*.position' => $allRules['work_experience.*.position'],
                'work_experience.*.activity_sphere' => $allRules['work_experience.*.activity_sphere'],
                'work_experience.*.main_tasks' => $allRules['work_experience.*.main_tasks'],
                'work_experience.*.main_tasks.*' => $allRules['work_experience.*.main_tasks.*'],
                'total_experience_years' => $allRules['total_experience_years'],
                'job_satisfaction' => $allRules['job_satisfaction'],
                'desired_positions' => $allRules['desired_positions'],
                'desired_positions.*' => $allRules['desired_positions.*'],
                'awards' => $allRules['awards'],
                'awards.*' => $allRules['awards.*'],
                'expected_salary' => $allRules['expected_salary'],
                'employer_requirements' => $allRules['employer_requirements'],
            ],
            4 => [
                'gallup_pdf' => [
                    $this->currentStep === 4 && $this->candidate && $this->candidate->gallup_pdf ? 'nullable' : 'required',
                    'file',
                    'mimes:pdf,jpg,jpeg,png,webp',
                    'max:10240',
                    function ($attribute, $value, $fail) {
                        if ($value && !is_string($value) && !$this->isValidGallupFile($value)) {
                            $fail('Загруженный файл не является корректным отчетом Gallup.');
                        }
                    }
                ],
                'mbti_type' => 'required|string|in:INTJ-A,INTJ-T,INTP-A,INTP-T,ENTJ-A,ENTJ-T,ENTP-A,ENTP-T,INFJ-A,INFJ-T,INFP-A,INFP-T,ENFJ-A,ENFJ-T,ENFP-A,ENFP-T,ISTJ-A,ISTJ-T,ISFJ-A,ISFJ-T,ESTJ-A,ESTJ-T,ESFJ-A,ESFJ-T,ISTP-A,ISTP-T,ISFP-A,ISFP-T,ESTP-A,ESTP-T,ESFP-A,ESFP-T',
            ],
            default => [],
        };
    }

    /**
     * Возвращает номер шага для указанного поля
     */
    private function getFieldStep(string $field): ?int
    {
        $baseField = explode('.', $field)[0];

        $step1Fields = ['last_name', 'first_name', 'email', 'phone', 'gender', 'marital_status', 'birth_date', 'birth_place', 'current_city', 'ready_to_relocate', 'instagram', 'photo'];
        $step2Fields = ['religion', 'is_practicing', 'family_members', 'parents', 'siblings', 'children', 'hobbies', 'interests', 'visited_countries', 'books_per_year_min', 'books_per_year_max', 'favorite_sports', 'entertainment_hours_weekly', 'educational_hours_weekly', 'social_media_hours_weekly', 'has_driving_license', 'newCountry'];
        $step3Fields = ['school_name', 'school_city', 'school_graduation_year', 'universities', 'language_skills', 'computer_skills', 'work_experience', 'total_experience_years', 'job_satisfaction', 'desired_positions', 'activity_sphere', 'awards', 'expected_salary', 'expected_salary_from', 'expected_salary_to', 'employer_requirements'];
        $step4Fields = ['gallup_pdf', 'mbti_type', 'gardner_test_completed'];

        if (in_array($baseField, $step1Fields, true)) return 1;
        if (in_array($baseField, $step2Fields, true)) return 2;
        if (in_array($baseField, $step3Fields, true)) return 3;
        if (in_array($baseField, $step4Fields, true)) return 4;
        return null;
    }

    // Dynamic field methods
    public function addFamilyMember()
    {
        $this->family_members[] = [
            'type' => '',
            'birth_year' => '',
            'profession' => ''
        ];
    }

    // Новые методы для работы с категориями семьи
    public function addParent()
    {
        logger()->debug('addParent called', [
            'current_parents_count' => count($this->parents),
            'parents_before' => $this->parents
        ]);

        $this->parents[] = [
            'relation' => '',
            'birth_year' => '',
            'profession' => ''
        ];

        logger()->debug('addParent completed', [
            'new_parents_count' => count($this->parents),
            'parents_after' => $this->parents
        ]);
    }

    public function removeParent($index)
    {
        logger()->debug('removeParent called', [
            'index' => $index,
            'parents_before' => $this->parents,
            'count_before' => count($this->parents)
        ]);

        unset($this->parents[$index]);
        $this->parents = array_values($this->parents);

        logger()->debug('removeParent completed', [
            'parents_after' => $this->parents,
            'count_after' => count($this->parents)
        ]);
    }

    public function addSibling()
    {
        $this->siblings[] = [
            'relation' => '',
            'birth_year' => ''
        ];
    }

    public function removeSibling($index)
    {
        unset($this->siblings[$index]);
        $this->siblings = array_values($this->siblings);
    }

    public function addChild()
    {
        $this->children[] = [
            'gender' => '',
            'birth_year' => ''
        ];
    }

    public function removeChild($index)
    {
        unset($this->children[$index]);
        $this->children = array_values($this->children);
    }

    /**
     * Хук для отслеживания изменений в родителях
     */
    public function updatedParents()
    {
        logger()->info('updatedParents hook called', [
            'parents' => $this->parents
        ]);
    }

    /**
     * Хук для отслеживания изменений в братьях/сестрах
     */
    public function updatedSiblings()
    {
        logger()->info('updatedSiblings hook called', [
            'siblings' => $this->siblings
        ]);
    }

    /**
     * Хук для отслеживания изменений в детях
     */
    public function updatedChildren()
    {
        logger()->info('updatedChildren hook called', [
            'children' => $this->children
        ]);
    }

    /**
     * Хук для отслеживания изменений в опыте работы
     * Автоматически обновляет поле years при изменении дат
     * и капитализирует первую букву в полях company, city, position
     */
    public function updatedWorkExperience($value, $key)
    {
        // Проверяем, изменились ли поля дат
        if (preg_match('/^(\d+)\.(start_month|start_year|end_month|end_year|is_current)$/', $key, $matches)) {
            $index = (int)$matches[1];
            $this->updateWorkExperienceYears($index);
        }

        // Автокапитализация для company, city, position
        if (preg_match('/^(\d+)\.(company|city|position)$/', $key, $matches)) {
            $index = (int)$matches[1];
            $field = $matches[2];
            if (isset($this->work_experience[$index][$field]) && is_string($this->work_experience[$index][$field])) {
                $this->work_experience[$index][$field] = $this->mbUcfirst($this->work_experience[$index][$field]);
            }
        }
    }

    /**
     * Автокапитализация названия школы
     */
    public function updatedSchoolName($value)
    {
        if (is_string($value) && !empty($value)) {
            $this->school_name = $this->mbUcfirst($value);
        }
    }

    /**
     * Автокапитализация города школы
     */
    public function updatedSchoolCity($value)
    {
        if (is_string($value) && !empty($value)) {
            $this->school_city = $this->mbUcfirst($value);
        }
    }

    /**
     * Автокапитализация полей университета (name, speciality, city)
     */
    public function updatedUniversities($value, $key)
    {
        if (preg_match('/^(\d+)\.(name|speciality|city)$/', $key, $matches)) {
            $index = (int)$matches[1];
            $field = $matches[2];
            if (isset($this->universities[$index][$field]) && is_string($this->universities[$index][$field])) {
                $this->universities[$index][$field] = $this->mbUcfirst($this->universities[$index][$field]);
            }
        }
    }

    /**
     * Капитализация первой буквы строки с поддержкой Unicode
     */
    private function mbUcfirst(string $string): string
    {
        $string = trim($string);
        if ($string === '') {
            return '';
        }
        $firstChar = mb_substr($string, 0, 1, 'UTF-8');
        $rest = mb_substr($string, 1, null, 'UTF-8');
        return mb_strtoupper($firstChar, 'UTF-8') . $rest;
    }

    /**
     * Обновляет поле years для указанного опыта работы
     */
    private function updateWorkExperienceYears($index)
    {
        if (!isset($this->work_experience[$index])) {
            return;
        }

        $exp = $this->work_experience[$index];
        $months = [
            0 => 'Янв', 1 => 'Фев', 2 => 'Мар', 3 => 'Апр',
            4 => 'Май', 5 => 'Июн', 6 => 'Июл', 7 => 'Авг',
            8 => 'Сен', 9 => 'Окт', 10 => 'Ноя', 11 => 'Дек'
        ];

        $startMonth = $exp['start_month'] ?? null;
        $startYear = $exp['start_year'] ?? null;
        $endMonth = $exp['end_month'] ?? null;
        $endYear = $exp['end_year'] ?? null;
        $isCurrent = $exp['is_current'] ?? false;

        if ($startMonth !== null && $startMonth !== '' && $startYear) {
            $startStr = ($months[(int)$startMonth] ?? '') . ' ' . $startYear;

            if ($isCurrent) {
                $endStr = __('Present');
            } elseif ($endMonth !== null && $endMonth !== '' && $endYear) {
                $endStr = ($months[(int)$endMonth] ?? '') . ' ' . $endYear;
            } else {
                $endStr = '';
            }

            if ($endStr) {
                $this->work_experience[$index]['years'] = $startStr . ' — ' . $endStr;
            } else {
                $this->work_experience[$index]['years'] = $startStr;
            }
        }
    }

    /**
     * Фильтрует пустые элементы из массивов семьи перед валидацией
     */
    private function filterEmptyFamilyElements()
    {
        logger()->debug('FILTER START - Original data:', [
            'parents' => $this->parents,
            'siblings' => $this->siblings,
            'children' => $this->children,
            'family_members' => $this->family_members
        ]);

        // ВАЖНО: Очищаем старую структуру family_members чтобы избежать конфликтов валидации
        $this->family_members = [];

        // Фильтруем родителей - удаляем полностью пустые записи
        if (is_array($this->parents)) {
            $originalCount = count($this->parents);
            $this->parents = array_filter($this->parents, function($parent) {
                $hasData = !empty($parent['relation']) || !empty($parent['birth_year']) || !empty($parent['profession']);
                logger()->debug('Parent filter check:', ['parent' => $parent, 'hasData' => $hasData]);
                return $hasData;
            });
            $this->parents = array_values($this->parents); // Переиндексируем
            logger()->debug('Parents filtered: ' . $originalCount . ' -> ' . count($this->parents));
        }

        // Фильтруем братьев и сестер
        if (is_array($this->siblings)) {
            $originalCount = count($this->siblings);
            $this->siblings = array_filter($this->siblings, function($sibling) {
                $hasData = !empty($sibling['relation']) || !empty($sibling['birth_year']);
                logger()->debug('Sibling filter check:', ['sibling' => $sibling, 'hasData' => $hasData]);
                return $hasData;
            });
            $this->siblings = array_values($this->siblings);
            logger()->debug('Siblings filtered: ' . $originalCount . ' -> ' . count($this->siblings));
        }

        // Фильтруем детей
        if (is_array($this->children)) {
            $originalCount = count($this->children);
            $this->children = array_filter($this->children, function($child) {
                $hasData = !empty($child['name']) || !empty($child['birth_year']);
                logger()->debug('Child filter check:', ['child' => $child, 'hasData' => $hasData]);
                return $hasData;
            });
            $this->children = array_values($this->children);
            logger()->debug('Children filtered: ' . $originalCount . ' -> ' . count($this->children));
        }

        logger()->debug('FILTER END - Filtered data:', [
            'parents' => $this->parents,
            'siblings' => $this->siblings,
            'children' => $this->children,
            'family_members' => $this->family_members
        ]);
    }



    public function removeFamilyMember($index)
    {
        unset($this->family_members[$index]);
        $this->family_members = array_values($this->family_members);
    }

    /**
     * Загрузка категорий семьи из старых данных или инициализация новых структур
     */
    private function loadFamilyCategories()
    {
        // Проверяем, есть ли новые поля в JSON структуре
        $familyData = $this->candidate->family_members ?? [];

        if (is_array($familyData) && isset($familyData['parents'])) {
            // Новая структура - загружаем как есть
            $this->parents = $familyData['parents'] ?? [];
            $this->siblings = $familyData['siblings'] ?? [];
            $this->children = $familyData['children'] ?? [];
        } else {
            // Старая структура - мигрируем данные
            $this->parents = [];
            $this->siblings = [];
            $this->children = [];

            foreach ($familyData as $member) {
                if (!is_array($member)) continue;

                $type = $member['type'] ?? '';
                switch ($type) {
                    case 'Отец':
                    case 'Мать':
                        $this->parents[] = [
                            'relation' => $type,
                            'birth_year' => $member['birth_year'] ?? '',
                            'profession' => $member['profession'] ?? ''
                        ];
                        break;
                    case 'Брат':
                    case 'Сестра':
                        $this->siblings[] = [
                            'relation' => $type,
                            'birth_year' => $member['birth_year'] ?? '',
                        ];
                        break;
                    case 'Сын':
                    case 'Дочь':
                        $this->children[] = [
                            'name' => $member['profession'] ?? '', // В старой структуре имя было в поле profession
                            'birth_year' => $member['birth_year'] ?? '',
                        ];
                        break;
                }
            }
        }
    }

    /**
     * Строит новую структуру JSON для сохранения в БД
     */
    private function buildFamilyStructure()
    {
        $structure = [
            'parents' => $this->parents ?? [],
            'siblings' => $this->siblings ?? [],
            'children' => $this->children ?? []
        ];
        
        logger()->info('buildFamilyStructure called', [
            'input_parents' => $this->parents,
            'input_siblings' => $this->siblings,
            'input_children' => $this->children,
            'output_structure' => $structure
        ]);
        
        return $structure;
    }

    /**
     * Переопределяем метод validate для фильтрации семьи перед валидацией
     */
    public function validate($rules = null, $messages = [], $attributes = [])
    {
        // Фильтруем семью перед любой валидацией
        $this->filterEmptyFamilyElements();

        // Добавляем кастомную валидацию для семьи
        $this->validateFamilyData();

        // Вызываем родительский метод с оригинальными правилами
        return parent::validate($rules, $messages, $attributes);
    }

    /**
     * Модифицирует правила валидации для семьи, делая их обязательными только для заполненных элементов
     */
    private function modifyFamilyValidationRules($rules)
    {
        // Создаем новые правила для каждого заполненного элемента
        $modifiedRules = $rules;

        // Удаляем старые правила для массивов семьи
        unset($modifiedRules['parents.*.relation']);
        unset($modifiedRules['parents.*.birth_year']);
        unset($modifiedRules['parents.*.profession']);
        unset($modifiedRules['siblings.*.relation']);
        unset($modifiedRules['siblings.*.birth_year']);
        unset($modifiedRules['children.*.gender']);
        unset($modifiedRules['children.*.birth_year']);

        // Добавляем правила для каждого конкретного элемента
        foreach ($this->parents as $index => $parent) {
            $modifiedRules["parents.{$index}.relation"] = 'required|string|in:Отец,Мать';
            $modifiedRules["parents.{$index}.birth_year"] = 'required|integer|min:1900|max:' . date('Y');
            $modifiedRules["parents.{$index}.profession"] = 'required|string|max:255';
        }

        foreach ($this->siblings as $index => $sibling) {
            $modifiedRules["siblings.{$index}.relation"] = 'required|string|in:Брат,Сестра';
            $modifiedRules["siblings.{$index}.birth_year"] = 'required|integer|min:1900|max:' . date('Y');
        }

        foreach ($this->children as $index => $child) {
            $modifiedRules["children.{$index}.gender"] = 'required|string|in:М,Ж';
            $modifiedRules["children.{$index}.birth_year"] = 'required|integer|min:1900|max:' . date('Y');
        }

        return $modifiedRules;
    }

    /**
     * Кастомная валидация данных семьи
     */
    private function validateFamilyData()
    {
        logger()->debug('CUSTOM VALIDATION - Family data:', [
            'parents' => $this->parents,
            'siblings' => $this->siblings,
            'children' => $this->children
        ]);

        $errors = [];

        // Проверяем, что добавлен хотя бы один родитель
        if (empty($this->parents) || count($this->parents) === 0) {
            $errors['parents'] = 'Добавьте минимум одного родителя';
        }

        // Валидируем родителей
        foreach ($this->parents as $index => $parent) {
            if (empty($parent['relation'])) {
                $errors["parents.{$index}.relation"] = 'Поле Родство обязательно.';
            } elseif (!in_array($parent['relation'], ['Отец', 'Мать'])) {
                $errors["parents.{$index}.relation"] = 'Выберите корректное родство.';
            }

            if (empty($parent['birth_year'])) {
                $errors["parents.{$index}.birth_year"] = 'Поле Год рождения обязательно.';
            } elseif (!is_numeric($parent['birth_year']) || $parent['birth_year'] < 1900 || $parent['birth_year'] > date('Y')) {
                $errors["parents.{$index}.birth_year"] = 'Введите корректный год рождения.';
            }

            if (empty($parent['profession'])) {
                $errors["parents.{$index}.profession"] = 'Поле Профессия обязательно.';
            }
        }

        // Валидируем братьев и сестер
        foreach ($this->siblings as $index => $sibling) {
            if (empty($sibling['relation'])) {
                $errors["siblings.{$index}.relation"] = 'Поле Родство обязательно.';
            } elseif (!in_array($sibling['relation'], ['Брат', 'Сестра'])) {
                $errors["siblings.{$index}.relation"] = 'Выберите корректное родство.';
            }

            if (empty($sibling['birth_year'])) {
                $errors["siblings.{$index}.birth_year"] = 'Поле Год рождения обязательно.';
            } elseif (!is_numeric($sibling['birth_year']) || $sibling['birth_year'] < 1900 || $sibling['birth_year'] > date('Y')) {
                $errors["siblings.{$index}.birth_year"] = 'Введите корректный год рождения.';
            }
        }

        // Валидируем детей
        foreach ($this->children as $index => $child) {
            if (empty($child['gender'])) {
                $errors["children.{$index}.gender"] = 'Поле Пол ребенка обязательно.';
            }

            if (empty($child['birth_year'])) {
                $errors["children.{$index}.birth_year"] = 'Поле Год рождения обязательно.';
            } elseif (!is_numeric($child['birth_year']) || $child['birth_year'] < 1900 || $child['birth_year'] > date('Y')) {
                $errors["children.{$index}.birth_year"] = 'Введите корректный год рождения.';
            }
        }

        // Если есть ошибки, выбрасываем исключение валидации
        if (!empty($errors)) {
            throw \Illuminate\Validation\ValidationException::withMessages($errors);
        }
    }

    /**
     * Обеспечивает наличие обязательных языков в списке
     */
    private function ensureRequiredLanguages($existingLanguages)
    {
        $requiredLanguages = ['Казахский', 'Русский', 'Английский'];
        $result = [];

        // Сначала добавляем обязательные языки
        foreach ($requiredLanguages as $lang) {
            $found = false;
            foreach ($existingLanguages as $existing) {
                if (($existing['language'] ?? '') === $lang) {
                    $result[] = $existing;
                    $found = true;
                    break;
                }
            }
            // Если обязательный язык не найден, добавляем его с пустым уровнем
            if (!$found) {
                $result[] = ['language' => $lang, 'level' => ''];
            }
        }

        // Затем добавляем остальные языки
        foreach ($existingLanguages as $existing) {
            if (!in_array($existing['language'] ?? '', $requiredLanguages)) {
                $result[] = $existing;
            }
        }

        return $result;
    }

    /**
     * Кастомная валидация языковых навыков
     * Проверяет наличие обязательных языков: Казахский, Русский, Английский
     */
    private function validateLanguageSkills()
    {
        // Проверяем только если есть языковые навыки
        if (empty($this->language_skills) || !is_array($this->language_skills)) {
            return;
        }

        $requiredLanguages = ['Казахский', 'Русский', 'Английский'];

        // Проверяем, что у каждого обязательного языка указан уровень
        $errors = [];
        foreach ($this->language_skills as $index => $skill) {
            if (in_array($skill['language'] ?? '', $requiredLanguages)) {
                if (empty($skill['level'])) {
                    $language = $skill['language'];
                    $errors["language_skills.{$index}.level"] = "Укажите уровень владения языком \"{$language}\"";
                }
            }
        }

        if (!empty($errors)) {
            throw \Illuminate\Validation\ValidationException::withMessages($errors);
        }
    }

    /**
     * Переопределяем метод validateOnly для фильтрации семьи перед валидацией
     */
    public function validateOnly($field, $rules = null, $messages = [], $attributes = [], $dataOverrides = [])
    {
        // Если валидируем поля семьи, сначала фильтруем
        if (strpos($field, 'parents.') === 0 ||
            strpos($field, 'siblings.') === 0 ||
            strpos($field, 'children.') === 0) {
            $this->filterEmptyFamilyElements();

            // Если правила не переданы, получаем правила и модифицируем их
            if ($rules === null) {
                $allRules = $this->rules();
                $rules = $this->modifyFamilyValidationRules($allRules);
            }
        }

        // Вызываем родительский метод
        return parent::validateOnly($field, $rules, $messages, $attributes, $dataOverrides);
    }

    public function updatedNewCountry($value)
    {
        try {
            logger()->debug('Updating new country:', [
                'value' => $value,
                'countries_count' => count($this->countries),
                'first_country' => $this->countries[0] ?? null
            ]);

            if ($value) {
                $country = collect($this->countries)->firstWhere('name_ru', $value);
                logger()->debug('Found country:', [
                    'country' => $country,
                    'has_flag_url' => isset($country['flag_url']),
                    'flag_url_value' => $country['flag_url'] ?? null
                ]);

                if ($country && !in_array($value, $this->visited_countries)) {
                    $this->visited_countries[] = $value;
                    $this->newCountry = '';
                    logger()->debug('Added country to visited_countries:', [
                        'visited_countries' => $this->visited_countries,
                        'last_added' => end($this->visited_countries)
                    ]);
                }
            }
        } catch (\Exception $e) {
            logger()->error('Error in updatedNewCountry:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    public function removeCountry($countryOrIndex)
    {
        // Если передано число, считаем что это индекс (для обратной совместимости)
        if (is_numeric($countryOrIndex)) {
            unset($this->visited_countries[$countryOrIndex]);
        } else {
            // Иначе это название страны - удаляем по значению
            $this->visited_countries = array_values(
                array_filter($this->visited_countries, function($country) use ($countryOrIndex) {
                    return $country !== $countryOrIndex;
                })
            );
            return;
        }
        
        $this->visited_countries = array_values($this->visited_countries);
    }



    public function addUniversity()
    {
        $this->universities = collect($this->universities)->toArray();
        $this->universities[] = [
            'name' => '',
            'city' => '',
            'graduation_year' => '',
            'speciality' => '',
            'degree' => '',
            'gpa' => ''
        ];
    }

    public function removeUniversity($index)
    {
        $universities = collect($this->universities)->toArray();
        unset($universities[$index]);
        $this->universities = array_values($universities);
    }

    public function addLanguage()
    {
        // Обеспечиваем корректную инициализацию языков
        if (empty($this->languages)) {
            $this->loadLanguages();
        }

        // Добавляем новый язык с безопасными значениями по умолчанию
        $firstLanguage = !empty($this->languages) ? $this->languages[0] : 'Русский';

        $this->language_skills[] = [
            'language' => $firstLanguage,
            'level' => 'Начальный'
        ];
    }

    private function loadLanguages()
    {
        try {
            $jsonPath = base_path('resources/json/languages.json');
            if (!file_exists($jsonPath)) {
                throw new \Exception('Languages JSON file not found');
            }

            $languagesData = json_decode(file_get_contents($jsonPath), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Invalid JSON in languages file: ' . json_last_error_msg());
            }

            $this->languages = [];
            $locale = app()->getLocale();
            $nameField = $locale === 'en' ? 'name_en' : 'name_ru';

            if (isset($languagesData['languages']) && is_array($languagesData['languages'])) {
                foreach ($languagesData['languages'] as $language) {
                    if (isset($language[$nameField]) && !empty($language[$nameField])) {
                        $this->languages[] = $language[$nameField];
                    }
                }
            }

            // Если массив языков пустой, используем fallback
            if (empty($this->languages)) {
                if ($locale === 'en') {
                    $this->languages = ['Russian', 'English', 'Spanish', 'French', 'German', 'Chinese', 'Japanese', 'Arabic', 'Kazakh'];
                } else {
                    $this->languages = ['Русский', 'Английский', 'Испанский', 'Французский', 'Немецкий', 'Китайский', 'Японский', 'Арабский', 'Казахский'];
                }
            }

        } catch (\Exception $e) {
            logger()->error('Error loading languages: ' . $e->getMessage());
            // Fallback к базовым языкам
            $locale = app()->getLocale();
            if ($locale === 'en') {
                $this->languages = ['Russian', 'English', 'Spanish', 'French', 'German', 'Chinese', 'Japanese', 'Arabic', 'Kazakh'];
            } else {
                $this->languages = ['Русский', 'Английский', 'Испанский', 'Французский', 'Немецкий', 'Китайский', 'Японский', 'Арабский', 'Казахский'];
            }
        }
    }



    public function removeLanguage($index)
    {
        unset($this->language_skills[$index]);
        $this->language_skills = array_values($this->language_skills);
    }

    public function addWorkExperience()
    {
        $this->work_experience[] = [
            'years' => '',
            'company' => '',
            'city' => '',
            'position' => '',
            'start_month' => '',
            'start_year' => '',
            'end_month' => '',
            'end_year' => '',
            'start_period' => 240, // Примерно 2020 год (240 месяцев с 1990)
            'end_period' => 300,   // Примерно 2025 год (300 месяцев с 1990)
            'is_current' => false,
            'activity_sphere' => '', // Сфера деятельности
            'main_tasks' => ['', '', ''], // Основные задачи (минимум 3)
        ];
    }

    public function removeWorkExperience($index)
    {
        unset($this->work_experience[$index]);
        $this->work_experience = array_values($this->work_experience);
    }

    // Методы для желаемых должностей
    public function addDesiredPosition()
    {
        if (count($this->desired_positions) < 3) {
            $this->desired_positions[] = '';
        }
    }

    public function removeDesiredPosition($index)
    {
        unset($this->desired_positions[$index]);
        $this->desired_positions = array_values($this->desired_positions);
    }

    // Методы для добавления задач в опыт работы
    public function addWorkTask($experienceIndex)
    {
        if (isset($this->work_experience[$experienceIndex])) {
            if (!isset($this->work_experience[$experienceIndex]['main_tasks'])) {
                $this->work_experience[$experienceIndex]['main_tasks'] = [];
            }
            if (count($this->work_experience[$experienceIndex]['main_tasks']) < 8) {
                $this->work_experience[$experienceIndex]['main_tasks'][] = '';
            }
        }
    }

    public function removeWorkTask($experienceIndex, $taskIndex)
    {
        if (isset($this->work_experience[$experienceIndex]['main_tasks'][$taskIndex])) {
            // Не удаляем если осталось 3 или меньше задач
            if (count($this->work_experience[$experienceIndex]['main_tasks']) > 3) {
                unset($this->work_experience[$experienceIndex]['main_tasks'][$taskIndex]);
                $this->work_experience[$experienceIndex]['main_tasks'] = array_values($this->work_experience[$experienceIndex]['main_tasks']);
            }
        }
    }

    // Методы для наград и достижений
    public function addAward()
    {
        $this->awards[] = '';
    }

    public function removeAward($index)
    {
        unset($this->awards[$index]);
        $this->awards = array_values($this->awards);
    }

    public function updatedPhoto()
    {
        $this->validate([
            'photo' => 'image|max:20480' // 20MB
        ]);

        if ($this->photo) {
            try {
                // Принудительно сохраняем фото в постоянное место хранения
                $this->savePhotoImmediately();

                // Отправляем событие в браузер
                $this->dispatch('photoUploaded');

            } catch (\Exception $e) {
                // Обработка ошибок
                $this->addError('photo', 'Ошибка при обработке фото: ' . $e->getMessage());
                $this->dispatch('photo-error', ['message' => 'Ошибка при загрузке фото']);
            }
        }
    }

    public function savePhotoImmediately()
    {
        if (!$this->photo || is_string($this->photo)) {
            return; // Фото уже сохранено или отсутствует
        }

        try {
            // Создаем или обновляем кандидата если его нет
            if (!$this->candidate) {
                $this->candidate = new Candidate();
                $this->candidate->user_id = auth()->id();
                $this->candidate->step = $this->currentStep;
                // Сохраняем базовую информацию если есть, иначе оставляем null
                if ($this->last_name || $this->first_name) {
                    $firstName = mb_strtoupper(mb_substr($this->first_name, 0, 1, 'UTF-8'), 'UTF-8') . mb_strtolower(mb_substr($this->first_name, 1, null, 'UTF-8'), 'UTF-8');
                    $lastName = mb_strtoupper(mb_substr($this->last_name, 0, 1, 'UTF-8'), 'UTF-8') . mb_strtolower(mb_substr($this->last_name, 1, null, 'UTF-8'), 'UTF-8');
                    $this->candidate->full_name = trim($firstName . ' ' . $lastName);
                } else {
                    $this->candidate->full_name = null; // Явно устанавливаем null
                }
                if ($this->email) $this->candidate->email = $this->email;
            }

            // Удаляем старое фото если есть
            if ($this->candidate->photo) {
                Storage::disk('public')->delete($this->candidate->photo);
            }

            // Сохраняем новое фото
            $photoPath = $this->photo->store('photos', 'public');
            $this->candidate->photo = $photoPath;
            $this->candidate->save();

            // Обновляем предпросмотр на постоянный URL
            $this->photoPreview = Storage::disk('public')->url($photoPath);

            // Устанавливаем фото как строку, чтобы указать что оно уже сохранено
            $this->photo = $photoPath;

            logger()->info('Photo saved immediately', ['path' => $photoPath, 'candidate_id' => $this->candidate->id]);

        } catch (\Exception $e) {
            logger()->error('Error saving photo immediately: ' . $e->getMessage());
            throw $e;
        }
    }

    public function removePhoto()
    {
        try {
            // Удаляем фото из storage если оно есть
            if ($this->candidate && $this->candidate->photo) {
                Storage::disk('public')->delete($this->candidate->photo);
            }

            // Очищаем свойство фото
            $this->photo = null;
            $this->photoPreview = null;

            // Обновляем базу данных если кандидат существует
            if ($this->candidate) {
                $this->candidate->photo = null;
                $this->candidate->save();
            }

            session()->flash('message', 'Фото удалено');
        } catch (\Exception $e) {
            logger()->error('Error removing photo: ' . $e->getMessage());
            session()->flash('error', 'Ошибка при удалении фото');
        }
    }

    public function updatedInstagram()
    {
        // Автоматически добавляем @ если пользователь не указал
        if ($this->instagram && !str_starts_with($this->instagram, '@')) {
            $this->instagram = '@' . $this->instagram;
        }
    }

    public function updatedGallupPdf()
    {
        logger()->info('Gallup file upload started', [
            'file_present' => $this->gallup_pdf ? 'yes' : 'no',
            'file_type' => $this->gallup_pdf ? get_class($this->gallup_pdf) : 'null'
        ]);

        if ($this->gallup_pdf) {
            try {
                // Логируем информацию о файле
                logger()->info('Gallup file info', [
                    'original_name' => $this->gallup_pdf->getClientOriginalName(),
                    'size' => $this->gallup_pdf->getSize(),
                    'mime_type' => $this->gallup_pdf->getMimeType(),
                ]);

                // Базовая валидация файла (PDF или изображение)
                $this->validate([
                    'gallup_pdf' => 'file|mimes:pdf,jpg,jpeg,png,webp|max:10240'
                ]);

                logger()->info('Gallup file passed basic validation');

                // Проверяем, что это корректный Gallup файл
                if (!$this->isValidGallupFile($this->gallup_pdf)) {
                    logger()->warning('Gallup file failed content validation');
                    $this->addError('gallup_pdf', 'Загруженный файл не является корректным отчетом Gallup. Убедитесь, что это официальный PDF или изображение с результатами теста Gallup.');
                    $this->resetGallupFile();
                    return;
                }

                logger()->info('Gallup file validation successful');

                // Отправляем событие в JavaScript
                $this->dispatch('gallup-file-uploaded');

                session()->flash('message', 'Файл загружен и проверен');
            } catch (\Exception $e) {
                logger()->error('Error processing Gallup file', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                $this->addError('gallup_pdf', 'Ошибка при обработке файла: ' . $e->getMessage());
                $this->resetGallupFile();
            }
        }
    }

    /**
     * Сбрасывает состояние gallup_pdf файла
     */
    private function resetGallupFile()
    {
        $this->gallup_pdf = null;
        $this->dispatch('gallup-file-reset'); // Отправляем событие в JavaScript для сброса UI
    }

    public function submit()
    {
        try {
            logger()->debug('Starting submit method');
            logger()->debug('Current step: ' . $this->currentStep);
            logger()->debug('Gallup PDF: ', ['gallup_pdf' => $this->gallup_pdf ? 'present' : 'null', 'candidate_gallup' => $this->candidate?->gallup_pdf]);
            logger()->debug('MBTI type: ' . $this->mbti_type);

            // Фильтруем пустые элементы семьи перед валидацией
            $this->filterEmptyFamilyElements();

            // Создаем специальные правила для финального submit
            $rules = $this->rules();

            // Если фото уже сохранено (строка) и не загружается новое, исключаем из валидации
            if ($this->candidate && $this->candidate->photo && is_string($this->photo)) {
                unset($rules['photo']);
                logger()->debug('Photo validation removed (existing file)');
            } else if ($this->candidate && $this->candidate->photo) {
                // Если есть сохраненное фото, но загружается новое
                $rules['photo'] = ['nullable', 'image', 'max:20480'];
                logger()->debug('Photo rule modified to nullable (candidate has existing photo)');
            }

            // Если Gallup PDF уже сохранен (строка) и не загружается новый, исключаем из валидации
            if ($this->candidate && $this->candidate->gallup_pdf && is_string($this->gallup_pdf)) {
                unset($rules['gallup_pdf']);
                logger()->debug('Gallup PDF validation removed (existing file)');
            } else if ($this->candidate && $this->candidate->gallup_pdf) {
                // Если есть сохраненный файл, но загружается новый
                $rules['gallup_pdf'] = [
                    'nullable',
                    'file',
                    'mimes:pdf,jpg,jpeg,png,webp',
                    'max:10240',
                    function ($attribute, $value, $fail) {
                        if ($value && !is_string($value) && !$this->isValidGallupFile($value)) {
                            $fail('Загруженный файл не является корректным отчетом Gallup.');
                        }
                    }
                ];
                logger()->debug('Gallup PDF rule modified to nullable with validation (file exists in DB)');
            }

            logger()->debug('Validation rules for submit:', ['photo' => $rules['photo'] ?? 'not set', 'gallup_pdf' => $rules['gallup_pdf'] ?? 'not set', 'mbti_type' => $rules['mbti_type'] ?? 'not set']);

            // Отладка значения религии
            logger()->debug('Religion debug:', [
                'current_religion_value' => $this->religion,
                'allowed_religions' => array_values(config('lists.religions')),
                'religion_validation_rule' => $rules['religion'] ?? 'not set'
            ]);

            $this->validate($rules);
            logger()->debug('Validation passed');

            if (!$this->candidate) {
                $this->candidate = new Candidate();
                $this->candidate->user_id = auth()->id();
            } else {
                // Проверяем права доступа при финальном сохранении
                if ($this->candidate->user_id !== auth()->id() && !auth()->user()->is_admin) {
                    abort(403, 'У вас нет прав для изменения этой анкеты.');
                }
            }

            // Basic Information
            // Объединяем ФИО (сначала Имя, потом Фамилия) с капитализацией
            $firstName = mb_strtoupper(mb_substr($this->first_name, 0, 1, 'UTF-8'), 'UTF-8') . mb_strtolower(mb_substr($this->first_name, 1, null, 'UTF-8'), 'UTF-8');
            $lastName = mb_strtoupper(mb_substr($this->last_name, 0, 1, 'UTF-8'), 'UTF-8') . mb_strtolower(mb_substr($this->last_name, 1, null, 'UTF-8'), 'UTF-8');
            $this->candidate->full_name = trim($firstName . ' ' . $lastName);
            $this->candidate->email = $this->email;
            $this->candidate->phone = $this->phone;
            $this->candidate->gender = $this->gender;
            $this->candidate->marital_status = $this->marital_status;
            $this->candidate->birth_date = $this->birth_date;
            $this->candidate->birth_place = $this->birth_place;
            $this->candidate->current_city = $this->current_city;
            $this->candidate->step = 5; // Устанавливаем финальный шаг

            // Handle photo upload
            if ($this->photo && !is_string($this->photo)) {
                if ($this->candidate->photo) {
                    Storage::disk('public')->delete($this->candidate->photo);
                }
                $photoPath = $this->photo->store('photos', 'public');
                $this->candidate->photo = $photoPath;
            }

            // Additional Information
            $this->candidate->religion = $this->religion;
            $this->candidate->is_practicing = $this->is_practicing;

            // Сохраняем новую структуру семьи в JSON
            $familyStructure = $this->buildFamilyStructure();
            logger()->info('Saving family structure in submit', [
                'family_structure' => $familyStructure,
                'parents_from_component' => $this->parents,
                'siblings_from_component' => $this->siblings,
                'children_from_component' => $this->children
            ]);
            $this->candidate->family_members = $familyStructure;

            $this->candidate->hobbies = $this->hobbies;
            $this->candidate->interests = $this->interests;
            $this->candidate->visited_countries = $this->visited_countries;
            $this->candidate->books_per_year = $this->books_per_year;
            $this->candidate->favorite_sports = $this->favorite_sports;
            $this->candidate->entertainment_hours_weekly = $this->entertainment_hours_weekly;
            $this->candidate->educational_hours_weekly = $this->educational_hours_weekly;
            $this->candidate->social_media_hours_weekly = $this->social_media_hours_weekly;
            $this->candidate->has_driving_license = $this->has_driving_license;

            // Education and Work
            // Объединяем поля школы в формат "Название / Город / Год"
            if ($this->school_name && $this->school_city && $this->school_graduation_year) {
                $this->candidate->school = trim($this->school_name) . ' / ' . trim($this->school_city) . ' / ' . $this->school_graduation_year;
            }
            $this->candidate->universities = $this->universities;

            // Фильтруем пустые языковые навыки
            $this->candidate->language_skills = array_filter($this->language_skills, function($skill) {
                return !empty($skill['language']) && !empty($skill['level']);
            });

            $this->candidate->computer_skills = $this->computer_skills;

            // Фильтруем пустые записи опыта работы
            $this->candidate->work_experience = array_filter($this->work_experience, function($experience) {
                return !empty($experience['years']) || !empty($experience['company']) || !empty($experience['city']) || !empty($experience['position']);
            });
            $this->candidate->total_experience_years = $this->total_experience_years;
            $this->candidate->job_satisfaction = $this->job_satisfaction;
            // Сохраняем желаемые должности как массив и также в старое поле для обратной совместимости
            $this->candidate->desired_positions = array_filter($this->desired_positions);
            $this->candidate->desired_position = implode(' / ', array_filter($this->desired_positions));
            $this->candidate->activity_sphere = $this->activity_sphere;
            $this->candidate->awards = array_filter($this->awards);
            $this->candidate->expected_salary = $this->expected_salary;
            $this->candidate->expected_salary_from = $this->expected_salary_from;
            $this->candidate->expected_salary_to = $this->expected_salary_to;
            $this->candidate->employer_requirements = $this->employer_requirements;

            // Handle Gallup PDF upload
            if ($this->gallup_pdf && !is_string($this->gallup_pdf)) {
                if ($this->candidate->gallup_pdf) {
                    Storage::disk('public')->delete($this->candidate->gallup_pdf);
                }
                $gallupPath = $this->gallup_pdf->store('gallup', 'public');
                $this->candidate->gallup_pdf = $gallupPath;
            }

            // Save MBTI type
            $this->candidate->mbti_type = $this->mbti_type;

            $this->candidate->save();

            // Создаем запись в истории о завершении анкеты
            CandidateHistory::create([
                'candidate_id' => $this->candidate->id,
                'field_name' => 'status',
                'old_value' => 'in_progress',
                'new_value' => 'completed',
                'changed_by' => auth()->user()?->name ?? 'Guest',
                'ip_address' => request()->ip()
            ]);

            // Запускаем обработку Gallup файла синхронно (сразу обрабатывается)
            if ($this->candidate->gallup_pdf) {
                try {
                    ProcessGallupFile::dispatchSync($this->candidate);
                    $this->candidate->refresh(); // Обновляем данные после обработки
                    logger()->info('Gallup file processed successfully', [
                        'candidate_id' => $this->candidate->id,
                        'step' => $this->candidate->step
                    ]);
                } catch (\Exception $e) {
                    logger()->error('Gallup file processing failed', [
                        'candidate_id' => $this->candidate->id,
                        'error' => $e->getMessage()
                    ]);
                }
            }

            session()->flash('message', 'Резюме успешно сохранено!');

            // Определяем, куда перенаправить пользователя
            if (auth()->user()->is_admin) {
                return redirect()->to('/admin/candidates');
            }

            return redirect()->route('dashboard');
        } catch (\Illuminate\Validation\ValidationException $e) {
            logger()->debug('Validation errors in submit:', $e->errors());
            throw $e;
        } catch (\Exception $e) {
            logger()->error('Unexpected error in submit:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    protected function saveProgress()
    {
        // Если это новая запись, создаем новую модель
        if (!$this->candidate) {
            $this->candidate = new Candidate();
            $this->candidate->user_id = auth()->id();
        } else {
            // Проверяем права доступа при сохранении существующей анкеты
            if ($this->candidate->user_id !== auth()->id() && !auth()->user()->is_admin) {
                abort(403, 'У вас нет прав для изменения этой анкеты.');
            }
        }

        // Базовые данные всегда сохраняем, если они есть
        if ($this->last_name || $this->first_name) {
            $firstName = mb_strtoupper(mb_substr($this->first_name, 0, 1, 'UTF-8'), 'UTF-8') . mb_strtolower(mb_substr($this->first_name, 1, null, 'UTF-8'), 'UTF-8');
            $lastName = mb_strtoupper(mb_substr($this->last_name, 0, 1, 'UTF-8'), 'UTF-8') . mb_strtolower(mb_substr($this->last_name, 1, null, 'UTF-8'), 'UTF-8');
            $this->candidate->full_name = trim($firstName . ' ' . $lastName);
        } else {
            $this->candidate->full_name = null; // Явно устанавливаем null если имя не введено
        }
        if ($this->email) $this->candidate->email = $this->email;
        if ($this->phone) $this->candidate->phone = $this->phone;
        if ($this->gender) $this->candidate->gender = $this->gender;
        if ($this->marital_status) $this->candidate->marital_status = $this->marital_status;
        if ($this->birth_date) $this->candidate->birth_date = $this->birth_date;
        if ($this->birth_place) $this->candidate->birth_place = $this->birth_place;
        if ($this->current_city) $this->candidate->current_city = $this->current_city;
        if ($this->ready_to_relocate !== null) $this->candidate->ready_to_relocate = $this->ready_to_relocate;
        if ($this->instagram) $this->candidate->instagram = $this->instagram;

        // Фото обрабатываем отдельно
        if ($this->photo && !is_string($this->photo)) {
            if ($this->candidate->photo) {
                Storage::disk('public')->delete($this->candidate->photo);
            }
            $photoPath = $this->photo->store('photos', 'public');
            $this->candidate->photo = $photoPath;
        }

        // Дополнительная информация
        if ($this->religion !== null) $this->candidate->religion = $this->religion;
        if ($this->is_practicing !== null) $this->candidate->is_practicing = $this->is_practicing;

        // Сохраняем новую структуру семьи
        $familyStructure = $this->buildFamilyStructure();
        logger()->info('Saving family structure in saveProgress', [
            'family_structure' => $familyStructure,
            'parents_from_component' => $this->parents,
            'siblings_from_component' => $this->siblings,
            'children_from_component' => $this->children
        ]);
        // Всегда сохраняем структуру семьи, даже если она пустая
        $this->candidate->family_members = $familyStructure;

        if ($this->hobbies !== null) $this->candidate->hobbies = $this->hobbies;
        if ($this->interests !== null) $this->candidate->interests = $this->interests;
        if (!empty($this->visited_countries)) $this->candidate->visited_countries = $this->visited_countries;
        if ($this->books_per_year !== null) $this->candidate->books_per_year = $this->books_per_year;
        if (!empty($this->favorite_sports)) $this->candidate->favorite_sports = $this->favorite_sports;
        if ($this->entertainment_hours_weekly !== null) $this->candidate->entertainment_hours_weekly = $this->entertainment_hours_weekly;
        if ($this->educational_hours_weekly !== null) $this->candidate->educational_hours_weekly = $this->educational_hours_weekly;
        if ($this->social_media_hours_weekly !== null) $this->candidate->social_media_hours_weekly = $this->social_media_hours_weekly;
        if ($this->has_driving_license !== null) $this->candidate->has_driving_license = $this->has_driving_license;

        // Образование и работа
        // Объединяем поля школы в формат "Название / Город / Год"
        if ($this->school_name && $this->school_city && $this->school_graduation_year) {
            $this->candidate->school = trim($this->school_name) . ' / ' . trim($this->school_city) . ' / ' . $this->school_graduation_year;
        }
        if (!empty($this->universities)) $this->candidate->universities = $this->universities;

        // Фильтруем пустые языковые навыки
        if (!empty($this->language_skills)) {
            $filteredLanguageSkills = array_filter($this->language_skills, function($skill) {
                return !empty($skill['language']) && !empty($skill['level']);
            });
            $this->candidate->language_skills = array_values($filteredLanguageSkills);
        }

        if ($this->computer_skills !== null) $this->candidate->computer_skills = $this->computer_skills;

        // Фильтруем пустые записи опыта работы
        if (!empty($this->work_experience)) {
            $filteredWorkExperience = array_filter($this->work_experience, function($experience) {
                return !empty($experience['years']) || !empty($experience['company']) || !empty($experience['city']) || !empty($experience['position']);
            });
            $this->candidate->work_experience = array_values($filteredWorkExperience);
        }
        if ($this->total_experience_years !== null) $this->candidate->total_experience_years = $this->total_experience_years;
        if ($this->job_satisfaction !== null) $this->candidate->job_satisfaction = $this->job_satisfaction;
        if (!empty($this->desired_positions)) {
            $this->candidate->desired_positions = array_filter($this->desired_positions);
            $this->candidate->desired_position = implode(' / ', array_filter($this->desired_positions));
        }
        if ($this->activity_sphere) $this->candidate->activity_sphere = $this->activity_sphere;
        if (!empty($this->awards)) $this->candidate->awards = array_filter($this->awards);
        if ($this->expected_salary !== null) $this->candidate->expected_salary = $this->expected_salary;
        if ($this->expected_salary_from !== null) $this->candidate->expected_salary_from = $this->expected_salary_from;
        if ($this->expected_salary_to !== null) $this->candidate->expected_salary_to = $this->expected_salary_to;
        if ($this->employer_requirements !== null) $this->candidate->employer_requirements = $this->employer_requirements;

        // Handle Gallup PDF upload
        if ($this->gallup_pdf && !is_string($this->gallup_pdf)) {
            if ($this->candidate->gallup_pdf) {
                Storage::disk('public')->delete($this->candidate->gallup_pdf);
            }
            $gallupPath = $this->gallup_pdf->store('gallup', 'public');
            $this->candidate->gallup_pdf = $gallupPath;
        }

        // Save MBTI type if set
        if ($this->mbti_type) {
            $this->candidate->mbti_type = $this->mbti_type;
        }

        // Обновляем текущий шаг
        // Не изменяем шаг, если анкета уже завершена (step >= 5)
        if ($this->candidate->step < 5) {
        $this->candidate->step = $this->currentStep;
        }

        // Сохраняем все изменения
        $this->candidate->save();

        // Создаем запись в истории
        // Только если шаг действительно изменился и анкета не завершена
        if ($this->candidate->step < 5) {
        CandidateHistory::create([
            'candidate_id' => $this->candidate->id,
            'field_name' => 'step',
            'old_value' => $this->candidate->wasRecentlyCreated ? 0 : ($this->currentStep - 1),
            'new_value' => $this->currentStep,
            'changed_by' => auth()->user()?->name ?? 'Guest',
            'ip_address' => request()->ip()
        ]);
        }

        session()->flash('message', 'Прогресс сохранен');
    }

    public function addCountry($country = null)
    {
        // Если страна передана как параметр (из live search), используем её
        $countryToAdd = $country ?? $this->newCountry;
        
        logger()->debug('Adding country:', [
            'country_param' => $country,
            'newCountry' => $this->newCountry,
            'countryToAdd' => $countryToAdd,
            'visited_countries' => $this->visited_countries
        ]);

        if ($countryToAdd && !in_array($countryToAdd, $this->visited_countries)) {
            $this->visited_countries[] = $countryToAdd;
            $this->newCountry = ''; // Сбрасываем
            
            logger()->debug('Country added:', [
                'visited_countries' => $this->visited_countries,
                'last_added' => end($this->visited_countries)
            ]);
        }
    }


    public function getGallupPdfUrl()
    {
        if (!$this->gallup_pdf) {
            return null;
        }

        // Если это временный файл Livewire (UploadedFile)
        if (!is_string($this->gallup_pdf)) {
            return null; // Для временных файлов не показываем ссылку на скачивание
        }

        // Если это сохраненный файл (строка с путем)
        return Storage::disk('public')->url($this->gallup_pdf);
    }

    public function getGallupFileInfo()
    {
        if (!$this->gallup_pdf) {
            return null;
        }

        // Если это временный файл Livewire (UploadedFile)
        if (!is_string($this->gallup_pdf)) {
            return [
                'fileName' => $this->gallup_pdf->getClientOriginalName(),
                'fileSize' => $this->formatFileSize($this->gallup_pdf->getSize()),
                'isExisting' => false
            ];
        }

        // Если это сохраненный файл (строка с путем)
        $filePath = Storage::disk('public')->path($this->gallup_pdf);

        if (!file_exists($filePath)) {
            return [
                'fileName' => 'Файл не найден',
                'fileSize' => 'Не определен',
                'isExisting' => true
            ];
        }

        $pathInfo = pathinfo($this->gallup_pdf);
        $fileName = $pathInfo['basename'];

        // Убираем timestamp префикс если есть
        $cleanName = preg_replace('/^\d+_/', '', $fileName);

        return [
            'fileName' => $cleanName ?: 'Gallup результаты.pdf',
            'fileSize' => $this->formatFileSize(filesize($filePath)),
            'isExisting' => true
        ];
    }

    private function formatFileSize($bytes)
    {
        if ($bytes == 0) {
            return '0 Bytes';
        }

        $k = 1024;
        $sizes = ['Bytes', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes) / log($k));

        return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
    }

    /**
     * Проверяет, является ли загруженный файл корректным Gallup файлом (PDF или изображение)
     */
    private function isValidGallupFile($file): bool
    {
        try {
            // Получаем временный путь к файлу
            $tempPath = $file->getRealPath();
            $extension = strtolower($file->getClientOriginalExtension());

            // Для изображений - просто проверяем, что это валидное изображение
            if (in_array($extension, ['jpg', 'jpeg', 'png', 'webp'])) {
                $imageInfo = @getimagesize($tempPath);
                $isValidImage = $imageInfo !== false;

                logger()->info('Gallup image validation', [
                    'extension' => $extension,
                    'is_valid_image' => $isValidImage,
                    'width' => $imageInfo[0] ?? 0,
                    'height' => $imageInfo[1] ?? 0
                ]);

                // Изображения принимаем без глубокой проверки - GPT-4o проанализирует
                return $isValidImage;
            }

            // Для PDF - проверяем ключевые слова Gallup
            if ($extension === 'pdf') {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($tempPath);
                $text = $pdf->getText();
                $pages = $pdf->getPages();

                logger()->info('Gallup PDF validation', [
                    'page_count' => count($pages),
                    'has_gallup_inc' => str_contains($text, 'Gallup, Inc.'),
                    'has_clifton' => str_contains($text, 'CliftonStrengths') || str_contains($text, 'Clifton'),
                    'text_sample' => substr($text, 0, 500)
                ]);

                // Для PDF проверяем ключевые слова (поддержка русских и английских)
                $containsGallupKeywords = str_contains($text, 'Gallup') ||
                                        str_contains($text, 'CliftonStrengths') ||
                                        str_contains($text, 'StrengthsFinder') ||
                                        str_contains($text, 'Clifton') ||
                                        str_contains($text, 'Клифтон') ||
                                        str_contains($text, 'талант');

                // Смягчённые условия: минимум 1 страница и ключевые слова ИЛИ 10+ страниц
                $hasMinimumPages = count($pages) >= 1;
                $hasManyPages = count($pages) >= 10;
                $isValid = ($hasMinimumPages && $containsGallupKeywords) || $hasManyPages;

                logger()->info('Gallup PDF validation result', [
                    'is_valid' => $isValid,
                    'minimum_pages' => $hasMinimumPages,
                    'has_keywords' => $containsGallupKeywords
                ]);

                return $isValid;
            }

            // Неподдерживаемый формат
            return false;
        } catch (\Exception $e) {
            logger()->error('Error checking Gallup file: ' . $e->getMessage());
            // В случае ошибки, разрешаем загрузку (GPT-4o проанализирует)
            return true;
        }
    }

    /**
     * Конвертирует английские значения религии в русские для совместимости
     */
    private function convertReligionToRussian($religion)
    {
        $religions = config('lists.religions');

        // Если это уже русское значение, возвращаем как есть
        if (in_array($religion, array_values($religions))) {
            return $religion;
        }

        // Если это английский ключ, конвертируем в русское значение
        if (array_key_exists($religion, $religions)) {
            return $religions[$religion];
        }

        return $religion;
    }

    /**
     * Конвертирует старый формат опыта работы в новый
     */
    private function convertWorkExperienceFormat($workExperience)
    {
        logger()->debug('Converting work experience format:', ['input' => $workExperience]);

        if (empty($workExperience)) {
            logger()->debug('Work experience is empty, returning empty array');
            return [];
        }

        $converted = [];

        foreach ($workExperience as $experience) {
            // Если это уже новый формат (есть поле 'years'), оставляем как есть
            if (isset($experience['years'])) {
                $converted[] = [
                    'years' => $experience['years'] ?? '',
                    'company' => $experience['company'] ?? '',
                    'city' => $experience['city'] ?? '',
                    'position' => $experience['position'] ?? '',
                    'start_month' => $experience['start_month'] ?? '',
                    'start_year' => $experience['start_year'] ?? '',
                    'end_month' => $experience['end_month'] ?? '',
                    'end_year' => $experience['end_year'] ?? '',
                    'start_period' => $experience['start_period'] ?? 240, // Значение по умолчанию
                    'end_period' => $experience['end_period'] ?? 300,     // Значение по умолчанию
                    'is_current' => $experience['is_current'] ?? false,
                    'activity_sphere' => $experience['activity_sphere'] ?? '',
                    'main_tasks' => $experience['main_tasks'] ?? ['', '', ''], // Минимум 3 задачи
                ];
            }
            // Если это старый формат, конвертируем
            else {
                $years = '';
                if (isset($experience['start_date']) && isset($experience['end_date'])) {
                    $startYear = $experience['start_date'] ? date('Y', strtotime($experience['start_date'])) : '';
                    $endYear = $experience['end_date'] ? date('Y', strtotime($experience['end_date'])) : '';
                    $years = $startYear && $endYear ? "$startYear-$endYear" : ($startYear ?: $endYear);
                }

                // Конвертируем даты в значения ползунков
                $startPeriod = 240; // Значение по умолчанию
                $endPeriod = 300;   // Значение по умолчанию

                if (isset($experience['start_date'])) {
                    $startDate = strtotime($experience['start_date']);
                    $startYear = date('Y', $startDate);
                    $startMonth = date('n', $startDate);
                    $startPeriod = ($startYear - 1990) * 12 + ($startMonth - 1);
                }

                if (isset($experience['end_date'])) {
                    $endDate = strtotime($experience['end_date']);
                    $endYear = date('Y', $endDate);
                    $endMonth = date('n', $endDate);
                    $endPeriod = ($endYear - 1990) * 12 + ($endMonth - 1);
                }

                // Извлекаем месяц и год из дат
                $startMonth = '';
                $startYear = '';
                $endMonth = '';
                $endYear = '';

                if (isset($experience['start_date'])) {
                    $startDate = strtotime($experience['start_date']);
                    $startMonth = date('n', $startDate) - 1; // Преобразуем в 0-11
                    $startYear = date('Y', $startDate);
                }

                if (isset($experience['end_date'])) {
                    $endDate = strtotime($experience['end_date']);
                    $endMonth = date('n', $endDate) - 1; // Преобразуем в 0-11
                    $endYear = date('Y', $endDate);
                }

                $converted[] = [
                    'years' => $years,
                    'company' => $experience['company'] ?? '',
                    'city' => '', // В старом формате не было города
                    'position' => $experience['position'] ?? '',
                    'start_month' => $startMonth,
                    'start_year' => $startYear,
                    'end_month' => $endMonth,
                    'end_year' => $endYear,
                    'start_period' => $startPeriod,
                    'end_period' => $endPeriod,
                    'is_current' => false,
                    'activity_sphere' => '',
                    'main_tasks' => ['', '', ''], // Минимум 3 задачи
                ];
            }
        }

        logger()->debug('Work experience conversion completed:', ['output' => $converted]);
        return $converted;
    }

    /**
     * Переинициализирует валидацию при переходе между шагами
     */
    protected function reinitializeValidation()
    {
        // Сбрасываем только ошибки валидации, не затрагивая состояние полей
        $this->resetErrorBag();

        // Не вызываем resetValidation() чтобы не мешать JavaScript
        // $this->resetValidation();

        // Отправляем событие в браузер для переинициализации JavaScript
        $this->dispatch('step-changed', ['step' => $this->currentStep]);

        // Дополнительно отправляем событие для переинициализации JavaScript компонентов
        $this->dispatch('reinitialize-js');

        // Логируем для отладки
        logger()->debug('Validation reinitialized for step: ' . $this->currentStep);
        logger()->debug('Events dispatched: step-changed, reinitialize-js');
    }

    public function updatedBooksPerYearMin()
    {
        $this->updateBooksPerYear();
    }

    public function updatedBooksPerYearMax()
    {
        $this->updateBooksPerYear();
    }

    private function updateBooksPerYear()
    {
        if ($this->books_per_year_min == $this->books_per_year_max) {
            $this->books_per_year = (string) $this->books_per_year_min;
        } else {
            $this->books_per_year = $this->books_per_year_min . '-' . $this->books_per_year_max;
        }
    }

    private function parseBooksPerYear($value)
    {
        if (empty($value) || $value === '0' || $value === 0) {
            $this->books_per_year_min = 0;
            $this->books_per_year_max = 0;
            return;
        }

        // Если значение содержит дефис, это диапазон
        if (strpos($value, '-') !== false) {
            $parts = explode('-', $value);
            $this->books_per_year_min = (int) trim($parts[0]);
            $this->books_per_year_max = (int) trim($parts[1]);
        } else {
            // Иначе это одно значение
            $single_value = (int) $value;
            $this->books_per_year_min = $single_value;
            $this->books_per_year_max = $single_value;
        }
    }

    /**
     * Группирует ошибки по шагам
     */
    public function getErrorsByStep()
    {
        $errorsByStep = [
            1 => [],
            2 => [],
            3 => [],
            4 => []
        ];

        foreach ($this->getErrorBag()->keys() as $errorKey) {
            $step = $this->getFieldStep($errorKey);
            if ($step) {
                $errorsByStep[$step][] = $errorKey;
            }
        }

        return $errorsByStep;
    }

    /**
     * Проверяет есть ли ошибки на конкретном шаге
     */
    public function hasErrorsOnStep($step)
    {
        $errorsByStep = $this->getErrorsByStep();
        return !empty($errorsByStep[$step]);
    }

    /**
     * Возвращает названия шагов на русском
     */
    public function getStepTitle($step)
    {
        return match($step) {
            1 => 'Основная информация',
            2 => 'Дополнительная информация',
            3 => 'Образование и работа',
            4 => 'Тесты',
            default => 'Шаг ' . $step
        };
    }

    public function render()
    {
        return view('livewire.candidate-form');
    }
}
