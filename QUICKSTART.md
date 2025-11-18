# 🚀 Быстрый старт TalentsLab Next.js

Это руководство поможет вам запустить проект за **5 минут**.

---

## ⚡ Вариант 1: Автоматическая установка (рекомендуется)

### Windows

```bash
cd nextjs-app
setup.bat
```

### Linux / macOS

```bash
cd nextjs-app
chmod +x setup.sh
./setup.sh
```

Скрипт автоматически:
- ✅ Проверит установку Node.js
- ✅ Установит зависимости
- ✅ Создаст .env файл
- ✅ Сгенерирует Prisma клиент
- ✅ Применит схему к базе данных

**После установки:**
```bash
npm run dev
```

Откройте http://localhost:3000

---

## 🐳 Вариант 2: Docker (самый простой)

**Требования:** Docker и Docker Compose

### 1. Клонируйте репозиторий

```bash
git clone https://github.com/xaaknazar/talentslab.git
cd talentslab/nextjs-app
```

### 2. Создайте .env файл

```bash
cp .env.example .env
```

Сгенерируйте NEXTAUTH_SECRET:
```bash
# Linux/macOS
openssl rand -base64 32

# Windows (PowerShell)
[Convert]::ToBase64String((1..32 | ForEach-Object { Get-Random -Maximum 256 }))
```

Вставьте в .env:
```env
NEXTAUTH_SECRET="ваш-сгенерированный-секрет"
```

### 3. Запустите Docker Compose

```bash
docker-compose up -d
```

Это запустит:
- ✅ MySQL (порт 3306)
- ✅ Redis (порт 6379)
- ✅ Next.js приложение (порт 3000)

### 4. Откройте браузер

http://localhost:3000

**Готово! 🎉**

### Полезные команды Docker

```bash
# Посмотреть логи
docker-compose logs -f app

# Остановить
docker-compose down

# Пересобрать
docker-compose up -d --build

# Удалить все (включая данные)
docker-compose down -v
```

---

## 🛠️ Вариант 3: Ручная установка

### Требования

- Node.js 18+ ([скачать](https://nodejs.org/))
- npm или yarn
- MySQL 8.0+ (работающий локально или удаленно)
- Redis (опционально, для queue/jobs)

### 1. Клонируйте репозиторий

```bash
git clone https://github.com/xaaknazar/talentslab.git
cd talentslab/nextjs-app
```

### 2. Установите зависимости

```bash
npm install
```

### 3. Настройте .env

```bash
cp .env.example .env
```

Отредактируйте `.env`:

```env
# Database (укажите свои credentials)
DATABASE_URL="mysql://user:password@localhost:3306/talentslab"

# NextAuth
NEXTAUTH_URL="http://localhost:3000"
NEXTAUTH_SECRET="ваш-секрет"  # Сгенерируйте: openssl rand -base64 32

# Email (опционально)
SMTP_HOST="smtp.gmail.com"
SMTP_PORT="587"
SMTP_USER="your-email@gmail.com"
SMTP_PASSWORD="your-app-password"

# Redis (опционально)
REDIS_URL="redis://localhost:6379"

# Google API (опционально)
GOOGLE_CLIENT_ID=""
GOOGLE_CLIENT_SECRET=""
```

### 4. Создайте базу данных

```sql
CREATE DATABASE talentslab CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 5. Примените Prisma схему

```bash
npm run prisma:generate
npm run prisma:push
```

### 6. Запустите development сервер

```bash
npm run dev
```

### 7. Откройте браузер

http://localhost:3000

---

## 📝 Что дальше?

### 1. Создайте первого пользователя

Пока нет страницы регистрации, создайте пользователя через Prisma Studio:

```bash
npm run prisma:studio
```

1. Откройте http://localhost:5555
2. Перейдите в таблицу `User`
3. Нажмите "Add record"
4. Заполните:
   - `name`: Ваше имя
   - `email`: ваш@email.com
   - `password`: (используйте bcrypt хэш)
   - `isAdmin`: true
   - `emailVerifiedAt`: текущая дата

**Для хэширования пароля:**
```bash
# Node.js REPL
node
> require('bcryptjs').hashSync('ваш-пароль', 10)
```

### 2. Войдите в систему

http://localhost:3000/auth/login

### 3. Изучите документацию

- `README.md` - полная документация
- `MIGRATION_NEXT_STEPS.md` - что нужно доделать

---

## 🔧 Полезные команды

```bash
# Development
npm run dev                  # Запуск dev сервера (localhost:3000)

# Production
npm run build                # Сборка для production
npm start                    # Запуск production сервера

# Prisma
npm run prisma:generate      # Сгенерировать Prisma клиент
npm run prisma:push          # Применить схему к БД
npm run prisma:studio        # Открыть Prisma Studio (GUI для БД)
npm run prisma:pull          # Получить схему из БД

# Linting
npm run lint                 # Проверка кода
```

---

## 🐛 Проблемы и решения

### Ошибка подключения к БД

```
Error: P1001: Can't reach database server
```

**Решение:**
1. Убедитесь что MySQL запущен
2. Проверьте `DATABASE_URL` в `.env`
3. Проверьте что БД создана

### Prisma ошибки

```
Error: Cannot find module '@prisma/client'
```

**Решение:**
```bash
rm -rf node_modules/.prisma
npm run prisma:generate
```

### Port 3000 занят

```
Error: Port 3000 is already in use
```

**Решение:**
```bash
# Запустите на другом порту
PORT=3001 npm run dev
```

Или убейте процесс:
```bash
# Linux/macOS
lsof -ti:3000 | xargs kill

# Windows
netstat -ano | findstr :3000
taskkill /PID <PID> /F
```

### Next.js кэш

Если что-то работает странно:
```bash
rm -rf .next
npm run dev
```

---

## 📚 Дополнительные ресурсы

### Документация
- [Next.js Docs](https://nextjs.org/docs)
- [Prisma Docs](https://www.prisma.io/docs)
- [NextAuth.js Docs](https://next-auth.js.org)
- [Tailwind CSS](https://tailwindcss.com/docs)

### Видео туториалы
- [Next.js 14 Full Course](https://www.youtube.com/results?search_query=nextjs+14+tutorial)
- [Prisma Tutorial](https://www.youtube.com/results?search_query=prisma+tutorial)

### Сообщество
- [Next.js Discord](https://discord.gg/nextjs)
- [Prisma Discord](https://discord.gg/prisma)

---

## ✅ Checklist первого запуска

- [ ] Node.js 18+ установлен
- [ ] MySQL работает
- [ ] Зависимости установлены (`npm install`)
- [ ] `.env` файл создан и настроен
- [ ] База данных создана
- [ ] Prisma клиент сгенерирован
- [ ] Схема применена к БД
- [ ] Dev сервер запущен
- [ ] Страница открывается в браузере
- [ ] Первый пользователь создан
- [ ] Успешный вход в систему

---

## 🎉 Готово!

Проект запущен и работает!

**Следующие шаги:**
1. Изучите `MIGRATION_NEXT_STEPS.md` для плана разработки
2. Начните с завершения аутентификации
3. Создайте многошаговую форму кандидата

**Нужна помощь?** Проверьте README.md или создайте Issue на GitHub.

Удачи в разработке! 🚀
