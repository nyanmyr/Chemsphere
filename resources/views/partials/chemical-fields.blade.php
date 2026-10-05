@php
    $c = $chemical ?? null;
    $split = fn ($v) => collect(is_array($v) ? $v : explode(',', (string) $v))->map(fn ($s) => trim($s))->filter()->values()->all();
@endphp

<div class="grid gap-4 sm:grid-cols-2">
    <x-input name="chemical_name" label="Name" :value="$c?->chemical_name" class="sm:col-span-2" />
    <x-input name="batch_number" label="Batch number" :value="$c?->batch_number" />
    <x-input name="brand_name" label="Brand" :value="$c?->brand_name" />
    <x-input name="location_id" label="Location ID" type="number" min="1" step="1" :value="$c?->location_id" />
    <x-select name="unit" label="Unit" :options="\App\Enums\Unit::cases()" :value="$c?->unit" />
    <x-input name="volume_per_unit" label="Volume per unit" type="number" step="0.001" min="0" max="9999999999" :value="$c?->volume_per_unit" />
    <x-input name="initial_quantity" label="Initial quantity" type="number" step="0.001" min="0" max="9999999999" :value="$c?->initial_quantity" />
    <x-input name="current_quantity" label="Current quantity" type="number" step="0.001" min="0" max="9999999999" :value="$c?->current_quantity" />
    <x-input name="arrival_date" label="Arrival date" type="date" :value="$c?->arrival_date?->format('Y-m-d')" />
    <x-input name="expiration_date" label="Expiration date" type="date" :value="$c?->expiration_date?->format('Y-m-d')" />
    <x-chips name="safety_classes" label="Safety classes" :cases="\App\Enums\SafetyClass::cases()" :selected="old('safety_classes', $split($c?->safety_classes))" class="sm:col-span-2" />
    <x-chips name="ghs_symbols" label="GHS symbols" :cases="\App\Enums\GHSSymbol::cases()" :selected="old('ghs_symbols', $split($c?->ghs_symbols))" class="sm:col-span-2" />
</div>
