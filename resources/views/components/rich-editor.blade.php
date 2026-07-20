@props([
    'name',
    'value' => '',
    'placeholder' => '',
    'wireKey' => null,
])

<div wire:ignore
     @if ($wireKey) wire:key="{{ $wireKey }}" @endif
     x-data="richEditor(@js($value), @js($placeholder))"
     class="rounded-xl border overflow-hidden bg-white transition-shadow
            {{ $errors->has($name) ? 'border-red-300' : 'border-gray-200' }}">

    <div x-ref="body"></div>

    <input type="hidden" wire:model="{{ $name }}" x-ref="hiddenInput">
</div>
@error($name) <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
