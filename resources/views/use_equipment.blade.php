@php $date = fn ($d) => $d ? $d->format('M j, Y') : ''; @endphp

<x-app-layout title="Use {{ $equipment->equipment_name }}">
    <div class="card mb-6 max-w-2xl p-5">
        <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm sm:grid-cols-3">
            <div><dt class="text-muted">Model</dt><dd class="font-medium">{{ $equipment->model }}</dd></div>
            <div><dt class="text-muted">Serial ID</dt><dd class="font-medium">{{ $equipment->serial_id }}</dd></div>
            <div><dt class="text-muted">Location ID</dt><dd class="font-medium tabular-nums">{{ $equipment->location?->location_id }}</dd></div>
            <div><dt class="text-muted">Location Name</dt><dd class="font-medium tabular-nums">{{ $equipment->location?->location_name }}</dd></div>
            <div><dt class="text-muted">Status</dt><dd class="font-medium">{{ ucfirst((string) ($equipment->status->value ?? $equipment->status)) }}</dd></div>
            <div><dt class="text-muted">Last maintenance</dt><dd class="font-medium">{{ $date($equipment->last_maintenance) }}</dd></div>
            <div><dt class="text-muted">Next maintenance</dt><dd class="font-medium">{{ $date($equipment->next_maintenance) }}</dd></div>
        </dl>
    </div>

    <form action="{{ route('equipment.use.update', $equipment->equipment_id) }}" method="POST" class="card max-w-2xl space-y-4 p-6">
        @csrf
        @method('PUT')
        <x-textarea name="notes" label="Notes (optional)" placeholder="What was it used for?" />
        <x-form-actions submit="Log usage" :cancel="url()->previous(route('equipment'))" />
    </form>
</x-app-layout>
