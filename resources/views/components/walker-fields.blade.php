@props([
    'group',
    'index',
    'walker' => [],
    'category',
])

@php
    $prefix = str_replace('_', '-', $group).'-'.$index;
@endphp

<div class="border-t border-hedge-900/10 py-6 first:border-t-0 first:pt-2 last:pb-1" data-walker-row>
    <div class="flex items-center justify-between gap-4">
        <h4 class="min-w-0 font-semibold text-hedge-900"><span data-walker-label>{{ $category }} walker</span></h4>
        <button class="shrink-0 border border-hedge-700 px-3 py-1.5 text-base font-semibold text-hedge-800 hover:bg-hedge-50 sm:text-sm" type="button" data-remove-walker>Remove</button>
    </div>
    <div class="mt-5 grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
        <div class="sm:col-span-2 lg:col-span-1">
            <label class="field-label" for="{{ $prefix }}-name">Full name *</label>
            <input class="field-control" id="{{ $prefix }}-name" name="{{ $group }}[{{ $index }}][name]" type="text" value="{{ $walker['name'] ?? '' }}" autocomplete="off" required @error("{$group}.{$index}.name") aria-invalid="true" aria-describedby="{{ $prefix }}-name-error" @enderror>
            @error("{$group}.{$index}.name")<p id="{{ $prefix }}-name-error" class="mt-2 font-semibold text-barn-700">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="field-label" for="{{ $prefix }}-age">Age *</label>
            <input class="field-control tabular-nums" id="{{ $prefix }}-age" name="{{ $group }}[{{ $index }}][age]" type="number" value="{{ $walker['age'] ?? '' }}" min="0" max="120" inputmode="numeric" required @error("{$group}.{$index}.age") aria-invalid="true" aria-describedby="{{ $prefix }}-age-error" @enderror>
            @error("{$group}.{$index}.age")<p id="{{ $prefix }}-age-error" class="mt-2 font-semibold text-barn-700">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="field-label" for="{{ $prefix }}-gender">Gender *</label>
            <span class="grid grid-cols-[1fr_2rem]">
                <select class="field-control col-span-full row-start-1 appearance-none pr-8" id="{{ $prefix }}-gender" name="{{ $group }}[{{ $index }}][gender]" required @error("{$group}.{$index}.gender") aria-invalid="true" aria-describedby="{{ $prefix }}-gender-error" @enderror>
                    <option value="">Select</option>
                    @foreach(['M', 'F', 'Other', 'Prefer not to say'] as $option)
                        <option value="{{ $option }}" @selected(($walker['gender'] ?? '') === $option)>{{ $option }}</option>
                    @endforeach
                </select>
                <svg viewBox="0 0 8 5" width="8" height="5" fill="none" class="pointer-events-none col-start-2 row-start-1 place-self-center text-hedge-700" aria-hidden="true"><path d="M.5.5 4 4 7.5.5" stroke="currentColor" /></svg>
            </span>
            @error("{$group}.{$index}.gender")<p id="{{ $prefix }}-gender-error" class="mt-2 font-semibold text-barn-700">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="field-label" for="{{ $prefix }}-postcode">Postcode *</label>
            <input class="field-control" id="{{ $prefix }}-postcode" name="{{ $group }}[{{ $index }}][postcode]" type="text" value="{{ $walker['postcode'] ?? '' }}" autocomplete="postal-code" required @error("{$group}.{$index}.postcode") aria-invalid="true" aria-describedby="{{ $prefix }}-postcode-error" @enderror>
            @error("{$group}.{$index}.postcode")<p id="{{ $prefix }}-postcode-error" class="mt-2 font-semibold text-barn-700">{{ $message }}</p>@enderror
        </div>
    </div>
</div>
