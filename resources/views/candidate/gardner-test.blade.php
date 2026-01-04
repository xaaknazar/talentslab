<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Gardner Test - Multiple Intelligences') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('All 45 questions on one page') }}</h3>
                        <p class="text-sm text-gray-600">{{ __('Answer questions in any order') }}</p>
                    </div>

                    @livewire('gardner-test-all-questions')
                </div>
            </div>
        </div>
    </div>
</x-app-layout> 