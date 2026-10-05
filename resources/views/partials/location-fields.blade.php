@php
    $l = $location ?? null;
@endphp

<div class="space-y-4">
    <x-input name="location_name" label="Name" :value="$l?->location_name" />
    <x-textarea name="description" label="Description" :value="$l?->description" placeholder="Shelf, room, or cabinet details" />
</div>
