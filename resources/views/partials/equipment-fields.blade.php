@php $e = $equipment ?? null; @endphp
<div class="grid gap-4 sm:grid-cols-2">
    <x-input name="equipment_name" label="Name" :value="$e?->equipment_name" class="sm:col-span-2" />
    <x-input name="model" label="Model" :value="$e?->model" />
    <x-input name="serial_id" label="Serial ID" :value="$e?->serial_id" />
    <x-input name="location_id" label="Location ID" type="number" min="1" step="1" :value="$e?->location_id" />
    <x-select name="status" label="Status" :options="\App\Enums\EquipmentStatus::cases()" :value="$e?->status" />
    <x-input name="purchase_date" label="Purchase date" type="date" :value="$e?->purchase_date?->format('Y-m-d')" />
    <x-input name="warranty_expiration" label="Warranty expiration" type="date" :value="$e?->warranty_expiration?->format('Y-m-d')" />
    <x-input name="last_maintenance" label="Last maintenance" type="date" :value="$e?->last_maintenance?->format('Y-m-d')" />
    <x-input name="next_maintenance" label="Next maintenance" type="date" :value="$e?->next_maintenance?->format('Y-m-d')" />
</div>
