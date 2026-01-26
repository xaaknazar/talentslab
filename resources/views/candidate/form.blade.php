<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Candidate Form') }}
            @if($candidateId)
                <span class="text-sm font-normal text-gray-600 ml-2">({{ __('Editing') }})</span>
            @endif
        </h2>
    </x-slot>

    <div class="py-12">
        <livewire:candidate-form :candidate-id="$candidateId" />
    </div>
</x-app-layout> 