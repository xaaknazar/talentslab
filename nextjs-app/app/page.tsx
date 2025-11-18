import Link from 'next/link'
import { Button } from '@/components/ui/button'

export default function HomePage() {
  return (
    <main className="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100">
      <div className="container-custom text-center">
        <div className="max-w-3xl mx-auto space-y-8">
          <h1 className="text-5xl font-bold text-gray-900">
            Добро пожаловать в TalentsLab
          </h1>
          <p className="text-xl text-gray-600">
            Комплексная система для работы с кандидатами
          </p>

          <div className="flex gap-4 justify-center">
            <Link href="/auth/login">
              <Button size="lg" className="btn-primary">
                Войти в систему
              </Button>
            </Link>
            <Link href="/auth/register">
              <Button size="lg" variant="outline">
                Регистрация
              </Button>
            </Link>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12">
            <div className="card p-6">
              <h3 className="text-lg font-semibold mb-2">Анкетирование</h3>
              <p className="text-sm text-gray-600">
                Многошаговая форма с валидацией и автосохранением
              </p>
            </div>
            <div className="card p-6">
              <h3 className="text-lg font-semibold mb-2">Тестирование</h3>
              <p className="text-sm text-gray-600">
                Gallup тесты, MBTI, тест Гарднера
              </p>
            </div>
            <div className="card p-6">
              <h3 className="text-lg font-semibold mb-2">Отчеты</h3>
              <p className="text-sm text-gray-600">
                PDF отчеты, Google Docs интеграция
              </p>
            </div>
          </div>
        </div>
      </div>
    </main>
  )
}
