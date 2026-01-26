<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <h1 class="text-2xl font-bold mb-8">{{ __('Welcome') }}, {{ Auth::user()->name }}!</h1>

                <div class="max-w-xl">
                    <!-- Резюме -->
                    <div class="bg-white p-6 rounded-lg shadow-md border border-gray-200">
                        <h2 class="text-xl font-semibold mb-4">{{ __('Resume') }}</h2>
                        @if($candidate)
                            <div class="mb-4">
                                <div class="flex items-center mb-2">
                                    <div class="w-3 h-3 rounded-full {{ $candidate->step >= 5 ? 'bg-green-500' : 'bg-yellow-500' }} mr-2"></div>
                                    <p class="text-gray-600">
                                        {{ __('Status') }}: {{ $candidate->step >= 5 ? __('Completed') : __('In progress') }}
                                    </p>
                                </div>
                                @if($candidate->step < 5)
                                    <p class="text-sm text-gray-500">{{ __('Current step') }}: {{ $candidate->step }} {{ __('of') }} 4</p>
                                @endif
                            </div>
                            <div class="flex gap-3">
                                <a href="{{ route('candidate.form', ['id' => $candidate->id]) }}" class="inline-flex items-center px-4 py-2 bg-blue-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-200 disabled:opacity-25 transition">
                                    {{ __('Edit resume') }}
                                </a>
                                @if($candidate->step >= 5)
                                    <a href="{{ route('candidate.report', $candidate) }}" class="inline-flex items-center px-4 py-2 bg-green-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-600 active:bg-green-700 focus:outline-none focus:border-green-700 focus:ring focus:ring-green-200 disabled:opacity-25 transition">
                                        {{ __('View resume') }}
                                    </a>
                                @endif
                            </div>
                            @if($lastUpdate)
                                <p class="mt-2 text-sm text-gray-500">{{ __('updated') }} {{ $lastUpdate }}</p>
                            @endif
                        @else
                        <p class="text-gray-600 mb-4">{{ __('Fill out your resume to participate in the selection.') }}</p>
                        <a href="{{ route('candidate.form') }}" class="inline-flex items-center px-4 py-2 bg-orange-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-600 active:bg-orange-700 focus:outline-none focus:border-orange-700 focus:ring focus:ring-orange-200 disabled:opacity-25 transition">
                            {{ __('Create resume') }}
                        </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
