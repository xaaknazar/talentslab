<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Поддерживаемые языки
     */
    protected array $supportedLocales = ['ru', 'en', 'ar'];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Приоритет: сессия > cookie > браузер > дефолт
        $locale = Session::get('locale')
            ?? $request->cookie('locale')
            ?? $this->getBrowserLocale($request)
            ?? config('app.locale', 'ru');

        // Проверяем что язык поддерживается
        if (!in_array($locale, $this->supportedLocales)) {
            $locale = config('app.locale', 'ru');
        }

        App::setLocale($locale);

        return $next($request);
    }

    /**
     * Получить предпочитаемый язык из браузера
     */
    protected function getBrowserLocale(Request $request): ?string
    {
        $acceptLanguage = $request->header('Accept-Language');

        if (!$acceptLanguage) {
            return null;
        }

        // Парсим Accept-Language header
        $languages = explode(',', $acceptLanguage);

        foreach ($languages as $language) {
            $lang = strtolower(substr(trim(explode(';', $language)[0]), 0, 2));

            if (in_array($lang, $this->supportedLocales)) {
                return $lang;
            }
        }

        return null;
    }
}
