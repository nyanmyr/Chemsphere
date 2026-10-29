@php
    $c = $chemical ?? null;
    $split = fn ($value) => collect(explode(',', (string) $value))->map(fn ($item) => trim($item))->filter()->values()->all();
@endphp

<div class="grid gap-4 sm:grid-cols-2">
    <x-input name="chemical_name" label="Chemical Name" :value="$c?->chemical_name" class="sm:col-span-2" />
    <x-chips name="safety_classes" label="Safety Classes" :cases="\App\Enums\SafetyClass::cases()" :selected="old('safety_classes', $split($c?->safety_classes))" class="sm:col-span-2" />
    <x-chips name="ghs_symbols" label="GHS Symbols" :cases="\App\Enums\GHSSymbol::cases()" :selected="old('ghs_symbols', $split($c?->ghs_symbols))" class="sm:col-span-2" />
    <p class="text-sm text-muted sm:col-span-2">
        This creates one chemical category. Add one or more batches after saving it; batch number, brand, quantity, location, and dates belong to each batch.
    </p>
</div>
