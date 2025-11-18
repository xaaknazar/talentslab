#!/bin/bash

echo "🚀 TalentsLab Next.js - Автоматическая установка"
echo "================================================"
echo ""

# Проверка Node.js
if ! command -v node &> /dev/null; then
    echo "❌ Node.js не установлен. Установите Node.js 18+ и попробуйте снова."
    exit 1
fi

NODE_VERSION=$(node -v | cut -d'v' -f2 | cut -d'.' -f1)
if [ "$NODE_VERSION" -lt 18 ]; then
    echo "❌ Требуется Node.js 18 или выше. Текущая версия: $(node -v)"
    exit 1
fi

echo "✅ Node.js: $(node -v)"

# Проверка npm
if ! command -v npm &> /dev/null; then
    echo "❌ npm не установлен"
    exit 1
fi

echo "✅ npm: $(npm -v)"

# Установка зависимостей
echo ""
echo "📦 Установка зависимостей..."
npm install

if [ $? -ne 0 ]; then
    echo "❌ Ошибка при установке зависимостей"
    exit 1
fi

echo "✅ Зависимости установлены"

# Создание .env файла
echo ""
if [ ! -f .env ]; then
    echo "📝 Создание .env файла..."
    cp .env.example .env

    # Генерация NEXTAUTH_SECRET
    if command -v openssl &> /dev/null; then
        SECRET=$(openssl rand -base64 32)
        if [[ "$OSTYPE" == "darwin"* ]]; then
            # macOS
            sed -i '' "s|NEXTAUTH_SECRET=\"your-secret-key-here\"|NEXTAUTH_SECRET=\"$SECRET\"|" .env
        else
            # Linux
            sed -i "s|NEXTAUTH_SECRET=\"your-secret-key-here\"|NEXTAUTH_SECRET=\"$SECRET\"|" .env
        fi
        echo "✅ Сгенерирован NEXTAUTH_SECRET"
    fi

    echo ""
    echo "⚠️  ВАЖНО! Отредактируйте .env файл и укажите:"
    echo "   - DATABASE_URL (строка подключения к MySQL)"
    echo "   - SMTP настройки для отправки email"
    echo "   - Google API credentials (если нужно)"
    echo ""
    echo "Пример DATABASE_URL:"
    echo "DATABASE_URL=\"mysql://user:password@localhost:3306/talentslab\""
    echo ""
    read -p "Нажмите Enter после настройки .env файла..."
else
    echo "✅ .env файл уже существует"
fi

# Генерация Prisma клиента
echo ""
echo "🔧 Генерация Prisma клиента..."
npm run prisma:generate

if [ $? -ne 0 ]; then
    echo "❌ Ошибка при генерации Prisma клиента"
    exit 1
fi

echo "✅ Prisma клиент сгенерирован"

# Проверка подключения к БД
echo ""
echo "🔍 Проверка подключения к базе данных..."
echo "Если база данных еще не создана, выполните:"
echo "  mysql -u root -p"
echo "  CREATE DATABASE talentslab CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
echo ""

# Опционально: применить схему к БД
read -p "Применить Prisma схему к базе данных? (y/n) " -n 1 -r
echo
if [[ $REPLY =~ ^[Yy]$ ]]; then
    npm run prisma:push
    if [ $? -eq 0 ]; then
        echo "✅ Схема применена к базе данных"
    else
        echo "❌ Ошибка при применении схемы. Проверьте DATABASE_URL в .env"
        exit 1
    fi
fi

echo ""
echo "✅ Установка завершена!"
echo ""
echo "🚀 Запуск проекта:"
echo "   npm run dev"
echo ""
echo "📖 Документация:"
echo "   - README.md - полная документация"
echo "   - MIGRATION_NEXT_STEPS.md - план дальнейшей разработки"
echo ""
echo "🔧 Полезные команды:"
echo "   npm run dev          - запуск development сервера"
echo "   npm run build        - сборка production"
echo "   npm run start        - запуск production сервера"
echo "   npm run prisma:studio - GUI для работы с БД"
echo ""
