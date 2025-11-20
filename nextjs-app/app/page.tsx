import Link from 'next/link'
import { Button } from '@/components/ui/button'
import { PromoBanner } from '@/components/promo-banner'
import { Navbar } from '@/components/navbar'

export default function HomePage() {
  return (
    <>
      <Navbar />
      <PromoBanner />

      <main className="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-50 to-indigo-100">
        <div className="container-custom text-center py-12">
          <div className="max-w-3xl mx-auto space-y-8">
            <h1 className="text-4xl sm:text-5xl font-bold text-gray-900">
              Добро пожаловать в TalentsLab
            </h1>
            <p className="text-lg sm:text-xl text-gray-600">
              Комплексная система для работы с кандидатами
            </p>

            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <Link href="/auth/login">
                <Button size="lg" className="btn-primary w-full sm:w-auto">
                  Войти в систему
                </Button>
              </Link>
              <Link href="/auth/register">
                <Button size="lg" variant="outline" className="w-full sm:w-auto">
                  Регистрация
                </Button>
              </Link>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mt-12">
              <div className="card p-6 hover:shadow-lg transition-shadow">
                <h3 className="text-lg font-semibold mb-2">Анкетирование</h3>
                <p className="text-sm text-gray-600">
                  Многошаговая форма с валидацией и автосохранением
                </p>
              </div>
              <div className="card p-6 hover:shadow-lg transition-shadow">
                <h3 className="text-lg font-semibold mb-2">Тестирование</h3>
                <p className="text-sm text-gray-600">
                  Gallup тесты, MBTI, тест Гарднера
                </p>
              </div>
              <div className="card p-6 hover:shadow-lg transition-shadow">
                <h3 className="text-lg font-semibold mb-2">Отчеты</h3>
                <p className="text-sm text-gray-600">
                  PDF отчеты, Google Docs интеграция
                </p>
              </div>
            </div>
          </div>
        </div>
      </main>
    </>
  )
}
