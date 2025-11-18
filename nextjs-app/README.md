# TalentsLab - Next.js Edition

Полная миграция проекта TalentsLab с Laravel на Next.js 14 + React 18 + TypeScript.

## 🚀 Технологический стек

### Frontend
- **Next.js 14.2.13** - React фреймворк с App Router
- **React 18.2.0** - Библиотека для UI
- **TypeScript 5.2.2** - Типизация
- **Tailwind CSS 3.4.1** - CSS фреймворк для стилизации
- **Framer Motion 12.9.4** - Анимации
- **Radix UI** - Headless UI компоненты
- **React Hook Form** - Управление формами
- **TanStack Query** - State management и кэширование данных

### Backend
- **Next.js API Routes** - Backend API
- **Prisma 5.9.0** - ORM для работы с базой данных
- **NextAuth.js** - Аутентификация
- **Zod** - Валидация схем

### Database
- **MySQL** - База данных (та же, что была в Laravel)

### Дополнительно
- **Google APIs** - Интеграция с Google Docs/Sheets
- **Puppeteer** - PDF генерация
- **ExcelJS** - Работа с Excel
- **Bull + Redis** - Queue/Jobs система
- **Nodemailer** - Отправка email

## 📁 Структура проекта

```
nextjs-app/
├── app/                      # Next.js 14 App Router
│   ├── api/                  # API routes
│   │   ├── auth/            # Аутентификация
│   │   ├── candidates/      # CRUD для кандидатов
│   │   ├── gallup/          # Gallup тесты
│   │   └── ...
│   ├── (auth)/              # Auth pages group
│   │   ├── login/
│   │   └── register/
│   ├── (dashboard)/         # Dashboard pages group
│   │   ├── dashboard/
│   │   └── candidates/
│   ├── admin/               # Admin panel
│   ├── layout.tsx           # Root layout
│   ├── page.tsx             # Home page
│   ├── providers.tsx        # Global providers
│   └── globals.css          # Global styles
├── components/              # React components
│   ├── ui/                  # UI components (Button, Input, etc.)
│   ├── forms/               # Form components
│   └── layouts/             # Layout components
├── lib/                     # Utilities
│   ├── prisma.ts           # Prisma client
│   ├── auth.ts             # Auth configuration
│   └── utils.ts            # Helper functions
├── prisma/                  # Prisma schema
│   └── schema.prisma       # Database schema
├── types/                   # TypeScript types
│   ├── index.ts            # Global types
│   └── next-auth.d.ts      # NextAuth types
├── public/                  # Static files
│   └── uploads/            # User uploads
├── .env.example            # Environment variables example
├── package.json            # Dependencies
├── tsconfig.json           # TypeScript config
├── tailwind.config.ts      # Tailwind config
└── next.config.js          # Next.js config
```

## 🔧 Установка

### 1. Клонируйте репозиторий

```bash
cd nextjs-app
```

### 2. Установите зависимости

```bash
npm install
```

### 3. Настройте переменные окружения

Скопируйте `.env.example` в `.env`:

```bash
cp .env.example .env
```

Отредактируйте `.env` и заполните необходимые переменные:

```env
# Database (используйте те же credentials что и в Laravel)
DATABASE_URL="mysql://user:password@localhost:3306/talentslab"

# NextAuth
NEXTAUTH_URL="http://localhost:3000"
NEXTAUTH_SECRET="ваш-секретный-ключ"  # Сгенерируйте: openssl rand -base64 32

# Email
SMTP_HOST="smtp.gmail.com"
SMTP_PORT="587"
SMTP_USER="your-email@gmail.com"
SMTP_PASSWORD="your-app-password"

# Google API
GOOGLE_CLIENT_ID="..."
GOOGLE_CLIENT_SECRET="..."

# Redis (для Bull Queue)
REDIS_URL="redis://localhost:6379"
```

### 4. Подключитесь к существующей базе данных

