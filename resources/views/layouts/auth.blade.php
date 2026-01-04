<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'TalentsLab - Система управления обучением')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Styles -->
        <link href="{{ asset('css/auth-pages.css') }}" rel="stylesheet">

        @stack('styles')
        <style>
            .language-switcher {
                position: absolute;
                top: 20px;
                right: 20px;
                display: flex;
                gap: 8px;
                z-index: 100;
            }
            .language-switcher a {
                display: flex;
                align-items: center;
                gap: 6px;
                padding: 8px 12px;
                border-radius: 8px;
                text-decoration: none;
                font-size: 13px;
                font-weight: 500;
                transition: all 0.2s ease;
                background: rgba(255, 255, 255, 0.9);
                color: #374151;
                border: 1px solid rgba(0, 0, 0, 0.1);
            }
            .language-switcher a:hover {
                background: #fff;
                border-color: #3b82f6;
                color: #3b82f6;
            }
            .language-switcher a.active {
                background: #3b82f6;
                color: #fff;
                border-color: #3b82f6;
            }
            .lang-code {
                font-weight: 600;
            }

            /* Mobile responsive styles */
            @media (max-width: 640px) {
                .language-switcher {
                    top: 10px;
                    right: 10px;
                    gap: 4px;
                }
                .language-switcher a {
                    padding: 6px 10px;
                    font-size: 12px;
                    gap: 4px;
                }
                .language-switcher .lang-name {
                    display: none;
                }
            }

            @media (max-width: 400px) {
                .language-switcher {
                    top: 8px;
                    right: 8px;
                    gap: 3px;
                }
                .language-switcher a {
                    padding: 5px 8px;
                    font-size: 11px;
                    border-radius: 6px;
                }
            }
        </style>
    </head>
    <body>
        <!-- Language Switcher -->
        <div class="language-switcher">
            @php
                $currentLocale = app()->getLocale();
                $languages = [
                    'ru' => ['name' => 'Русский', 'code' => 'Ru'],
                    'en' => ['name' => 'English', 'code' => 'En'],
                    'ar' => ['name' => 'العربية', 'code' => 'SA']
                ];
            @endphp
            @foreach($languages as $locale => $lang)
                <a href="{{ route('language.switch', $locale) }}" class="{{ $currentLocale === $locale ? 'active' : '' }}">
                    <span class="lang-code">{{ $lang['code'] }}</span>
                    <span class="lang-name">{{ $lang['name'] }}</span>
                </a>
            @endforeach
        </div>

        <div class="container">
            <div class="main-card">
                <!-- Left Panel - Auth Form -->
                <div class="left-panel">
                    <div class="auth-section">
                        @yield('content')
                    </div>
                </div>

                <!-- Right Panel - Branding -->
                <div class="right-panel">
                    <div class="logos-container">
                        <img src="{{ asset('logos/divergents_logo.png') }}" alt="Divergents Logo" class="brand-logo brand-logo-left">
                        <img src="{{ asset('logos/talents_lab_logo.png') }}" alt="Talents Lab Logo" class="brand-logo brand-logo-right">
                    </div>
                    <p class="brand-description">
                        {{ __('Talents Lab is a modern online platform with a resume database reflecting a holistic psychometric portrait of personality.') }}
                        <br><br>
                        {{ __('By completing the questionnaire, you will create a resume and be able to find a vacancy that best matches your abilities and potential.') }}
                    </p>
                </div>
            </div>
        </div>

        @stack('scripts')
    </body>
</html>
