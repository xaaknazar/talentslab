<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>@yield('title', 'TalentsLab - CV Database')</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Styles -->
        <link href="{{ asset('css/auth-pages.css') }}" rel="stylesheet">

        @stack('styles')
        <style>
            /* Minimalist Language Switcher */
            .lang-switch {
                position: fixed;
                top: 24px;
                {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 24px;
                z-index: 1000;
            }

            .lang-switch-btn {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 10px 16px;
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                cursor: pointer;
                font-size: 14px;
                font-weight: 500;
                color: #374151;
                transition: all 0.2s ease;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            }

            .lang-switch-btn:hover {
                border-color: #3b82f6;
                box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
            }

            .lang-switch-btn svg {
                width: 18px;
                height: 18px;
                color: #6b7280;
            }

            .lang-switch-btn .chevron {
                width: 16px;
                height: 16px;
                transition: transform 0.2s ease;
            }

            .lang-switch.open .chevron {
                transform: rotate(180deg);
            }

            .lang-dropdown {
                position: absolute;
                top: calc(100% + 8px);
                {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 0;
                min-width: 180px;
                background: #fff;
                border: 1px solid #e5e7eb;
                border-radius: 12px;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12);
                opacity: 0;
                visibility: hidden;
                transform: translateY(-8px);
                transition: all 0.2s ease;
                overflow: hidden;
            }

            .lang-switch.open .lang-dropdown {
                opacity: 1;
                visibility: visible;
                transform: translateY(0);
            }

            .lang-option {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 12px 16px;
                text-decoration: none;
                color: #374151;
                font-size: 14px;
                font-weight: 500;
                transition: background-color 0.15s ease;
            }

            .lang-option:hover {
                background: #f9fafb;
            }

            .lang-option.active {
                background: #eff6ff;
                color: #2563eb;
            }

            .lang-option .check {
                width: 18px;
                height: 18px;
                color: #2563eb;
                opacity: 0;
            }

            .lang-option.active .check {
                opacity: 1;
            }

            .lang-name {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .lang-code {
                font-weight: 600;
                color: #6b7280;
                font-size: 12px;
                text-transform: uppercase;
            }

            .lang-option.active .lang-code {
                color: #3b82f6;
            }

            /* Mobile responsive */
            @media (max-width: 640px) {
                .lang-switch {
                    top: 16px;
                    {{ app()->getLocale() == 'ar' ? 'left' : 'right' }}: 16px;
                }

                .lang-switch-btn {
                    padding: 8px 12px;
                    font-size: 13px;
                }

                .lang-switch-btn .lang-text {
                    display: none;
                }

                .lang-dropdown {
                    min-width: 160px;
                }
            }
        </style>
    </head>
    <body>
        <!-- Minimalist Language Switcher -->
        <div class="lang-switch" id="langSwitch">
            @php
                $currentLocale = app()->getLocale();
                $languages = [
                    'ru' => ['name' => 'Русский', 'code' => 'RU'],
                    'en' => ['name' => 'English', 'code' => 'EN'],
                    'ar' => ['name' => 'العربية', 'code' => 'AR']
                ];
                $currentLang = $languages[$currentLocale] ?? $languages['ru'];
            @endphp

            <button type="button" class="lang-switch-btn" onclick="toggleLangDropdown()">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" />
                </svg>
                <span class="lang-text">{{ $currentLang['name'] }}</span>
                <svg class="chevron" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                </svg>
            </button>

            <div class="lang-dropdown">
                @foreach($languages as $locale => $lang)
                    <a href="{{ route('language.switch', $locale) }}" class="lang-option {{ $currentLocale === $locale ? 'active' : '' }}">
                        <span class="lang-name">
                            <span class="lang-code">{{ $lang['code'] }}</span>
                            <span>{{ $lang['name'] }}</span>
                        </span>
                        <svg class="check" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                        </svg>
                    </a>
                @endforeach
            </div>
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

        <script>
            function toggleLangDropdown() {
                document.getElementById('langSwitch').classList.toggle('open');
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                const langSwitch = document.getElementById('langSwitch');
                if (!langSwitch.contains(e.target)) {
                    langSwitch.classList.remove('open');
                }
            });
        </script>

        @stack('scripts')
    </body>
</html>