Prisma схема уже создана на основе Laravel миграций. Сгенерируйте Prisma клиент:

```bash
npm run prisma:generate
```

Если база данных еще не создана, примените схему:

```bash
npm run prisma:push
```

### 5. Запустите development сервер

```bash
npm run dev
```

Откройте [http://localhost:3000](http://localhost:3000) в браузере.

## 📊 База данных

### Миграция с Laravel

База данных MySQL **остается той же**. Prisma схема создана на основе существующих Laravel миграций и полностью совместима с текущей структурой БД.

### Основные модели

- **User** - Пользователи системы
- **Candidate** - Кандидаты
- **CandidateFile** - Файлы кандидатов
- **CandidateHistory** - История изменений
- **CandidateStatus** - Статусы кандидатов
- **CandidateComment** - Комментарии
- **GallupReport** - Gallup отчеты
- **GallupTalent** - Gallup таланты
- **GardnerTestResult** - Результаты теста Гарднера

### Работа с Prisma

```bash
# Сгенерировать клиент
npm run prisma:generate

# Применить схему к БД
npm run prisma:push

# Открыть Prisma Studio (GUI для БД)
npm run prisma:studio

# Получить схему из существующей БД
npm run prisma:pull
```

## 🔐 Аутентификация

Используется **NextAuth.js** вместо Laravel Jetstream/Sanctum.

### Основные endpoints:

- `POST /api/auth/signin` - Вход
- `POST /api/auth/signout` - Выход
- `GET /api/auth/session` - Текущая сессия

### Использование в компонентах:

```typescript
import { useSession, signIn, signOut } from 'next-auth/react'

export default function Component() {
  const { data: session, status } = useSession()

  if (status === 'loading') return <div>Loading...</div>
  if (!session) return <button onClick={() => signIn()}>Sign in</button>

  return (
    <div>
      Signed in as {session.user.email}
      <button onClick={() => signOut()}>Sign out</button>
    </div>
  )
}
```

### Защита routes:

```typescript
// app/dashboard/page.tsx
import { redirect } from 'next/navigation'
import { getServerSession } from 'next-auth'
import { authOptions } from '@/lib/auth'

export default async function DashboardPage() {
  const session = await getServerSession(authOptions)

  if (!session) {
    redirect('/auth/login')
  }

  return <div>Dashboard</div>
}
```

## 📝 API Routes

### Кандидаты

- `GET /api/candidates` - Список кандидатов (с пагинацией и поиском)
- `POST /api/candidates` - Создать кандидата
- `GET /api/candidates/[id]` - Получить кандидата
- `PATCH /api/candidates/[id]` - Обновить кандидата
- `DELETE /api/candidates/[id]` - Удалить кандидата (только admin)

### Gallup

- `POST /api/gallup/process` - Обработать Gallup PDF
- `GET /api/gallup/reports/[id]` - Получить отчет
- `POST /api/gallup/generate-docs` - Создать Google Docs

### Тесты

- `POST /api/tests/gardner` - Сохранить результат теста Гарднера
- `GET /api/tests/gardner/[userId]` - Получить результат

## 🎨 Компоненты

### UI компоненты (Radix UI + Tailwind)

- Button
- Input
- Card
- Label
- Dialog
- Dropdown Menu
- Select
- Tabs
- Toast

### Пример использования:

```typescript
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Card, CardHeader, CardTitle, CardContent } from '@/components/ui/card'

export default function Example() {
  return (
    <Card>
      <CardHeader>
        <CardTitle>Форма</CardTitle>
      </CardHeader>
      <CardContent>
        <Input placeholder="Email" />
        <Button>Отправить</Button>
      </CardContent>
    </Card>
  )
}
```

## 🔄 State Management

Используется **TanStack Query** (React Query) для работы с серверными данными.

```typescript
'use client'

import { useQuery } from '@tanstack/react-query'

export default function CandidatesList() {
  const { data, isLoading } = useQuery({
    queryKey: ['candidates'],
    queryFn: async () => {
      const res = await fetch('/api/candidates')
      return res.json()
    },
  })

  if (isLoading) return <div>Loading...</div>

  return (
    <div>
      {data?.data.map((candidate) => (
        <div key={candidate.id}>{candidate.fullName}</div>
      ))}
    </div>
  )
}
```

## 📄 Формы

Используется **React Hook Form** + **Zod** для валидации.

```typescript
'use client'

import { useForm } from 'react-hook-form'
import { zodResolver } from '@hookform/resolvers/zod'
import { z } from 'zod'

const schema = z.object({
  email: z.string().email(),
  password: z.string().min(8),
})

export default function LoginForm() {
  const { register, handleSubmit, formState: { errors } } = useForm({
    resolver: zodResolver(schema),
  })

  const onSubmit = async (data) => {
    // Handle submission
  }

  return (
    <form onSubmit={handleSubmit(onSubmit)}>
      <input {...register('email')} />
      {errors.email && <span>{errors.email.message}</span>}

      <input {...register('password')} type="password" />
      {errors.password && <span>{errors.password.message}</span>}

      <button type="submit">Submit</button>
    </form>
  )
}
```

## 🚀 Деплой

### Vercel (рекомендуется)

```bash
npm install -g vercel
vercel
```

### Docker

```bash
docker build -t talentslab-nextjs .
docker run -p 3000:3000 talentslab-nextjs
```

### Manual

```bash
npm run build
npm start
```

## 📚 Документация

- [Next.js Documentation](https://nextjs.org/docs)
- [Prisma Documentation](https://www.prisma.io/docs)
- [NextAuth.js Documentation](https://next-auth.js.org)
- [TanStack Query](https://tanstack.com/query/latest)
- [Tailwind CSS](https://tailwindcss.com/docs)

## 🔧 Scripts

```bash
npm run dev          # Development сервер
npm run build        # Production build
npm run start        # Production сервер
npm run lint         # ESLint
npm run prisma:generate  # Сгенерировать Prisma клиент
npm run prisma:push      # Применить схему к БД
npm run prisma:studio    # Prisma Studio GUI
npm run prisma:pull      # Получить схему из БД
```

## ⚠️ Важные отличия от Laravel

### 1. Нет Filament админки
- Нужно создать свою админ-панель
- Используйте `/app/admin` для админских страниц

### 2. Queue/Jobs
- Вместо Laravel Queue используется Bull + Redis
- Настройте Redis сервер отдельно

### 3. Email
- Вместо Laravel Mail используется Nodemailer
- Настройте SMTP credentials в `.env`

### 4. File Uploads
- Загрузка файлов через API routes
- Используйте `sharp` для обработки изображений

### 5. PDF генерация
- Вместо dompdf используется Puppeteer
- Требует больше ресурсов

## 🐛 Troubleshooting

### Prisma ошибки

```bash
# Очистить кэш и пересоздать клиент
rm -rf node_modules/.prisma
npm run prisma:generate
```

### TypeScript ошибки

```bash
# Перезапустить TypeScript сервер в VSCode
Cmd/Ctrl + Shift + P -> "TypeScript: Restart TS Server"
```

### Next.js кэш

```bash
# Очистить кэш Next.js
rm -rf .next
npm run dev
```

## 📝 TODO

- [ ] Завершить миграцию всех Laravel контроллеров
- [ ] Создать админ-панель (замена Filament)
- [ ] Настроить Bull Queue для фоновых задач
- [ ] Мигрировать Google API интеграцию
- [ ] Настроить PDF генерацию
- [ ] Добавить E2E тесты
- [ ] Настроить CI/CD

## 📞 Поддержка

При возникновении проблем:
1. Проверьте `.env` файл
2. Убедитесь что MySQL запущен
3. Проверьте Prisma схему
4. Проверьте логи в консоли

## 📄 Лицензия

MIT
