<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=900">
    <title>@if($isReducedReport)Урезанный отчет о кандидате@elseОтчет о кандидате@endif - {{ $candidate->full_name }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('mini-logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        @page {
            margin: 10mm 0mm 10mm 0mm !important;
        }
        html {
            -webkit-text-size-adjust: 100%;
            -moz-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            text-size-adjust: 100%;
        }
        body {
            /*padding-left: 10mm !important;*/
            /*padding-right: 10mm !important;*/
            margin: 0 !important;
            -webkit-text-size-adjust: 100%;
            -moz-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
            text-size-adjust: 100%;
        }
        * {
            margin: 0;
            padding: 0;
        }
        @media print {
            body { font-size: 15px; }
            .no-print { display: none; }
        }

        /* Предотвращение разрыва текста между страницами */
        .mb-8, .data-row, .flex.items-start, .space-y-1 > div {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        /* Не разрывать записи опыта работы */
        [style*="display: flex"][style*="gap: 24px"] {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        /* Заголовки секций не отрываются от содержимого */
        h2 {
            page-break-after: avoid;
            break-after: avoid;
        }

        .logo-header {
            background: transparent;
        }

        /* Fallback TailwindCSS базовые стили если основные не загрузились */
        .bg-gray-50 { background-color: #f9fafb; }
        .bg-white { background-color: #ffffff; }
        .bg-gray-100 { background-color: #f3f4f6; }
        .text-gray-800 { color: #1f2937; }
        .text-gray-600 { color: #4b5563; }
        .text-gray-500 { color: #6b7280; }
        .text-blue-600 { color: #2563eb; }
        body { font-family: 'Montserrat', sans-serif; }
        .font-sans { font-family: 'Montserrat', sans-serif; }
        .font-bold { font-weight: 700; }
        .font-medium { font-weight: 500; }
        .text-lg { font-size: 1.125rem; line-height: 1.75rem; }
        .text-sm { font-size: 0.875rem; line-height: 1.25rem; }
        .text-xs { font-size: 0.75rem; line-height: 1rem; }
        .text-3xl { font-size: 1.875rem; line-height: 2.25rem; }
        .text-xl { font-size: 1.25rem; line-height: 1.75rem; }
        .max-w-4xl { max-width: 56rem; }
        .mx-auto { margin-left: auto; margin-right: auto; }
        .shadow-lg { box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); }
        .p-6 { padding: 1.5rem; }
        .p-4 { padding: 1rem; }
        .p-3 { padding: 0.75rem; }
        .mb-4 { margin-bottom: 1rem; }
        .mb-8 { margin-bottom: 2rem; }
        .mb-6 { margin-bottom: 1.5rem; }
        .ml-8 { margin-left: 2rem; }
        .mt-4 { margin-top: 1rem; }
        .mb-1 { margin-bottom: 0.25rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .flex { display: flex; }
        .grid { display: grid; }
        .grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .gap-6 { gap: 1.5rem; }
        .gap-8 { gap: 2rem; }
        .space-y-3 > :not([hidden]) ~ :not([hidden]) { margin-top: 0.75rem; }
        .space-y-2 > :not([hidden]) ~ :not([hidden]) { margin-top: 0.5rem; }
        .space-y-1 > :not([hidden]) ~ :not([hidden]) { margin-top: 0.25rem; }
        .items-start { align-items: flex-start; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .justify-center { justify-content: center; }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .w-150 { width: 37.5rem; }
        .w-40 { width: 10rem; }
        .w-20 { width: 5rem; }
        .w-32 { width: 8rem; }
        .w-8 { width: 2rem; }
        .w-72 { width: 18rem; }
        .w-48 { width: 12rem; }
        .w-60 { width: 19rem; }
        .w-auto { width: auto; }
        .h-14 { height: 3.5rem; }
        .h-4 { height: 1.5rem; }
        .h-8 { height: 2rem; }
        .h-12 { height: 3rem; }
        .h-90 { height: 22.5rem; }
        .h-60 { height: 15rem; }
        .h-80 { height: 20rem; }
        .w-64 { width: 16rem; }
        .w-47 { width: 11.75rem; }
        .h-69 { height: 17.25rem; }
        .flex-1 { flex: 1 1 0%; }
        .flex-shrink-0 { flex-shrink: 0; }
        .border-b { border-bottom-width: 1px; }
        .border-2 { border-width: 2px; }
        .border-gray-200 { border-color: #e5e7eb; }
        .border-gray-300 { border-color: #d1d5db; }
        .rounded { border-radius: 0.25rem; }
        .object-cover { object-fit: cover; }
        .block { display: block; }
        .bg-gray-50 { background-color: #f9fafb; }
        .bg-blue-500 { background-color: #3b82f6; }
        .bg-gray-200 { background-color: #e5e7eb; }
        .bg-green-50 { background-color: #f0fdf4; }
        .bg-green-500 { background-color: #22c55e; }
        .text-gray-700 { color: #374151; }
        .text-green-800 { color: #166534; }
        .text-green-600 { color: #16a34a; }
        .text-green-700 { color: #15803d; }
        .border { border-width: 1px; }
        .border-2 { border-width: 2px; }
        .border-gray-200 { border-color: #e5e7eb; }
        .border-green-200 { border-color: #bbf7d0; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .rounded-full { border-radius: 9999px; }
        .mr-3 { margin-right: 0.75rem; }
        .mt-1 { margin-top: 0.25rem; }
        .flex-col { flex-direction: column; }
        .shadow-md { box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); }
        .font-extrabold { font-weight: 800; }
        .float-right { float: right; }
        .clear-both { clear: both; }
        .ml-6 { margin-left: 1.5rem; }

        /* Стили для PDF содержимого */
        .pdf-content {
            line-height: 1.6;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .pdf-content p {
            margin-bottom: 0.5rem;
        }
        .bg-red-50 { background-color: #fef2f2; }
        .border-red-200 { border-color: #fecaca; }
        .text-red-600 { color: #dc2626; }
        .leading-relaxed { line-height: 1.625; }
        .hover\:text-blue-800:hover { color: #1e40af; }

        /* Стили для PDF изображений */
        .pdf-images-container {
            margin-top: 1rem;
        }

        .pdf-page-container {
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            overflow: hidden;
            background-color: #ffffff;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }

        .pdf-page-header {
            background-color: #f8fafc;
            border-bottom: 1px solid #e5e7eb;
            padding: 0.75rem 1rem;
            font-weight: 500;
            color: #374151;
        }

        .pdf-image-wrapper {
            padding: 1rem;
            text-align: center;
        }

        .pdf-page-image {
            max-width: 100%;
            height: auto;
            border-radius: 0.375rem;
            box-shadow: 0 2px 8px 0 rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease-in-out;
        }

        .pdf-page-image:hover {
            transform: scale(1.02);
            cursor: pointer;
        }

        /* Стили для переключения видимости */
        .pdf-images-container {
            transition: opacity 0.3s ease-in-out, max-height 0.3s ease-in-out;
        }

        .pdf-images-container.hidden {
            opacity: 0;
            max-height: 0;
            overflow: hidden;
        }

        /* Адаптивные стили для изображений */
        @media (max-width: 768px) {
            .pdf-image-wrapper {
                padding: 0.5rem;
            }

            .pdf-page-image {
                max-width: 100%;
            }
        }

        /* Стили для полноэкранного просмотра */
        .pdf-fullscreen-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.9);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .pdf-fullscreen-image {
            max-width: 90%;
            max-height: 90%;
            border-radius: 0.5rem;
        }

        .pdf-fullscreen-close {
            position: absolute;
            top: 20px;
            right: 30px;
            color: white;
            font-size: 2rem;
            cursor: pointer;
            z-index: 1001;
        }

        /* Стили для переключателя PDF */
        .space-x-2 > * + * {
            margin-left: 0.5rem;
        }

        .pdf-viewer {
            border: 1px solid #e5e7eb;
            border-radius: 0.5rem;
            overflow: hidden;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }

        .text-purple-600 { color: #9333ea; }
        .hover\:text-purple-800:hover { color: #6b21a8; }

        /* Анимация для переключения */
        .pdf-content, .pdf-viewer {
            transition: opacity 0.3s ease-in-out;
        }

        /* Правила для плавного перехода контента между страницами */

        /* Заголовок не отрывается от первой строки */
        h2 {
            page-break-after: avoid;
            break-after: avoid;
        }

        /* Только одиночные строки данных не разрываются пополам */
        .data-row {
            page-break-inside: avoid;
            break-inside: avoid;
        }

        /* Секция "Интересы и развитие" - заполняет страницу */
        .interests-section {
            page-break-inside: auto;
            break-inside: auto;
        }

        /* Каждая строка данных в секции интересов - НЕ разрывается между страницами */
        .interests-section .data-row {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            -webkit-column-break-inside: avoid !important;
        }

        /* Универсальное правило для всех data-row - не разрывать пополам */
        .data-row {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            -webkit-column-break-inside: avoid !important;
        }

        /* Секция "Опыт работы" - не разрывать записи пополам */
        .work-experience-section {
            page-break-inside: auto;
            break-inside: auto;
        }

        /* Каждая запись опыта работы - не разрывается */
        .work-experience-section .work-experience-item {
            display: block !important;
            overflow: hidden !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            -webkit-column-break-inside: avoid !important;
        }

        /* Общий стаж и награды - не разрывать */
        .work-experience-section .work-summary,
        .work-experience-section .work-awards {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            -webkit-column-break-inside: avoid !important;
        }

        /* Старый формат опыта работы */
        .work-experience-section .space-y-1 > div {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            -webkit-column-break-inside: avoid !important;
        }

        /* Секция "Психометрические данные" - не разрывать */
        .psychometric-section {
            display: block !important;
            overflow: hidden !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            -webkit-column-break-inside: avoid !important;
        }

        /* Строка MBTI не должна разрываться */
        .mbti-row {
            display: block !important;
            white-space: nowrap !important;
            page-break-inside: avoid !important;
        }

        /* Секция "Компьютерные навыки" - не разрывать */
        .computer-skills-section {
            display: block !important;
            overflow: hidden !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            -webkit-column-break-inside: avoid !important;
        }

        /* Секция "Виды интеллектов Гарднера" - НЕ разрывать вообще, целиком на следующую страницу */
        .gardner-section {
            display: block !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            -webkit-column-break-inside: avoid !important;
        }

        /* Контейнер графика - не разрывать */
        .gardner-chart-container {
            display: block !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        /* Каждый ряд графика НЕ разрывается */
        .gardner-row {
            display: block !important;
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            overflow: hidden !important;
        }

        /* Универсальное правило - все flex контейнеры с items-start не разрываются */
        .flex.items-start {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            -webkit-column-break-inside: avoid !important;
        }

        /* Все элементы внутри space-y-1 не разрываются */
        .space-y-1 > * {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
            -webkit-column-break-inside: avoid !important;
        }

    </style>
</head>
<body>
@php
if (! function_exists('mb_ucfirst')) {
    function mb_ucfirst(mixed $value): string
    {
        $text = trim((string) ($value ?? ''));
        if ($text === '') {
            return '';
        }
        $lower = mb_strtolower($text, 'UTF-8');
        $first = mb_strtoupper(mb_substr($lower, 0, 1, 'UTF-8'), 'UTF-8');
        return $first . mb_substr($lower, 1, null, 'UTF-8');
    }
}

// Функция для очистки git conflict маркеров из текста
if (! function_exists('clean_git_conflicts')) {
    function clean_git_conflicts(mixed $value): string
    {
        $text = (string) ($value ?? '');
        // Удаляем маркеры git конфликтов
        $text = preg_replace('/<<<<<<< .*?\n?/', '', $text);
        $text = preg_replace('/=======\n?/', '', $text);
        $text = preg_replace('/>>>>>>> .*?\n?/', '', $text);
        return trim($text);
    }
}

// Функция для склонения слова "год" в русском языке
if (! function_exists('pluralize_years')) {
    function pluralize_years(int $number): string
    {
        $absNumber = abs($number);
        $lastTwo = $absNumber % 100;
        $lastOne = $absNumber % 10;

        if ($lastTwo >= 11 && $lastTwo <= 19) {
            return $number . ' лет';
        }

        if ($lastOne === 1) {
            return $number . ' год';
        }

        if ($lastOne >= 2 && $lastOne <= 4) {
            return $number . ' года';
        }

        return $number . ' лет';
    }
}
@endphp

    <div class="max-w-4xl mx-auto bg-white ">
        <!-- Header -->
        <div class="logo-header p-3">
            <div class="flex justify-between items-center">
                <div>
               <img src="data:image/svg+xml;base64,{{ base64_encode(file_get_contents(public_path('logo.svg'))) }}" alt="DIVERGENTS talent laboratory" class="h-14 w-auto">
                </div>
                <div class="text-right">
                    <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('logos/talents_lab_logo.png'))) }}" alt="talents lab" class="h-4 w-auto">
                </div>
            </div>
        </div>

        <!-- Candidate Header -->
        <div class="p-3">
            <div class="mb-8">
                <!-- Фото справа с обтеканием -->
                <div class="float-right ml-6 flex-shrink-0">
                    @if($photoUrl)
                        <img src="{{$photoUrl}}" alt="Фото кандидата" class="w-47 h-69 object-cover rounded border-2 border-gray-300">
{{--                        <img src="data:image/png;base64,{{ base64_encode(file_get_contents($photoUrl)) }}" alt="Фото кандидата" class="w-47 h-69 object-cover rounded border-2 border-gray-300">--}}
                    @else
                        <div class="w-47 h-69 bg-gray-300 rounded border-2 border-gray-300 flex items-center justify-center">
                            <span class="text-gray-500 text-sm">Фото</span>
                        </div>
                    @endif
                    <!-- Дата заполнения под фото -->
                    @if($isFullReport)
                    <div style="text-align: center; margin-top: 8px;">
                        <span style="color: #6b7280; font-size: 11px;">Дата заполнения: {{ $candidate->created_at->format('d.m.Y') }}</span>
                    </div>
                    @endif
                </div>

                <div>
                        @if($isReducedReport)
                            <span class="text-lg text-gray-500 font-normal">(урезанная версия)</span>
                        @endif
                    <h1 class="text-3xl font-bold mb-4" style="color: #39761d; display: flex; align-items: center; gap: 8px;">
                        {{ clean_git_conflicts($candidate->full_name) }}
                        @if($candidate->gallup_pdf || ($candidate->gallup_talents && count($candidate->gallup_talents) > 0))
                            <span style="display: inline-flex; align-items: center; justify-content: center; background: #39761d; color: white; width: 20px; height: 20px; border-radius: 50%; margin-left: 10px; flex-shrink: 0;" title="Полный профиль">
                                <svg style="width: 14px; height: 14px;" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </span>
                        @endif
                    </h1>
                     @if($isFullReport)
                    <div class="text-base mb-6" style="line-height: 1.8;">
                        <div style="display: flex; flex-wrap: wrap; align-items: center; margin-bottom: 8px;">
                            <span class="font-medium text-gray-800" style="display: inline-flex; align-items: center; margin-right: 24px;">
                                <img src="https://cdn.jsdelivr.net/gh/twitter/twemoji@14.0.2/assets/svg/1f4cd.svg" alt="📍" style="width: 16px; height: 16px; margin-right: 6px;">
                                {{ mb_ucfirst(clean_git_conflicts($candidate->current_city)) }}
                            </span>
                            <span class="font-medium text-gray-800" style="display: inline-flex; align-items: center;">
                                <img src="https://cdn.jsdelivr.net/gh/twitter/twemoji@14.0.2/assets/svg/1f4e7.svg" alt="📧" style="width: 16px; height: 16px; margin-right: 6px;">
                                {{ $candidate->email }}
                            </span>
                        </div>
                        <div style="display: flex; flex-wrap: wrap; align-items: center;">
                            <span class="font-medium text-gray-800" style="display: inline-flex; align-items: center; margin-right: 24px;">
                                <img src="https://cdn.jsdelivr.net/gh/twitter/twemoji@14.0.2/assets/svg/1f4de.svg" alt="📞" style="width: 16px; height: 16px; margin-right: 6px;">
                                {{ $candidate->phone }}
                            </span>
                            @if($candidate->instagram)
                                <span class="font-medium text-gray-800" style="display: inline-flex; align-items: center;">
                                    <svg style="width: 16px; height: 16px; margin-right: 6px;" viewBox="0 0 24 24" fill="#E4405F"><path d="M12 0C8.74 0 8.333.015 7.053.072 5.775.132 4.905.333 4.14.63c-.789.306-1.459.717-2.126 1.384S.935 3.35.63 4.14C.333 4.905.131 5.775.072 7.053.012 8.333 0 8.74 0 12s.015 3.667.072 4.947c.06 1.277.261 2.148.558 2.913.306.788.717 1.459 1.384 2.126.667.666 1.336 1.079 2.126 1.384.766.296 1.636.499 2.913.558C8.333 23.988 8.74 24 12 24s3.667-.015 4.947-.072c1.277-.06 2.148-.262 2.913-.558.788-.306 1.459-.718 2.126-1.384.666-.667 1.079-1.335 1.384-2.126.296-.765.499-1.636.558-2.913.06-1.28.072-1.687.072-4.947s-.015-3.667-.072-4.947c-.06-1.277-.262-2.149-.558-2.913-.306-.789-.718-1.459-1.384-2.126C21.319 1.347 20.651.935 19.86.63c-.765-.297-1.636-.499-2.913-.558C15.667.012 15.26 0 12 0zm0 2.16c3.203 0 3.585.016 4.85.071 1.17.055 1.805.249 2.227.415.562.217.96.477 1.382.896.419.42.679.819.896 1.381.164.422.36 1.057.413 2.227.057 1.266.07 1.646.07 4.85s-.015 3.585-.074 4.85c-.061 1.17-.256 1.805-.421 2.227-.224.562-.479.96-.899 1.382-.419.419-.824.679-1.38.896-.42.164-1.065.36-2.235.413-1.274.057-1.649.07-4.859.07-3.211 0-3.586-.015-4.859-.074-1.171-.061-1.816-.256-2.236-.421-.569-.224-.96-.479-1.379-.899-.421-.419-.69-.824-.9-1.38-.165-.42-.359-1.065-.42-2.235-.045-1.26-.061-1.649-.061-4.844 0-3.196.016-3.586.061-4.861.061-1.17.255-1.814.42-2.234.21-.57.479-.96.9-1.381.419-.419.81-.689 1.379-.898.42-.166 1.051-.361 2.221-.421 1.275-.045 1.65-.06 4.859-.06l.045.03zm0 3.678c-3.405 0-6.162 2.76-6.162 6.162 0 3.405 2.76 6.162 6.162 6.162 3.405 0 6.162-2.76 6.162-6.162 0-3.405-2.757-6.162-6.162-6.162zM12 16c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4zm7.846-10.405c0 .795-.646 1.44-1.44 1.44-.795 0-1.44-.646-1.44-1.44 0-.794.646-1.439 1.44-1.439.793-.001 1.44.645 1.44 1.439z"/></svg>
                                    {{ $candidate->instagram }}
                                </span>
                            @endif
                        </div>
                    </div>
                     @endif
                    <h2 class="text-xl font-bold text-gray-800 mb-2">Основная информация</h2>
                     <!-- Основная информация -->
                     <div class="space-y-1">
                        <div class="flex items-start">
                            <span class="w-60 text-base text-gray-600">Желаемая должность:</span>
                            <span class="text-base font-medium flex-1">
                                @if($candidate->desired_positions && is_array($candidate->desired_positions) && count(array_filter($candidate->desired_positions)) > 0)
                                    {{ implode(' / ', array_filter($candidate->desired_positions)) }}
                                @elseif($candidate->desired_position)
                                    {{ $candidate->desired_position }}
                                @else
                                    Не указано
                                @endif
                            </span>
                        </div>
                         <div class="flex data-row">
                             <span class="w-60 text-base text-gray-600">Ожидаемая заработная плата:</span>
                             <span class="text-base font-medium">{{ $candidate->formatted_salary_range }}</span>
                         </div>
                         <div class="flex data-row">
                             <span class="w-60 text-base text-gray-600">Дата рождения:</span>
                             <span class="text-base font-medium">{{ $candidate->birth_date?->format('d.m.Y') ?: 'Не указано' }}</span>
                         </div>
                         <div class="flex items-start data-row">
                             <span class="w-60 text-base text-gray-600">Место рождения:</span>
                             <span class="text-base font-medium flex-1">{{ $candidate->birth_place ?: 'Не указано' }}</span>
                         </div>
                         <div class="flex data-row">
                             <span class="w-60 text-base text-gray-600">Пол:</span>
                             <span class="text-base font-medium">{{ $candidate->gender ?: 'Не указано' }}</span>
                         </div>
                         <div class="flex data-row">
                             <span class="w-60 text-base text-gray-600">Семейное положение:</span>
                             <span class="text-base font-medium">{{ $candidate->marital_status ?: 'Не указано' }}</span>
                         </div>
                         @php
                             $family = $candidate->getFamilyStructured();
                         @endphp

                         @if($isFullReport)
                         <!-- Дети -->
                         <div class="flex items-start">
                             <span class="w-60 text-base text-gray-600">Дети:</span>
                             <span class="text-base font-medium flex-1">
                                 @if(!empty($family['children']) && count($family['children']) > 0)
                                     {{ count($family['children']) }}
                                     @foreach($family['children'] as $child)
                                         ({{ $child['gender'] ?? 'М' }}{{ $child['birth_year'] ?? '' }})
                                     @endforeach
                                 @else
                                     0
                                 @endif
                             </span>
                         </div>
                         @endif

                        @if($isFullReport)
                        <!-- Родители -->
                        <div class="flex items-start">
                            <span class="w-60 text-base text-gray-600">Родители:</span>
                            <span class="text-base font-medium flex-1">
                                @if(!empty($family['parents']))
                                    @foreach($family['parents'] as $parent)
                                        <div>{{ $parent['relation'] ?? 'Не указано' }} - {{ $parent['birth_year'] ?? 'Не указано' }}{{ !empty($parent['profession']) ? ' - ' . $parent['profession'] : '' }}</div>
                                    @endforeach
                                @else
                                    Не указано
                                @endif
                            </span>
                        </div>
                        @endif

                         @if($isFullReport)
                         <!-- Братья и сестры -->
                         <div class="flex items-start">
                             <span class="w-60 text-base text-gray-600">Кол-во братьев/сестер:</span>
                             <span class="text-base font-medium flex-1">
                                 @if(!empty($family['siblings']))
                                     {{ count($family['siblings']) }}
                                     @foreach($family['siblings'] as $sibling)
                                         ({{ ($sibling['relation'] ?? 'Не указано') === 'Брат' ? 'Б' : 'С' }}{{ $sibling['birth_year'] ?? '' }})
                                     @endforeach
                                 @else
                                     Не указано
                                 @endif
                             </span>
                         </div>
                         @endif

                        <!-- Школа -->
                         <div class="flex items-start">
                             <span class="w-60 text-base text-gray-600">Школа:</span>
                             <span class="text-base font-medium flex-1">{{ $candidate->school ?: 'Не указано' }}</span>
                         </div>

                        <!-- Образование -->
                        <div class="flex items-start">
                             <span class="w-60 text-base text-gray-600">Профессиональное образование:</span>
                             <span class="text-base font-medium flex-1">
                                @if($candidate->universities && count($candidate->universities) > 0)
                                    @foreach($candidate->universities as $index => $university)
                                        @php
                                            $parts = [];
                                            $parts[] = mb_ucfirst($university['name'] ?? 'Не указано');
                                            if (!empty($university['city'])) $parts[] = mb_ucfirst($university['city']);
                                            $parts[] = $university['graduation_year'] ?? 'Не указано';
                                            $parts[] = mb_ucfirst($university['speciality'] ?? 'Не указано');
                                            if (!empty($university['degree'])) $parts[] = $university['degree'];
                                            if (!empty($university['gpa'])) $parts[] = $university['gpa'];
                                        @endphp
                                        <div>{{ implode(' / ', $parts) }}</div>
                                    @endforeach
                                @else
                                    Не указано
                                @endif
                            </span>
                         </div>
                    </div>
               </div>

               <div class="clear-both"></div>
            </div>
        </div>

        <!-- Main Content -->
        <div style="padding: 0 12px 12px 12px;">
            <!-- Опыт работы -->
            <div class="mb-8 work-experience-section">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Опыт работы</h2>
                @if($candidate->work_experience && count($candidate->work_experience) > 0)
                    @php
                        // Проверяем, есть ли у кандидата новые поля (main_tasks или activity_sphere)
                        $hasNewFields = false;
                        foreach($candidate->work_experience as $exp) {
                            if (!empty($exp['activity_sphere']) ||
                                (!empty($exp['main_tasks']) && is_array($exp['main_tasks']) && count(array_filter($exp['main_tasks'])) > 0)) {
                                $hasNewFields = true;
                                break;
                            }
                        }
                    @endphp

                    @if($hasNewFields)
                        {{-- Новый дизайн для анкет с заполненными main_tasks/activity_sphere --}}
                        @foreach($candidate->work_experience as $index => $experience)
                            <div class="work-experience-item" style="{{ !$loop->last ? 'margin-bottom: 16px; padding-bottom: 16px; border-bottom: 1px solid #e5e7eb;' : '' }}">
                                <div class="flex items-start">
                                    {{-- Левая колонка: информация о месте работы --}}
                                    <div class="w-60" style="flex-shrink: 0; padding-right: 4px;">
                                        {{-- Дата --}}
                                        <div style="color: #234088; font-size: 14px; font-weight: 500; margin-bottom: 4px;">
                                            {{ $experience['years'] ?? '' }}
                                        </div>
                                        {{-- Должность --}}
                                        <div style="color: #000000; font-weight: 600; font-size: 17px; margin-bottom: 2px;">
                                            {{ mb_ucfirst($experience['position'] ?? 'Не указано') }}
                                        </div>
                                        {{-- Компания / Город --}}
                                        <div style="color: #000000; font-weight: 600; font-size: 15px;">
                                            {{ mb_ucfirst($experience['company'] ?? 'Не указано') }}@if(!empty($experience['city'])), {{ mb_ucfirst($experience['city']) }}@endif
                                        </div>
                                        {{-- Сфера деятельности --}}
                                        @if(!empty($experience['activity_sphere']))
                                            <div style="color: #6b7280; font-size: 13px; margin-top: 2px;">
                                                {{ trim($experience['activity_sphere']) }}
                                            </div>
                                        @endif
                                    </div>
                                    {{-- Правая колонка: основные обязанности --}}
                                    <div class="flex-1">
                                        @if(!empty($experience['main_tasks']) && is_array($experience['main_tasks']) && count(array_filter($experience['main_tasks'])) > 0)
                                            @foreach(array_filter($experience['main_tasks']) as $task)
                                                <div style="margin-bottom: 4px; color: #000000; font-size: 14px; font-weight: 500;">
                                                    <span style="color: #9ca3af; margin-right: 8px;">•</span>{{ mb_ucfirst($task) }}
                                                </div>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        {{-- Общий стаж и удовлетворённость --}}
                        <div class="work-summary" style="margin-top: 16px;">
                            <div style="display: flex; align-items: center; margin-bottom: 4px;">
                                <span style="color: #000000; font-size: 14px; font-weight: 500; margin-right: 8px;">Общий стаж:</span>
                                <span style="color: #000000; font-weight: 500; font-size: 14px;">{{ pluralize_years($candidate->total_experience_years ?? 0) }}</span>
                            </div>
                            <div style="display: flex; align-items: center;">
                                <span style="color: #000000; font-size: 14px; font-weight: 500; margin-right: 8px;">Удовлетворённость работой:</span>
                                <span style="color: #000000; font-weight: 500; font-size: 14px;">{{ $candidate->job_satisfaction ?? '—' }}/5</span>
                            </div>
                        </div>

                        {{-- Награды и достижения --}}
                        @if($candidate->awards && is_array($candidate->awards) && count(array_filter($candidate->awards)) > 0)
                            <div class="work-awards" style="margin-top: 16px;">
                                <span style="color: #000000; font-weight: 700; font-size: 16px; display: block; margin-bottom: 8px;">Награды и достижения</span>
                                <ul style="margin: 0; padding: 0; list-style: none;">
                                    @foreach(array_filter($candidate->awards) as $award)
                                        <li style="display: flex; align-items: flex-start; margin-bottom: 4px; color: #374151; font-size: 14px;">
                                            <span style="color: #9ca3af; margin-right: 8px;">•</span>
                                            <span>{{ $award }}</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    @else
                        {{-- Старый компактный дизайн для анкет без новых полей --}}
                        <div class="space-y-1">
                            <div class="flex items-start">
                                <span class="w-60 text-base text-gray-600">Компании и должности:</span>
                                <div class="text-base font-medium flex-1 space-y-1">
                                    @foreach($candidate->work_experience as $index => $experience)
                                        <div>
                                            {{ $index + 1 }}.
                                            @php $years = trim($experience['years'] ?? ''); @endphp
                                            @if($years !== '')
                                                {{ implode(' - ', array_map('mb_ucfirst', explode(' - ', $years))) }} -
                                            @endif
                                            {{ mb_ucfirst($experience['company'] ?? 'Не указано') }}
                                            @if(!empty($experience['city']))
                                                ({{ mb_ucfirst($experience['city']) }})
                                            @endif
                                             - {{ mb_ucfirst($experience['position'] ?? 'Не указано') }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="flex data-row" style="margin-top: 1rem;">
                                <span class="w-60 text-base text-gray-600">Общий стаж работы (лет):</span>
                                <span class="text-base font-medium">{{ $candidate->total_experience_years ?? 0 }}</span>
                            </div>
                            <div class="flex data-row">
                                <span class="w-60 text-base text-gray-600">Любит свою работу на (из 5):</span>
                                <span class="text-base font-medium">{{ $candidate->job_satisfaction ?? 'Не указано' }}</span>
                            </div>
                        </div>
                    @endif
                @else
                    <p class="text-base text-gray-500">Опыт работы не указан</p>
                @endif
            </div>

            <!-- Интересы и развитие -->
            <div class="mb-8 interests-section">
                <h2 class="text-xl font-bold text-gray-800 mb-2">Интересы и развитие</h2>
                <div class="space-y-1">
                    <!-- 1. Хобби -->
                    <div class="flex items-start data-row">
                        <span class="w-60 text-base text-gray-600">Хобби:</span>
                        <span class="text-base font-medium flex-1">
                            @php
                                $hobbies = trim($candidate->hobbies ?? '');
                                if ($hobbies !== '') {
                                    $hLower = mb_strtolower($hobbies, 'UTF-8');
                                    $hFirst = mb_strtoupper(mb_substr($hLower, 0, 1, 'UTF-8'), 'UTF-8');
                                    $hRest = mb_substr($hLower, 1, null, 'UTF-8');
                                    $hobbies = $hFirst . $hRest;
                                } else {
                                    $hobbies = 'Не указано';
                                }
                            @endphp
                            {{ $hobbies }}
                        </span>
                    </div>
                    <!-- 2. Интересы -->
                    <div class="flex items-start data-row">
                        <span class="w-60 text-base text-gray-600">Интересы:</span>
                        <span class="text-base font-medium flex-1">
                            @php
                                $interests = trim($candidate->interests ?? '');
                                if ($interests !== '') {
                                    $lower = mb_strtolower($interests, 'UTF-8');
                                    $first = mb_strtoupper(mb_substr($lower, 0, 1, 'UTF-8'), 'UTF-8');
                                    $rest = mb_substr($lower, 1, null, 'UTF-8');
                                    $interests = $first . $rest;
                                } else {
                                    $interests = 'Не указано';
                                }
                            @endphp
                            {{ $interests }}
                        </span>
                    </div>
                    <!-- 3. Любимые виды спорта -->
                    <div class="flex items-start data-row">
                        <span class="w-60 text-base text-gray-600">Любимые виды спорта:</span>
                        <span class="text-base font-medium flex-1">
                            @php
                                $sports = $candidate->favorite_sports ?? '';
                                if (!empty($sports)) {
                                    if (is_array($sports)) {
                                        // Для каждого элемента массива: первая буква заглавная, остальные маленькие
                                        $sports = implode(', ', array_map(function($s) {
                                            $s = trim($s);
                                            if ($s === '') return '';
                                            $lower = mb_strtolower($s, 'UTF-8');
                                            return mb_strtoupper(mb_substr($lower, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($lower, 1, null, 'UTF-8');
                                        }, $sports));
                                    } else {
                                        // Для строки: первая буква заглавная, остальные маленькие
                                        $sports = trim($sports);
                                        $lower = mb_strtolower($sports, 'UTF-8');
                                        $sports = mb_strtoupper(mb_substr($lower, 0, 1, 'UTF-8'), 'UTF-8') . mb_substr($lower, 1, null, 'UTF-8');
                                    }
                                } else {
                                    $sports = 'Не указано';
                                }
                            @endphp
                            {{ $sports }}
                        </span>
                    </div>
                    <!-- 4. Посещенные страны -->
                    <div class="flex items-start data-row">
                        <span class="w-60 text-base text-gray-600">Посещенные страны:</span>
                        <span class="text-base font-medium flex-1">
                            @if($candidate->visited_countries)
                                @if(is_array($candidate->visited_countries))
                                    {{ implode(', ', $candidate->visited_countries) }}
                                @else
                                    {{ $candidate->visited_countries }}
                                @endif
                            @else
                                Не указано
                            @endif
                        </span>
                    </div>
                    <!-- 5. Кол-во книг в год -->
                    <div class="flex data-row">
                        <span class="w-60 text-base text-gray-600">Кол-во книг в год:</span>
                        <span class="text-base font-medium">{{ $candidate->books_per_year ?? 'Не указано' }}</span>
                    </div>
                    <!-- 6-7. Вероисповедание и Рел. практика -->
                    @if($isFullReport)
                    <div class="flex data-row">
                        <span class="w-60 text-base text-gray-600">Религия:</span>
                        <span class="text-base font-medium">{{ $candidate->religion ?: 'Не указано' }}</span>
                    </div>
                    <div class="flex data-row">
                        <span class="w-60 text-base text-gray-600">Рел. практика:</span>
                        <span class="text-base font-medium">{{ $candidate->is_practicing ? 'Да' : 'Нет' }}</span>
                    </div>
                    @endif
                    <!-- 8. Часы на разв. видео в неделю -->
                    <div class="flex data-row">
                        <span class="w-60 text-base text-gray-600">Часы на разв. видео в неделю:</span>
                        <span class="text-base font-medium">
                            @if($candidate->entertainment_hours_weekly)
                                {{ $candidate->entertainment_hours_weekly }} час{{ $candidate->entertainment_hours_weekly == 1 ? '' : ($candidate->entertainment_hours_weekly < 5 ? 'а' : 'ов') }}
                            @else
                                Не указано
                            @endif
                        </span>
                    </div>
                    <!-- 9. Часы на обра. видео в неделю -->
                    <div class="flex data-row">
                        <span class="w-60 text-base text-gray-600">Часы на обра. видео в неделю:</span>
                        <span class="text-base font-medium">
                            @if($candidate->educational_hours_weekly)
                                {{ $candidate->educational_hours_weekly }} час{{ $candidate->educational_hours_weekly == 1 ? '' : ($candidate->educational_hours_weekly < 5 ? 'а' : 'ов') }}
                            @else
                                Не указано
                            @endif
                        </span>
                    </div>
                    <!-- 10. Часы на соц. сети в неделю -->
                    <div class="flex data-row">
                        <span class="w-60 text-base text-gray-600">Часы на соц. сети в неделю:</span>
                        <span class="text-base font-medium">
                            @if($candidate->social_media_hours_weekly)
                                {{ $candidate->social_media_hours_weekly }} час{{ $candidate->social_media_hours_weekly == 1 ? '' : ($candidate->social_media_hours_weekly < 5 ? 'а' : 'ов') }}
                            @else
                                Не указано
                            @endif
                        </span>
                    </div>
                    <!-- 11. Водительские права -->
                    <div class="flex data-row">
                        <span class="w-60 text-base text-gray-600">Водительские права:</span>
                        <span class="text-base font-medium">{{ $candidate->has_driving_license ? 'Есть' : 'Нет' }}</span>
                    </div>
                    <!-- 12. Пожелания на рабочем месте -->
                    <div class="flex items-start data-row">
                        <span class="w-60 text-base text-gray-600">Пожелания на рабочем месте:</span>
                        <span class="text-base font-medium flex-1">
                            @php
                                $workplace = trim($candidate->employer_requirements ?? '');
                                if ($workplace !== '') {
                                    $wLower = mb_strtolower($workplace, 'UTF-8');
                                    $wFirst = mb_strtoupper(mb_substr($wLower, 0, 1, 'UTF-8'), 'UTF-8');
                                    $wRest = mb_substr($wLower, 1, null, 'UTF-8');
                                    $workplace = $wFirst . $wRest;
                                } else {
                                    $workplace = 'Не указано';
                                }
                            @endphp
                            {{ $workplace }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Языковые навыки -->
            <div class="mb-8">
                <h2 class="text-xl font-bold text-gray-800 mb-2">Языковые навыки</h2>
                @if($candidate->language_skills && count($candidate->language_skills) > 0)
                    <div class="space-y-1">
                        @foreach($candidate->language_skills as $skill)
                            <div class="flex text-base">
                                <span class="w-32 font-medium">{{ $skill['language'] ?? 'Не указано' }}</span>
                                <span class="w-40">{{ $skill['level'] ?? 'Не указано' }}</span>
                                @if(isset($skill['additional_language']) && $skill['additional_language'])
                                    <span class="w-32 font-medium">{{ $skill['additional_language'] ?? '' }}</span>
                                    <span class="text-gray-600">-</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-base text-gray-500">Языковые навыки не указаны</p>
                @endif
            </div>

            <!-- Компьютерные навыки -->
            <div class="mb-8 computer-skills-section">
                <h2 class="text-xl font-bold text-gray-800 mb-2">Компьютерные навыки</h2>
                <p class="text-base font-medium">{{ $candidate->computer_skills ?: 'Не указано' }}</p>
            </div>

            <!-- Психометрические данные -->
            <div class="mb-8 psychometric-section" style="page-break-inside: avoid;">
                <h2 class="text-xl font-bold text-gray-800 mb-2">Психометрические данные</h2>
                <div class="mbti-row" style="page-break-inside: avoid; white-space: nowrap;">
                    <span class="text-base text-gray-600" style="display: inline;">Тип личности по MBTI: </span>
                    <span class="text-base font-medium" style="color: #234088; display: inline;">{{ $candidate->mbti_full_name ?: 'Не указано' }}</span>
                </div>
            </div>

            <!-- Полный отчёт с Gallup -->
            @php
                $dpsReport = $candidate->gallupReportByType('DPs');
                $dptReport = $candidate->gallupReportByType('DPT');
                $fmdReport = $candidate->gallupReportByType('FMD');
                $hasAnyGallupReport = $dpsReport || $dptReport || $fmdReport;
            @endphp
            @if($hasAnyGallupReport)
            <div class="mb-8 gallup-reports-section no-print" style="page-break-inside: avoid;">
                <h2 class="text-xl font-bold text-gray-800 mb-4">Полный отчёт</h2>
                <p class="text-gray-600 mb-4">Откройте полный отчёт с Gallup CliftonStrengths данными:</p>
                <a href="{{ route('candidate.anketa.view', $candidate) }}"
                   style="display: inline-flex; align-items: center; gap: 10px; background: linear-gradient(135deg, #234088 0%, #1a3066 100%); color: white; padding: 14px 24px; border-radius: 10px; text-decoration: none; font-size: 16px; font-weight: 600; box-shadow: 0 4px 14px rgba(35, 64, 136, 0.35); transition: all 0.2s;"
                   onmouseover="this.style.transform='translateY(-2px)'; this.style.boxShadow='0 6px 20px rgba(35, 64, 136, 0.45)';"
                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 14px rgba(35, 64, 136, 0.35)';">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M14 2H6c-1.1 0-1.99.9-1.99 2L4 20c0 1.1.89 2 1.99 2H18c1.1 0 2-.9 2-2V8l-6-6zm2 16H8v-2h8v2zm0-4H8v-2h8v2zm-3-5V3.5L18.5 9H13z"/>
                    </svg>
                    Открыть полный отчёт ({{ $candidate->full_name }})
                </a>
            </div>
            @endif

            <!-- Виды интеллектов Гарднера -->
            @if($candidate->user && $candidate->user->gardnerTestResult)
            @php
                $results = $candidate->user->gardnerTestResult->results;

                // Маппинг типов интеллекта на цвета и эмодзи (используем изображения для PDF совместимости)
                $twemojiBase = 'https://cdn.jsdelivr.net/gh/twitter/twemoji@14.0.2/assets/svg/';
                $intelligenceConfig = [
                    'Лингвистический интеллект' => ['color' => '#e06666', 'textColor' => 'black', 'emoji' => '㊗️', 'img' => $twemojiBase . '3297.svg'],
                    'Логико-математический интеллект' => ['color' => '#ea9999', 'textColor' => 'black', 'emoji' => '🧠', 'img' => $twemojiBase . '1f9e0.svg'],
                    'Музыкальный интеллект' => ['color' => '#3c78d8', 'textColor' => 'white', 'emoji' => '🎶', 'img' => $twemojiBase . '1f3b6.svg'],
                    'Телесно-кинестетический интеллект' => ['color' => '#f6b26b', 'textColor' => 'black', 'emoji' => '✋🏻', 'img' => $twemojiBase . '270b-1f3fb.svg'],
                    'Пространственный интеллект' => ['color' => '#38761d', 'textColor' => 'white', 'emoji' => '👁️', 'img' => $twemojiBase . '1f441.svg'],
                    'Межличностный интеллект' => ['color' => '#073763', 'textColor' => 'white', 'emoji' => '👥', 'img' => $twemojiBase . '1f465.svg'],
                    'Внутриличностный интеллект' => ['color' => '#a6bee7', 'textColor' => 'black', 'emoji' => '💭', 'img' => $twemojiBase . '1f4ad.svg'],
                    'Натуралистический интеллект' => ['color' => '#f1c232', 'textColor' => 'black', 'emoji' => '🌻', 'img' => $twemojiBase . '1f33b.svg'],
                    'Экзистенциальный интеллект' => ['color' => '#6d9eeb', 'textColor' => 'black', 'emoji' => '🙏🏻', 'img' => $twemojiBase . '1f64f-1f3fb.svg'],
                ];

                // Первый ряд (5 типов)
                $row1Types = [
                    'Лингвистический интеллект',
                    'Логико-математический интеллект',
                    'Музыкальный интеллект',
                    'Телесно-кинестетический интеллект',
                    'Пространственный интеллект',
                ];

                // Второй ряд (4 типа)
                $row2Types = [
                    'Межличностный интеллект',
                    'Внутриличностный интеллект',
                    'Натуралистический интеллект',
                    'Экзистенциальный интеллект',
                ];
            @endphp
            <div class="mb-4 gardner-section">
                <h2 class="text-xl font-bold text-gray-800 mb-4" style="page-break-after: avoid !important;">Виды интеллектов Гарднера</h2>
                <div class="bg-gray-100 rounded-lg p-6 gardner-chart-container">
                    <!-- Первый ряд -->
                    <div class="gardner-row" style="page-break-inside: avoid; display: block; overflow: hidden;">
                    <div style="display: flex; align-items: flex-end; height: 180px; margin-bottom: 8px;">
                        <!-- Ось Y -->
                        <div style="width: 28px; height: 180px; position: relative; margin-right: 8px;">
                            @foreach([100, 75, 50, 25, 0] as $mark)
                                <div style="position: absolute; bottom: {{ $mark * 1.8 }}px; right: 0; transform: translateY(50%); font-size: 8px; color: #666; text-align: right; width: 24px;">{{ $mark }}</div>
                            @endforeach
                        </div>
                        <!-- Столбцы первого ряда -->
                        <div style="flex: 1; position: relative; height: 180px;">
                            @foreach([100, 75, 50, 25, 0] as $mark)
                                <div style="position: absolute; bottom: {{ $mark * 1.8 }}px; left: 0; right: 0; border-bottom: 1px solid #d1d5db; z-index: 0;"></div>
                            @endforeach
                            <div style="display: flex; align-items: flex-end; justify-content: center; height: 180px; position: relative; z-index: 1;">
                                @foreach($row1Types as $type)
                                    @php
                                        $percentage = $results[$type] ?? '0%';
                                        $numericValue = (int) str_replace('%', '', $percentage);
                                        $config = $intelligenceConfig[$type] ?? ['color' => '#cccccc', 'textColor' => 'white', 'emoji' => '❓', 'img' => $twemojiBase . '2753.svg'];
                                        $barHeight = max(round($numericValue * 1.8), 28);
                                        $textColor = $config['textColor'] ?? 'white';
                                        $textShadow = $textColor === 'white' ? '1px 1px 2px rgba(0,0,0,0.3)' : 'none';
                                    @endphp
                                    <div style="width: 110px; height: {{ $barHeight }}px; background-color: {{ $config['color'] }}; border-radius: 6px 6px 0 0; display: flex; align-items: flex-start; justify-content: center; padding-top: {{ $barHeight > 35 ? '6' : '2' }}px; margin: 0 24px;">
                                        <span style="font-size: 26px; font-weight: bold; color: {{ $textColor }}; text-shadow: {{ $textShadow }};">{{ $numericValue }}%</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <!-- Подписи первого ряда -->
                    <div style="display: flex; justify-content: center; margin-left: 36px; margin-bottom: 24px;">
                        @foreach($row1Types as $type)
                            @php
                                $shortName = str_replace(' интеллект', '', $type);
                                $config = $intelligenceConfig[$type] ?? ['color' => '#cccccc', 'emoji' => '❓', 'img' => $twemojiBase . '2753.svg'];
                            @endphp
                            <div style="width: 110px; display: flex; flex-direction: column; align-items: center; margin: 0 24px;">
                                <div style="height: 28px; display: flex; align-items: center; justify-content: center;">
                                    <img src="{{ $config['img'] }}" alt="{{ $config['emoji'] }}" style="width: 24px; height: 24px;">
                                </div>
                                <div style="text-align: center;">
                                    <div style="font-size: 15px; font-weight: bold; color: #374151; line-height: 1.2;">{{ $shortName }}</div>
                                    <div style="font-size: 15px; font-weight: bold; color: #374151; line-height: 1.2;">интеллект</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    </div>

                    <!-- Второй ряд -->
                    <div class="gardner-row" style="page-break-inside: avoid; display: block; overflow: hidden;">
                    <div style="display: flex; align-items: flex-end; height: 180px; margin-bottom: 8px;">
                        <!-- Ось Y -->
                        <div style="width: 28px; height: 180px; position: relative; margin-right: 8px;">
                            @foreach([100, 75, 50, 25, 0] as $mark)
                                <div style="position: absolute; bottom: {{ $mark * 1.8 }}px; right: 0; transform: translateY(50%); font-size: 8px; color: #666; text-align: right; width: 24px;">{{ $mark }}</div>
                            @endforeach
                        </div>
                        <!-- Столбцы второго ряда -->
                        <div style="flex: 1; position: relative; height: 180px;">
                            @foreach([100, 75, 50, 25, 0] as $mark)
                                <div style="position: absolute; bottom: {{ $mark * 1.8 }}px; left: 0; right: 0; border-bottom: 1px solid #d1d5db; z-index: 0;"></div>
                            @endforeach
                            <div style="display: flex; align-items: flex-end; justify-content: center; height: 180px; position: relative; z-index: 1;">
                                @foreach($row2Types as $type)
                                    @php
                                        $percentage = $results[$type] ?? '0%';
                                        $numericValue = (int) str_replace('%', '', $percentage);
                                        $config = $intelligenceConfig[$type] ?? ['color' => '#cccccc', 'textColor' => 'white', 'emoji' => '❓', 'img' => $twemojiBase . '2753.svg'];
                                        $barHeight = max(round($numericValue * 1.8), 28);
                                        $textColor = $config['textColor'] ?? 'white';
                                        $textShadow = $textColor === 'white' ? '1px 1px 2px rgba(0,0,0,0.3)' : 'none';
                                    @endphp
                                    <div style="width: 110px; height: {{ $barHeight }}px; background-color: {{ $config['color'] }}; border-radius: 6px 6px 0 0; display: flex; align-items: flex-start; justify-content: center; padding-top: {{ $barHeight > 35 ? '6' : '2' }}px; margin: 0 36px;">
                                        <span style="font-size: 26px; font-weight: bold; color: {{ $textColor }}; text-shadow: {{ $textShadow }};">{{ $numericValue }}%</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <!-- Подписи второго ряда -->
                    <div style="display: flex; justify-content: center; margin-left: 36px;">
                        @foreach($row2Types as $type)
                            @php
                                $shortName = str_replace(' интеллект', '', $type);
                                $config = $intelligenceConfig[$type] ?? ['color' => '#cccccc', 'emoji' => '❓', 'img' => $twemojiBase . '2753.svg'];
                            @endphp
                            <div style="width: 110px; display: flex; flex-direction: column; align-items: center; margin: 0 36px;">
                                <div style="height: 28px; display: flex; align-items: center; justify-content: center;">
                                    <img src="{{ $config['img'] }}" alt="{{ $config['emoji'] }}" style="width: 24px; height: 24px;">
                                </div>
                                <div style="text-align: center;">
                                    <div style="font-size: 15px; font-weight: bold; color: #374151; line-height: 1.2;">{{ $shortName }}</div>
                                    <div style="font-size: 15px; font-weight: bold; color: #374151; line-height: 1.2;">интеллект</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Footer -->
        <div class="bg-gray-100 p-4 text-center text-xs text-gray-500 no-print">
            <p>Отчет сгенерирован {{ now()->format('d.m.Y в H:i') }}</p>
        </div>
    </div>

    <!-- Кнопка Save Resume -->
    <div class="no-print" style="
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 999;
        font-family: 'Montserrat', sans-serif;
    ">
        <a href="{{ route('candidate.anketa.download.public', $candidate) }}" style="
            display: flex;
            align-items: center;
            gap: 10px;
            background: #39761d;
            color: white;
            padding: 14px 24px;
            border-radius: 50px;
            text-decoration: none;
            font-size: 15px;
            font-weight: 600;
            box-shadow: 0 6px 20px rgba(57,118,29,0.5);
            transition: transform 0.2s, box-shadow 0.2s;
            white-space: nowrap;
        " onmouseover="this.style.transform='scale(1.05)'" onmouseout="this.style.transform='scale(1)'">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="white"><path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z"/></svg>
            Save Resume
        </a>
    </div>
</body>
</html>
