@props([
    'index',
    'dog' => [],
    'ageDateLabel' => 'event day',
])

@php
    $prefix = 'dogs-'.$index;
@endphp

<div class="border-t border-hedge-900/10 py-6 first:border-t-0 first:pt-2 last:pb-1" data-dog-row>
    <div class="flex items-center justify-between gap-4">
        <h4 class="min-w-0 font-semibold text-hedge-900"><span data-dog-label>Dog</span></h4>
        <button class="shrink-0 border border-hedge-700 px-3 py-1.5 text-base font-semibold text-hedge-800 hover:bg-hedge-50 sm:text-sm" type="button" data-remove-dog>Remove</button>
    </div>
    <div class="mt-5 grid gap-5 sm:grid-cols-2">
        <div>
            <label class="field-label" for="{{ $prefix }}-name">Dog’s name *</label>
            <input class="field-control" id="{{ $prefix }}-name" name="dogs[{{ $index }}][name]" type="text" value="{{ $dog['name'] ?? '' }}" autocomplete="off" required @error("dogs.{$index}.name") aria-invalid="true" aria-describedby="{{ $prefix }}-name-error" @enderror>
            @error("dogs.{$index}.name")<p id="{{ $prefix }}-name-error" class="mt-2 font-semibold text-barn-700">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="field-label" for="{{ $prefix }}-age">Age on {{ $ageDateLabel }} *</label>
            <input class="field-control tabular-nums" id="{{ $prefix }}-age" name="dogs[{{ $index }}][age]" type="number" value="{{ $dog['age'] ?? '' }}" min="0" max="30" inputmode="numeric" required @error("dogs.{$index}.age") aria-invalid="true" aria-describedby="{{ $prefix }}-age-error" @enderror>
            @error("dogs.{$index}.age")<p id="{{ $prefix }}-age-error" class="mt-2 font-semibold text-barn-700">{{ $message }}</p>@enderror
        </div>
    </div>
</div>
