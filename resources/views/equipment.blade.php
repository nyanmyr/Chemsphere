@php
    $isAdmin = Auth::user()->user_role->isRole(\App\Enums\UserRole::ADMIN);
    $tone = ['available' => 'badge-ok', 'unavailable' => 'badge-warning', 'under maintenance' => 'badge-warning', 'broken' => 'badge-danger'];
    $date = fn($d) => $d ? $d->format('M j, Y') : '';
@endphp

<x-app-layout title="Equipment">
    <x-slot:actions>
        @if ($isAdmin)<a href="{{ route('equipment.create') }}" class="btn btn-primary">Add equipment</a>@endif
    </x-slot:actions>

    <x-filter-bar :action="route('equipment')" placeholder="Search name, model, or serial" :ranges="[
        'equipment_id' => ['Equipment ID', 'number', '1'],
        'location_id' => ['Location ID', 'number', '1'],
        'created_by' => ['Created by (user ID)', 'number', '1'],
        'date_received' => ['Date Received', 'date', null],
        'preventive_maintenance_date' => ['Preventive maintenance date', 'date', null],
        'next_due' => ['Next due', 'date', null],
    ]" :choices="[
        'search_status' => ['Status', \App\Enums\EquipmentStatus::cases()],
        'search_alert_type' => ['Alert type', $alertTypes],
    ]" sortable />

    <div id="equipment-table">
        <div class="card overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-line">
                    <tr>
                        <th class="th">Equipment</th>
                        <th class="th">Location</th>
                        <th class="th">Status</th>
                        <th class="th">Maintenance</th>
                        <th class="th">
                            <span class="sr-only">
                                Actions
                            </span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($data as $e)
                        @php $status = (string) ($e->status->value ?? $e->status); @endphp
                        <tr>
                            <td class="td">
                                <p class="font-medium">
                                    {{ $e->equipment_name }}
                                </p>
                                <p class="text-xs text-muted">
                                    #{{ $e->equipment_id }}, {{ $e->model }}, serial
                                    {{ $e->serial_id }}
                                </p>
                            </td>
                            <td class="td">
                                <p class="font-medium">
                                    {{ $e->location?->location_name }}
                                </p>
                                <p class="text-xs text-muted">
                                    #{{ $e->location?->location_id }}
                                </p>
                            </td>
                            <td class="td">
                                <span class="badge {{ $tone[$status] ?? 'badge-neutral' }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                            <td class="td whitespace-nowrap">
                                @php $maintenance = $statuses[$e->equipment_id] ?? null; @endphp
                                <p class="text-xs text-muted">
                                    Last {{ $date($e->preventive_maintenance_date) }}
                                </p>
                                <p class="tabular-nums">
                                    Next due {{ $date($e->next_due) }}
                                    @if ($maintenance === \App\Enums\AlertType::MAINTENANCE_OVERDUE)
                                        <span class="badge badge-danger ml-1">
                                            Overdue
                                        </span>
                                    @elseif ($maintenance === \App\Enums\AlertType::MAINTENANCE_CLOSE_TO_DUE)
                                        <span class="badge badge-warning ml-1">
                                            Close to due
                                        </span>
                                    @endif
                                </p>
                            </td>
                            <td class="td">
                                <div class="flex justify-end gap-1.5">
                                    <a href="{{ route('equipment.use.edit', $e->equipment_id) }}"
                                        class="btn btn-secondary btn-sm">
                                        View
                                    </a>
                                    @if ($isAdmin)
                                        <a href="{{ route('equipment.edit', $e->equipment_id) }}"
                                            class="btn btn-secondary btn-sm">
                                            Edit
                                        </a>
                                        <form action="{{ route('equipment.delete', $e->equipment_id) }}" method="POST"
                                            onsubmit="return confirm(@js('Delete ' . $e->equipment_name . '?'))">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="td py-10 text-center text-muted">
                                No equipment found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $data->links('pagination::tailwind') }}</div>
    </div>
</x-app-layout>
