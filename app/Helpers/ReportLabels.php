<?php

namespace App\Helpers;

class ReportLabels
{
    private static array $labels = [
        'ru' => [
            'candidate_report' => 'Отчет о кандидате',
            'reduced_report' => 'Урезанный отчет о кандидате',
            'reduced_version' => '(урезанная версия)',
            'full_name' => 'ФИО',
            'birth_date' => 'Дата рождения',
            'birth_place' => 'Место рождения',
            'age' => 'Возраст',
            'years' => 'лет',
            'year_singular' => 'год',
            'years_2_4' => 'года',
            'years_many' => 'лет',
            'hour_singular' => 'час',
            'hours_2_4' => 'часа',
            'hours_many' => 'часов',
            'gender' => 'Пол',
            'nationality' => 'Национальность',
            'city' => 'Город',
            'phone' => 'Телефон',
            'email' => 'Email',
            'marital_status' => 'Семейное положение',
            'driving_license' => 'Водительские права',
            'religion' => 'Религия',
            'religious_practice' => 'Рел. практика',
            'yes' => 'Да',
            'no' => 'Нет',
            'photo' => 'Фото',
            'candidate_photo' => 'Фото кандидата',

            // Main info
            'main_info' => 'Основная информация',
            'desired_position' => 'Желаемая должность',
            'expected_salary' => 'Ожидаемая заработная плата',
            'professional_education' => 'Профессиональное образование',
            'workplace_preferences' => 'Пожелания на рабочем месте',

            // Sections
            'work_experience' => 'Опыт работы',
            'work_experience_not_specified' => 'Опыт работы не указан',
            'companies_positions' => 'Компании и должности',
            'total_experience' => 'Общий стаж',
            'total_experience_years' => 'Общий стаж работы (лет)',
            'job_satisfaction' => 'Удовлетворённость работой',
            'job_satisfaction_out_of_5' => 'Любит свою работу на (из 5)',
            'awards_achievements' => 'Награды и достижения',
            'interests_development' => 'Интересы и развитие',
            'hobbies' => 'Хобби',
            'interests' => 'Интересы',
            'goals_5_years' => 'Цели на 5 лет',
            'ideal_job' => 'Идеальная работа',
            'strengths' => 'Сильные стороны',
            'weaknesses' => 'Слабые стороны',
            'achievements' => 'Достижения',
            'books_per_year' => 'Кол-во книг в год',
            'entertainment_hours' => 'Часы на разв. видео в неделю',
            'educational_hours' => 'Часы на обра. видео в неделю',
            'social_media_hours' => 'Часы на соц. сети в неделю',
            'visited_countries' => 'Посещенные страны',
            'favorite_sports' => 'Любимые виды спорта',

            // Education
            'education' => 'Образование',
            'school' => 'Школа',
            'university' => 'Университет',
            'specialization' => 'Специализация',
            'graduation_year' => 'Год окончания',
            'degree' => 'Степень',
            'gpa' => 'GPA',

            // Family
            'family' => 'Семья',
            'parents' => 'Родители',
            'siblings' => 'Кол-во братьев/сестер',
            'children' => 'Дети',
            'brother_short' => 'Б',
            'sister_short' => 'С',
            'male_short' => 'М',

            // Skills
            'language_skills' => 'Языковые навыки',
            'language_skills_not_specified' => 'Языковые навыки не указаны',
            'computer_skills' => 'Компьютерные навыки',

            // Psychometrics
            'psychometric_data' => 'Психометрические данные',
            'mbti_type' => 'Тип личности по MBTI',
            'gallup_talents' => 'Таланты по Gallup',
            'gardner_intelligence' => 'Виды интеллектов Гарднера',

            // Gardner intelligence types
            'linguistic_intelligence' => 'Лингвистический интеллект',
            'logical_mathematical_intelligence' => 'Логико-математический интеллект',
            'musical_intelligence' => 'Музыкальный интеллект',
            'bodily_kinesthetic_intelligence' => 'Телесно-кинестетический интеллект',
            'spatial_intelligence' => 'Пространственный интеллект',
            'interpersonal_intelligence' => 'Межличностный интеллект',
            'intrapersonal_intelligence' => 'Внутриличностный интеллект',
            'naturalistic_intelligence' => 'Натуралистический интеллект',
            'existential_intelligence' => 'Экзистенциальный интеллект',
            'intelligence' => 'интеллект',

            // Other
            'not_specified' => 'Не указано',
            'date_filled' => 'Дата заполнения',
            'available' => 'Есть',
            'not_available' => 'Нет',
            'report_generated' => 'Отчет сгенерирован',
            'full_profile' => 'Полный профиль',
        ],

        'en' => [
            'candidate_report' => 'Candidate Report',
            'reduced_report' => 'Reduced Candidate Report',
            'reduced_version' => '(reduced version)',
            'full_name' => 'Full Name',
            'birth_date' => 'Date of Birth',
            'birth_place' => 'Place of Birth',
            'age' => 'Age',
            'years' => 'years',
            'year_singular' => 'year',
            'years_2_4' => 'years',
            'years_many' => 'years',
            'hour_singular' => 'hour',
            'hours_2_4' => 'hours',
            'hours_many' => 'hours',
            'gender' => 'Gender',
            'nationality' => 'Nationality',
            'city' => 'City',
            'phone' => 'Phone',
            'email' => 'Email',
            'marital_status' => 'Marital Status',
            'driving_license' => 'Driving License',
            'religion' => 'Religion',
            'religious_practice' => 'Religious Practice',
            'yes' => 'Yes',
            'no' => 'No',
            'photo' => 'Photo',
            'candidate_photo' => 'Candidate Photo',

            // Main info
            'main_info' => 'Main Information',
            'desired_position' => 'Desired Position',
            'expected_salary' => 'Expected Salary',
            'professional_education' => 'Professional Education',
            'workplace_preferences' => 'Workplace Preferences',

            // Sections
            'work_experience' => 'Work Experience',
            'work_experience_not_specified' => 'Work experience not specified',
            'companies_positions' => 'Companies and Positions',
            'total_experience' => 'Total Experience',
            'total_experience_years' => 'Total Work Experience (years)',
            'job_satisfaction' => 'Job Satisfaction',
            'job_satisfaction_out_of_5' => 'Job satisfaction (out of 5)',
            'awards_achievements' => 'Awards and Achievements',
            'interests_development' => 'Interests and Development',
            'hobbies' => 'Hobbies',
            'interests' => 'Interests',
            'goals_5_years' => '5-Year Goals',
            'ideal_job' => 'Ideal Job',
            'strengths' => 'Strengths',
            'weaknesses' => 'Weaknesses',
            'achievements' => 'Achievements',
            'books_per_year' => 'Books per Year',
            'entertainment_hours' => 'Entertainment hours per week',
            'educational_hours' => 'Educational hours per week',
            'social_media_hours' => 'Social media hours per week',
            'visited_countries' => 'Visited Countries',
            'favorite_sports' => 'Favorite Sports',

            // Education
            'education' => 'Education',
            'school' => 'School',
            'university' => 'University',
            'specialization' => 'Specialization',
            'graduation_year' => 'Graduation Year',
            'degree' => 'Degree',
            'gpa' => 'GPA',

            // Family
            'family' => 'Family',
            'parents' => 'Parents',
            'siblings' => 'Number of Siblings',
            'children' => 'Children',
            'brother_short' => 'B',
            'sister_short' => 'S',
            'male_short' => 'M',

            // Skills
            'language_skills' => 'Language Skills',
            'language_skills_not_specified' => 'Language skills not specified',
            'computer_skills' => 'Computer Skills',

            // Psychometrics
            'psychometric_data' => 'Psychometric Data',
            'mbti_type' => 'MBTI Personality Type',
            'gallup_talents' => 'Gallup Talents',
            'gardner_intelligence' => 'Gardner Intelligence Types',

            // Gardner intelligence types
            'linguistic_intelligence' => 'Linguistic Intelligence',
            'logical_mathematical_intelligence' => 'Logical-Mathematical Intelligence',
            'musical_intelligence' => 'Musical Intelligence',
            'bodily_kinesthetic_intelligence' => 'Bodily-Kinesthetic Intelligence',
            'spatial_intelligence' => 'Spatial Intelligence',
            'interpersonal_intelligence' => 'Interpersonal Intelligence',
            'intrapersonal_intelligence' => 'Intrapersonal Intelligence',
            'naturalistic_intelligence' => 'Naturalistic Intelligence',
            'existential_intelligence' => 'Existential Intelligence',
            'intelligence' => 'Intelligence',

            // Other
            'not_specified' => 'Not specified',
            'date_filled' => 'Date Filled',
            'available' => 'Yes',
            'not_available' => 'No',
            'report_generated' => 'Report generated',
            'full_profile' => 'Full Profile',
        ],

        'ar' => [
            'candidate_report' => 'تقرير المرشح',
            'reduced_report' => 'تقرير مختصر للمرشح',
            'reduced_version' => '(نسخة مختصرة)',
            'full_name' => 'الاسم الكامل',
            'birth_date' => 'تاريخ الميلاد',
            'birth_place' => 'مكان الميلاد',
            'age' => 'العمر',
            'years' => 'سنوات',
            'year_singular' => 'سنة',
            'years_2_4' => 'سنوات',
            'years_many' => 'سنوات',
            'hour_singular' => 'ساعة',
            'hours_2_4' => 'ساعات',
            'hours_many' => 'ساعات',
            'gender' => 'الجنس',
            'nationality' => 'الجنسية',
            'city' => 'المدينة',
            'phone' => 'الهاتف',
            'email' => 'البريد الإلكتروني',
            'marital_status' => 'الحالة الاجتماعية',
            'driving_license' => 'رخصة القيادة',
            'religion' => 'الديانة',
            'religious_practice' => 'الممارسة الدينية',
            'yes' => 'نعم',
            'no' => 'لا',
            'photo' => 'صورة',
            'candidate_photo' => 'صورة المرشح',

            // Main info
            'main_info' => 'المعلومات الأساسية',
            'desired_position' => 'المنصب المطلوب',
            'expected_salary' => 'الراتب المتوقع',
            'professional_education' => 'التعليم المهني',
            'workplace_preferences' => 'تفضيلات مكان العمل',

            // Sections
            'work_experience' => 'الخبرة العملية',
            'work_experience_not_specified' => 'الخبرة العملية غير محددة',
            'companies_positions' => 'الشركات والمناصب',
            'total_experience' => 'إجمالي الخبرة',
            'total_experience_years' => 'إجمالي سنوات الخبرة',
            'job_satisfaction' => 'الرضا الوظيفي',
            'job_satisfaction_out_of_5' => 'الرضا الوظيفي (من 5)',
            'awards_achievements' => 'الجوائز والإنجازات',
            'interests_development' => 'الاهتمامات والتطوير',
            'hobbies' => 'الهوايات',
            'interests' => 'الاهتمامات',
            'goals_5_years' => 'أهداف الخمس سنوات',
            'ideal_job' => 'الوظيفة المثالية',
            'strengths' => 'نقاط القوة',
            'weaknesses' => 'نقاط الضعف',
            'achievements' => 'الإنجازات',
            'books_per_year' => 'الكتب في السنة',
            'entertainment_hours' => 'ساعات الترفيه أسبوعياً',
            'educational_hours' => 'ساعات التعليم أسبوعياً',
            'social_media_hours' => 'ساعات التواصل الاجتماعي أسبوعياً',
            'visited_countries' => 'الدول التي زارها',
            'favorite_sports' => 'الرياضات المفضلة',

            // Education
            'education' => 'التعليم',
            'school' => 'المدرسة',
            'university' => 'الجامعة',
            'specialization' => 'التخصص',
            'graduation_year' => 'سنة التخرج',
            'degree' => 'الدرجة',
            'gpa' => 'المعدل التراكمي',

            // Family
            'family' => 'العائلة',
            'parents' => 'الوالدين',
            'siblings' => 'عدد الإخوة',
            'children' => 'الأطفال',
            'brother_short' => 'أخ',
            'sister_short' => 'أخت',
            'male_short' => 'ذ',

            // Skills
            'language_skills' => 'المهارات اللغوية',
            'language_skills_not_specified' => 'المهارات اللغوية غير محددة',
            'computer_skills' => 'مهارات الحاسوب',

            // Psychometrics
            'psychometric_data' => 'البيانات النفسية',
            'mbti_type' => 'نوع الشخصية MBTI',
            'gallup_talents' => 'مواهب غالوب',
            'gardner_intelligence' => 'أنواع الذكاء لغاردنر',

            // Gardner intelligence types
            'linguistic_intelligence' => 'الذكاء اللغوي',
            'logical_mathematical_intelligence' => 'الذكاء المنطقي الرياضي',
            'musical_intelligence' => 'الذكاء الموسيقي',
            'bodily_kinesthetic_intelligence' => 'الذكاء الجسدي الحركي',
            'spatial_intelligence' => 'الذكاء المكاني',
            'interpersonal_intelligence' => 'الذكاء الاجتماعي',
            'intrapersonal_intelligence' => 'الذكاء الذاتي',
            'naturalistic_intelligence' => 'الذكاء الطبيعي',
            'existential_intelligence' => 'الذكاء الوجودي',
            'intelligence' => 'الذكاء',

            // Other
            'not_specified' => 'غير محدد',
            'date_filled' => 'تاريخ التعبئة',
            'available' => 'نعم',
            'not_available' => 'لا',
            'report_generated' => 'تم إنشاء التقرير',
            'full_profile' => 'الملف الكامل',
        ],
    ];

    public static function get(string $key, string $language = 'ru'): string
    {
        return self::$labels[$language][$key] ?? self::$labels['ru'][$key] ?? $key;
    }

    public static function all(string $language = 'ru'): array
    {
        return self::$labels[$language] ?? self::$labels['ru'];
    }

    public static function getDirection(string $language): string
    {
        return $language === 'ar' ? 'rtl' : 'ltr';
    }
}
