@php
    $isAdmin = Auth::user()->user_role->isRole(\App\Enums\UserRole::ADMIN);
    $tone = ['available' => 'badge-ok', 'unavailable' => 'badge-warning', 'under maintenance' => 'badge-warning', 'broken' => 'badge-danger'];
    $date = fn ($d) => $d ? $d->format('M j, Y') : '';
@endphp

<x-app-layout title="Equipment">
    <x-slot:actions>
        @if ($isAdmin)<a href="{{ route('equipment.create') }}" class="btn btn-primary">Add equipment</a>@endif
    </x-slot:actions>

    <x-filter-bar :action="route('equipment')" placeholder="Search name, model, or serial"
        :ranges="[
            'equipment_id' => ['Equipment ID', 'number', '1'],
            'location_id' => ['Location ID', 'number', '1'],
            'created_by' => ['Created by (user ID)', 'number', '1'],
            'purchase_date' => ['Purchase date', 'date', null],
            'warranty_expiration' => ['Warranty expiration', 'date', null],
            'last_maintenance' => ['Last maintenance', 'date', null],
            'next_maintenance' => ['Next maintenance', 'date', null],
        ]"
        :choices="['search_status' => ['Status', \App\Enums\EquipmentStatus::cases()]]" sortable />

    <div id="equipment-table">
        <div class="card overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-line">
                    <tr>
                        <th class="th">Equipment</th>
                        <th class="th">Location</th>
                        <th class="th">Status</th>
                        <th class="th">Warranty</th>
                        <th class="th">Maintenance</th>
                        <th class="th"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($data as $e)
                    @php $status = (string) ($e->status->value ?? $e->status); @endphp
                    <tr>
                        <td class="td">
                            <p class="font-medium">{{ $e->equipment_name }}</p>
                            <p class="text-xs text-muted">#{{ $e->equipment_id }}, {{ $e->model }}, serial {{ $e->serial_id }}</p>
                        </td>
                        <td class="td">
                            <p class="font-medium">{{ $e->location?->location_name }}</p>
                            <p class="text-xs text-muted">#{{ $e->location?->location_id }}</p>
                        </td>
                        <td class="td"><span class="badge {{ $tone[$status] ?? 'badge-neutral' }}">{{ ucfirst($status) }}</span></td>
                        <td class="td whitespace-nowrap">
                            <span class="tabular-nums">{{ $date($e->warranty_expiration) }}</span>
                            @if ($e->warranty_expiration?->isPast())<span class="badge badge-neutral ml-1">Expired</span>@endif
                        </td>
                        <td class="td whitespace-nowrap">
                            <p class="text-xs text-muted">Last {{ $date($e->last_maintenance) }}</p>
                            <p class="tabular-nums">Next {{ $date($e->next_maintenance) }}
                                @if ($e->next_maintenance?->isPast())<span class="badge badge-danger ml-1">Overdue</span>@endif
                            </p>
                        </td>
                        <td class="td">
                            <div class="flex justify-end gap-1.5">
                                <a href="{{ route('equipment.use.edit', $e->equipment_id) }}" class="btn btn-secondary btn-sm">Use</a>
                                @if ($isAdmin)
                                <a href="{{ route('equipment.edit', $e->equipment_id) }}" class="btn btn-secondary btn-sm">Edit</a>
                                <form action="{{ route('equipment.delete', $e->equipment_id) }}" method="POST" onsubmit="return confirm(@js('Delete ' . $e->equipment_name . '?'))">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="td py-10 text-center text-muted">No equipment found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $data->links('pagination::tailwind') }}</div>
    </div>
</x-app-layout>
