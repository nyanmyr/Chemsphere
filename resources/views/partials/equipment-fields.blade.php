@php
    $e = $equipment ?? null;
@endphp

<div class="grid gap-4 sm:grid-cols-2">
    <x-input name="equipment_name" label="Name" :value="$e?->equipment_name" class="sm:col-span-2" />
    <x-input name="model" label="Model" :value="$e?->model" />
    <x-input name="serial_id" label="Serial ID" :value="$e?->serial_id" />
    <x-input name="location_id" label="Location ID" type="number" min="1" step="1" :value="$e?->location_id" />
    <x-select name="status" label="Status" :options="\App\Enums\EquipmentStatus::cases()" :value="$e?->status" />
    <x-input name="date_received" label="Date Received" type="date" :value="$e?->date_received?->format('Y-m-d')" />
    <x-input name="warranty_expiration" label="Warranty Expiration" type="date" :value="$e?->warranty_expiration?->format('Y-m-d')" />
    <x-input name="last_maintenance" label="Last Maintenance" type="date" :value="$e?->last_maintenance?->format('Y-m-d')" />
    <x-input name="next_maintenance" label="Next Maintenance" type="date" :value="$e?->next_maintenance?->format('Y-m-d')" />
</div>
