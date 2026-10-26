@php
    $c = $chemical ?? null;
    $split = fn ($v) => collect(is_array($v) ? $v : explode(',', (string) $v))->map(fn ($s) => trim($s))->filter()->values()->all();
    $units = collect(\App\Enums\Unit::cases())
    ->mapWithKeys(fn ($u) => [$u->value => ucfirst($u->quantityType())]);
    $quantityType = $units[old('unit', $c?->unit)] ?? $units->first(); 
    $locations = \App\Models\Location::orderBy('location_name')->pluck('location_name', 'location_id');
@endphp

<div class="grid gap-4 sm:grid-cols-2">
    <x-input name="chemical_name" label="Name" :value="$c?->chemical_name" class="sm:col-span-2" />
    <x-input name="batch_number" label="Batch / Lot Number" :value="$c?->batch_number" />
    <x-input name="brand_name" label="Brand" :value="$c?->brand_name" />
    <x-select name="location_id" label="Location" :options="$locations" :value="$c?->location_id" keyed placeholder="Select a location" />
    <x-select name="unit" label="Unit" :options="\App\Enums\Unit::cases()" :value="$c?->unit" />
    <x-input name="quantity_per_unit" :label="$quantityType . ' Per Container'" type="number" step="0.01" min="0" max="99999999" :value="$c?->quantity_per_unit" />
    <x-input name="quantity_received" label="Quantity received" type="number" step="0.01" min="0" max="99999999" :value="$c?->quantity_received" />
    <x-input name="current_quantity" label="Current Quantity" type="number" step="0.01" min="0" max="99999999" :value="$c?->current_quantity" />
    <x-input name="date_received" label="Date Received" type="date" :value="$c?->date_received?->format('Y-m-d')" />
    <x-input name="expiration_date" label="Expiration Date" type="date" :value="$c?->expiration_date?->format('Y-m-d')" />
    <x-chips name="safety_classes" label="Safety Classes" :cases="\App\Enums\SafetyClass::cases()" :selected="old('safety_classes', $split($c?->safety_classes))" class="sm:col-span-2" />
    <x-chips name="ghs_symbols" label="GHS Symbols" :cases="\App\Enums\GHSSymbol::cases()" :selected="old('ghs_symbols', $split($c?->ghs_symbols))" class="sm:col-span-2" />
    <script>
    (() => {
        const units = @js($units);
        const unit = document.getElementById('unit');
        const label = document.querySelector('label[for="quantity_per_unit"]');

        unit.addEventListener('change', () => {
            label.textContent = (units[unit.value] ?? 'Quantity') + ' Per Container';
        });
    })();
    </script>
</div>
