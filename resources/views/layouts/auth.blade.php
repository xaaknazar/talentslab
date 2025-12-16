<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
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
    </head>
    <body>
        <!-- Animated Background with Arrows -->
        <div class="animated-bg" id="arrowsContainer"></div>

        <!-- Gradient Overlay -->
        <div class="gradient-overlay"></div>

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
                        Talents Lab — современная онлайн-платформа с базой данных резюме, отражающих целостный психометрический портрет личности.
                        <br><br>
                        Пройдя анкетирование, вы сформируете резюме и сможете подобрать вакансию, максимально соответствующую вашим способностям и потенциалу.
                    </p>

                    <!-- Decorative Elements -->
                    <div class="decorative-circle"></div>
                    <div class="decorative-circle-2"></div>
                </div>
            </div>
        </div>

        <script>
            // Create animated arrows going up
            function createAnimatedArrows() {
                const container = document.getElementById('arrowsContainer');
                const arrowCount = 25; // Number of arrows

                for (let i = 0; i < arrowCount; i++) {
                    const arrow = document.createElement('div');
                    arrow.className = 'arrow';
                    arrow.innerHTML = '↑'; // Up arrow symbol

                    // Random horizontal position
                    arrow.style.left = Math.random() * 100 + '%';

                    // Random animation duration (10-20 seconds)
                    const duration = 10 + Math.random() * 10;
                    arrow.style.animationDuration = duration + 's';

                    // Random delay for staggered start
                    const delay = Math.random() * 10;
                    arrow.style.animationDelay = delay + 's';

                    container.appendChild(arrow);
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                createAnimatedArrows();
            });
        </script>

        @stack('scripts')
    </body>
</html>
