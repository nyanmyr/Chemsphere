@php
    $b = $batch ?? null;
    $locationOptions = $locations->pluck('location_name', 'location_id');
    $units = collect(\App\Enums\Unit::cases())->mapWithKeys(fn ($unit) => [$unit->value => ucfirst($unit->quantityType())])->all();
@endphp

<div class="grid gap-4 sm:grid-cols-2">
    <x-input name="batch_number" label="Batch / Lot Number" :value="$b?->batch_number" />
    <x-input name="brand_name" label="Brand" :value="$b?->brand_name" />
    <x-select name="location_id" label="Storage Location" :options="$locationOptions" :value="$b?->location_id" keyed placeholder="Select a location" />
    <x-select name="unit" label="Unit" :options="\App\Enums\Unit::cases()" :value="$b?->unit" />
    <x-input name="quantity_per_unit" label="Quantity Per Container" type="number" step="0.01" min="0" max="99999999.99" :value="$b?->quantity_per_unit" />
    <x-input name="quantity_received" label="Quantity Received (containers)" type="number" min="1" max="99999999" :value="$b?->quantity_received" />
    <x-input name="current_quantity" label="Current Quantity (containers)" type="number" min="0" max="99999999" :value="$b?->current_quantity" />
    <x-input name="date_received" label="Date Received" type="date" :value="$b?->date_received?->format('Y-m-d')" />
    <x-input name="expiration_date" label="Expiration Date" type="date" :value="$b?->expiration_date?->format('Y-m-d')" />
    <script>
        (() => {
            const units = @js($units);
            const unit = document.getElementById('unit');
            const label = document.querySelector('label[for="quantity_per_unit"]');

            if (unit && label) {
                const updateLabel = () => {
                    label.textContent = (units[unit.value] ?? 'Quantity') + ' Per Container';
                };
                unit.addEventListener('change', updateLabel);
                updateLabel();
            }
        })();
    </script>
</div>
