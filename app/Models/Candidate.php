<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Candidate extends Model
{
    use HasFactory;

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Автоматически присваиваем display_number при создании нового кандидата
        static::creating(function ($candidate) {
            if (is_null($candidate->display_number)) {
                $maxNumber = static::max('display_number') ?? 0;
                $candidate->display_number = $maxNumber + 1;
            }
        });
    }

    /**
     * Get the route key for the model.
     * Используем display_number для URL вместо id
     */
    public function getRouteKeyName(): string
    {
        return 'display_number';
    }

    protected $fillable = [
        // Basic Information
        'user_id',
        'step',
        'step_parse_gallup',
        'display_number',
        'full_name',
        'email',
        'phone',
        'gender',
        'marital_status',
        'birth_date',
        'birth_place',
        'current_city',
        'ready_to_relocate',
        'instagram',
        'photo',

        // Additional Information
        'religion',
        'is_practicing',
        'family_members',
        'hobbies',
        'interests',
        'visited_countries',
        'books_per_year',
        'favorite_sports',
        'entertainment_hours_weekly',
        'educational_hours_weekly',
        'social_media_hours_weekly',
        'has_driving_license',

        // Education and Work
        'school',
        'universities',
        'language_skills',
        'computer_skills',
        'work_experience',
        'has_no_work_experience',
        'total_experience_years',
        'job_satisfaction',
        'desired_position',
        'desired_positions',
        'activity_sphere',
        'awards',
        'expected_salary',
        'expected_salary_from',
        'expected_salary_to',
        'employer_requirements',

        // Assessments
        'gallup_pdf',
        'anketa_pdf',
        'mbti_type',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'is_practicing' => 'boolean',
        'ready_to_relocate' => 'boolean',
        'has_driving_license' => 'boolean',
        'family_members' => 'array',
        'visited_countries' => 'array',
        'favorite_sports' => 'array',
        'universities' => 'array',
        'language_skills' => 'array',
        'computer_skills' => 'string',
        'work_experience' => 'array',
        'has_no_work_experience' => 'boolean',
        'desired_positions' => 'array',
        'awards' => 'array',
        'books_per_year' => 'string',
        'entertainment_hours_weekly' => 'integer',
        'educational_hours_weekly' => 'integer',
        'social_media_hours_weekly' => 'integer',
        'total_experience_years' => 'integer',
        'job_satisfaction' => 'integer',
        'expected_salary' => 'decimal:2',
        'expected_salary_from' => 'decimal:2',
        'expected_salary_to' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function additionalInformation(): HasOne
    {
        return $this->hasOne(AdditionalInformation::class);
    }

    /**
     * Получить полное описание MBTI типа
     */
    public function getMbtiFullNameAttribute(): ?string
    {
        if (!$this->mbti_type) {
            return null;
        }

        $mbtiTypes = [
            'INTJ' => 'INTJ - Архитектор',
            'INTP' => 'INTP - Логик',
            'ENTJ' => 'ENTJ - Командир',
            'ENTP' => 'ENTP - Полемист',
            'INFJ' => 'INFJ - Заступник',
            'INFP' => 'INFP - Посредник',
            'ENFJ' => 'ENFJ - Протагонист',
            'ENFP' => 'ENFP - Активист',
            'ISTJ' => 'ISTJ - Логист',
            'ISFJ' => 'ISFJ - Защитник',
            'ESTJ' => 'ESTJ - Менеджер',
            'ESFJ' => 'ESFJ - Консул',
            'ISTP' => 'ISTP - Виртуоз',
            'ISFP' => 'ISFP - Авантюрист',
            'ESTP' => 'ESTP - Предприниматель',
            'ESFP' => 'ESFP - Артист',
        ];

        return $mbtiTypes[$this->mbti_type] ?? $this->mbti_type;
    }

    /**
     * Получить форматированную строку с диапазоном зарплаты
     */
    public function getFormattedSalaryRangeAttribute(): string
    {
        // Проверяем новые поля (диапазон)
        if ($this->expected_salary_from && $this->expected_salary_to) {
            // Форматируем числа с пробелами в качестве разделителей тысяч
            $from = number_format($this->expected_salary_from, 0, ',', ' ');
            $to = number_format($this->expected_salary_to, 0, ',', ' ');

            return "{$from}-{$to}₸";
        }

        // Fallback: проверяем старое поле для обратной совместимости
        if ($this->expected_salary && $this->expected_salary > 0) {
            $formatted = number_format($this->expected_salary, 0, ',', ' ');
            return "{$formatted}₸";
        }

        return 'Не указано';
    }

    public function educationWork(): HasOne
    {
        return $this->hasOne(EducationWork::class);
    }

    public function assessment(): HasOne
    {
        return $this->hasOne(Assessment::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(CandidateFile::class);
    }

    public function history(): HasMany
    {
        return $this->hasMany(CandidateHistory::class);
    }

    public function statuses(): HasMany
    {
        return $this->hasMany(CandidateStatus::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(CandidateComment::class);
    }

    public function getLatestStatusAttribute()
    {
        return $this->statuses()->latest()->first();
    }

    public function getCurrentStatusAttribute()
    {
        return $this->latest_status?->status ?? 'draft';
    }

    public function gallupTalents()
    {
        return $this->hasMany(GallupTalent::class);
    }

    public function gallupReports()
    {
        return $this->hasMany(GallupReport::class);
    }

    /**
     * Получает структурированные данные о семье
     */
    public function getFamilyStructured()
    {
        $familyData = $this->family_members ?? [];
        
        // Если это новая структура
        if (is_array($familyData) && isset($familyData['parents'])) {
            return [
                'parents' => $familyData['parents'] ?? [],
                'siblings' => $familyData['siblings'] ?? [],
                'children' => $familyData['children'] ?? [],
                'is_new_structure' => true
            ];
        }
        
        // Если это старая структура - преобразуем
        $parents = [];
        $siblings = [];
        $children = [];
        
        if (is_array($familyData)) {
            foreach ($familyData as $member) {
                if (!is_array($member)) continue;
                
                $type = $member['type'] ?? '';
                switch ($type) {
                    case 'Отец':
                    case 'Мать':
                        $parents[] = [
                            'relation' => $type,
                            'birth_year' => $member['birth_year'] ?? '',
                            'profession' => $member['profession'] ?? ''
                        ];
                        break;
                    case 'Брат':
                    case 'Сестра':
                        $siblings[] = [
                            'relation' => $type,
                            'birth_year' => $member['birth_year'] ?? ''
                        ];
                        break;
                    case 'Сын':
                    case 'Дочь':
                        $children[] = [
                            'name' => $member['profession'] ?? '', // В старой структуре имя было в поле profession
                            'birth_year' => $member['birth_year'] ?? ''
                        ];
                        break;
                }
            }
        }
        
        return [
            'parents' => $parents,
            'siblings' => $siblings,
            'children' => $children,
            'is_new_structure' => false
        ];
    }

    /**
     * Получает форматированный список родителей
     */
    public function getFormattedParents()
    {
        $family = $this->getFamilyStructured();

        // Проверяем флаг "нет родителей"
        $familyData = $this->family_members ?? [];
        if (is_array($familyData) && !empty($familyData['no_parents'])) {
            return ['Нет родителей'];
        }

        $formatted = [];

        foreach ($family['parents'] as $parent) {
            $line = $parent['relation'] ?? 'Не указано';
            $line .= ' - ' . ($parent['birth_year'] ?? 'Не указано') . ' г.р.';
            if (!empty($parent['profession'])) {
                $line .= ' - ' . $parent['profession'];
            }
            $formatted[] = $line;
        }

        return $formatted;
    }

    /**
     * Получает форматированный список братьев и сестер
     */
    public function getFormattedSiblings()
    {
        $family = $this->getFamilyStructured();

        // Проверяем флаг "единственный ребенок"
        $familyData = $this->family_members ?? [];
        if (is_array($familyData) && !empty($familyData['only_child'])) {
            return ['Единственный ребенок'];
        }

        $formatted = [];

        foreach ($family['siblings'] as $sibling) {
            $line = $sibling['relation'] ?? 'Не указано';
            $line .= ' - ' . ($sibling['birth_year'] ?? 'Не указано') . ' г.р.';
            $formatted[] = $line;
        }

        // Если нет братьев/сестёр и флаг не установлен, возвращаем "Не указано"
        if (empty($formatted)) {
            return ['Не указано'];
        }

        return $formatted;
    }

    /**
     * Получает форматированный список детей
     */
    public function getFormattedChildren()
    {
        $family = $this->getFamilyStructured();
        $formatted = [];
        
        foreach ($family['children'] as $child) {
            $line = $child['name'] ?? 'Не указано';
            $line .= ' - ' . ($child['birth_year'] ?? 'Не указано') . ' г.р.';
            $formatted[] = $line;
        }
        
        return $formatted;
    }

    public function gallupReportByType(string $type): ?GallupReport
    {
        return $this->gallupReports()->where('type', $type)->latest()->first();
    }

    public function gardnerTestResult()
    {
        return $this->hasOneThrough(GardnerTestResult::class, User::class);
    }
}
