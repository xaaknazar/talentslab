<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Импорт кандидатов
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h1 class="text-2xl font-bold mb-6">Импорт кандидатов из Excel</h1>

                @if(session('success'))
                    <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('output'))
                    <div class="mb-6 p-4 bg-gray-100 border border-gray-300 rounded">
                        <h3 class="font-semibold mb-2">Результат импорта:</h3>
                        <pre class="text-sm whitespace-pre-wrap">{{ session('output') }}</pre>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('import.candidates') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div>
                        <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                            Выберите Excel файл (.xlsx)
                        </label>
                        <input type="file"
                               name="file"
                               id="file"
                               accept=".xlsx,.xls"
                               class="block w-full text-sm text-gray-500
                                      file:mr-4 file:py-2 file:px-4
                                      file:rounded-md file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-blue-50 file:text-blue-700
                                      hover:file:bg-blue-100"
                               required>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded p-4">
                        <h3 class="font-semibold text-yellow-800 mb-2">Важно:</h3>
                        <ul class="text-sm text-yellow-700 list-disc list-inside space-y-1">
                            <li>Используйте файл с тем же форматом, что и при экспорте</li>
                            <li>Первая строка должна содержать заголовки</li>
                            <li>Кандидаты без email и телефона будут пропущены</li>
                            <li>Существующие кандидаты (по email/телефону) будут обновлены</li>
                        </ul>
                    </div>

                    <div class="flex gap-4">
                        <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-800 focus:ring focus:ring-blue-200 disabled:opacity-25 transition">
                            Импортировать
                        </button>

                        <a href="{{ route('export.candidates') }}"
                           class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-800 focus:outline-none focus:border-green-800 focus:ring focus:ring-green-200 disabled:opacity-25 transition">
                            Скачать текущий экспорт
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
