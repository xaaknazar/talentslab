'use client'

import Link from 'next/link'
import { Phone } from 'lucide-react'

export function Navbar() {
  return (
    <nav className="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
      <div className="container-custom">
        <div className="flex items-center justify-between h-16">
          {/* Logo */}
          <Link href="/" className="flex items-center gap-2">
            <div className="flex items-center justify-center w-10 h-10 bg-gradient-to-br from-indigo-600 to-purple-600 rounded-lg">
              <span className="text-white font-bold text-xl">T</span>
            </div>
            <span className="font-bold text-xl text-gray-900 hidden sm:block">
              TalentsLab
            </span>
          </Link>

          {/* Промо инфо в навбаре */}
          <div className="hidden md:flex items-center gap-3 px-4 py-2 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-full border border-indigo-200">
            <span className="text-sm font-semibold text-indigo-900">
              🔥 Скидка 30%
            </span>
            <span className="text-xs text-gray-600">
              21-23 ноября
            </span>
          </div>

          {/* Телефон и действия */}
          <div className="flex items-center gap-2 sm:gap-4">
            {/* Телефон */}
            <Link
              href="https://wa.me/77780281453"
              target="_blank"
              className="flex items-center gap-2 px-3 sm:px-4 py-2 bg-green-500 hover:bg-green-600 text-white rounded-full transition-colors text-sm sm:text-base font-semibold"
            >
              <Phone className="w-4 h-4" />
              <span className="hidden sm:inline">+7 778 028 14 53</span>
              <span className="sm:hidden">Связаться</span>
            </Link>

            {/* Кнопки входа */}
            <Link
              href="/auth/login"
              className="hidden sm:inline-flex px-4 py-2 text-gray-700 hover:text-gray-900 font-medium transition-colors"
            >
              Войти
            </Link>
          </div>
        </div>
      </div>
    </nav>
  )
}
