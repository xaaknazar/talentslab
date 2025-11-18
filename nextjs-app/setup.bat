@echo off
echo.
echo 🚀 TalentsLab Next.js - Автоматическая установка
echo ================================================
echo.

REM Проверка Node.js
where node >nul 2>nul
if %errorlevel% neq 0 (
    echo ❌ Node.js не установлен. Установите Node.js 18+ и попробуйте снова.
    echo Скачать: https://nodejs.org/
    pause
    exit /b 1
)

echo ✅ Node.js:
node -v

REM Проверка npm
where npm >nul 2>nul
if %errorlevel% neq 0 (
    echo ❌ npm не установлен
    pause
    exit /b 1
)

echo ✅ npm:
npm -v

REM Установка зависимостей
echo.
echo 📦 Установка зависимостей...
call npm install

if %errorlevel% neq 0 (
    echo ❌ Ошибка при установке зависимостей
    pause
    exit /b 1
)

echo ✅ Зависимости установлены

REM Создание .env файла
echo.
if not exist .env (
    echo 📝 Создание .env файла...
    copy .env.example .env

    echo.
    echo ⚠️  ВАЖНО! Отредактируйте .env файл и укажите:
    echo    - DATABASE_URL ^(строка подключения к MySQL^)
    echo    - SMTP настройки для отправки email
    echo    - Google API credentials ^(если нужно^)
    echo.
    echo Пример DATABASE_URL:
    echo DATABASE_URL="mysql://user:password@localhost:3306/talentslab"
    echo.
    echo Нажмите любую клавишу после настройки .env файла...
    pause >nul
) else (
    echo ✅ .env файл уже существует
)

REM Генерация Prisma клиента
echo.
echo 🔧 Генерация Prisma клиента...
call npm run prisma:generate

if %errorlevel% neq 0 (
    echo ❌ Ошибка при генерации Prisma клиента
    pause
    exit /b 1
)

echo ✅ Prisma клиент сгенерирован

REM Информация о БД
echo.
echo 🔍 Проверка подключения к базе данных...
echo Если база данных еще не создана, выполните:
echo   mysql -u root -p
echo   CREATE DATABASE talentslab CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
echo.

REM Применить схему к БД
set /p APPLY_SCHEMA="Применить Prisma схему к базе данных? (y/n) "
if /i "%APPLY_SCHEMA%"=="y" (
    call npm run prisma:push
    if %errorlevel% equ 0 (
        echo ✅ Схема применена к базе данных
    ) else (
        echo ❌ Ошибка при применении схемы. Проверьте DATABASE_URL в .env
        pause
        exit /b 1
    )
)

echo.
echo ✅ Установка завершена!
echo.
echo 🚀 Запуск проекта:
echo    npm run dev
echo.
echo 📖 Документация:
echo    - README.md - полная документация
echo    - MIGRATION_NEXT_STEPS.md - план дальнейшей разработки
echo.
echo 🔧 Полезные команды:
echo    npm run dev           - запуск development сервера
echo    npm run build         - сборка production
echo    npm run start         - запуск production сервера
echo    npm run prisma:studio - GUI для работы с БД
echo.
pause
