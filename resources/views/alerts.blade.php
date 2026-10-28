@php
    $equipmentTypes = [App\Enums\AlertType::MAINTENANCE_CLOSE_TO_DUE, App\Enums\AlertType::MAINTENANCE_OVERDUE];
    $tone = fn($type) => in_array($type?->value, ['expired', 'out of stock', 'maintenance overdue']) ? 'badge-danger' : 'badge-warning';
@endphp

<x-app-layout title="Alerts">
    <div id="alerts-table">
        <div class="card overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-line">
                    <tr>
                        <th class="th">Message</th>
                        <th class="th">Item</th>
                        <th class="th">Received</th>
                        <th class="th"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($data as $alert)
                        <tr class="{{ $alert->read_at ? 'text-muted' : '' }}">
                            <td class="td">
                                <span class="badge {{ $tone($alert->alert_type) }}">
                                    {{ ucfirst((string) ($alert->alert_type->value ?? $alert->alert_type)) }}
                                </span>
                                <p class="mt-1 {{ $alert->read_at ? '' : 'font-medium' }}">
                                    {{ $alert->message }}
                                </p>
                            </td>
                            <td class="td">
                                @if (in_array($alert->alert_type, $equipmentTypes, true))
                                    <p class="font-medium">
                                        {{ $alert->equipment?->equipment_name }}
                                    </p>
                                    <p class="text-xs text-muted">
                                        #{{ $alert->equipment?->equipment_id }}, {{ $alert->equipment?->model }}, serial
                                        {{ $alert->equipment?->serial_id }}
                                    </p>
                                @else
                                    <p class="font-medium">
                                        {{ $alert->chemicalContainer?->chemical?->chemical_name ?? $alert->chemical?->chemical_name }}
                                    </p>
                                    <p class="text-xs text-muted">
                                        #{{ $alert->chemicalContainer?->chemical_container_id ?? $alert->chemical?->chemical_id }}, batch {{ $alert->chemicalContainer?->batch_number ?? $alert->chemical?->batch_number }},
                                        {{ $alert->chemicalContainer?->brand_name ?? $alert->chemical?->brand_name }}
                                    </p>
                                @endif
                            </td>
                            <td class="td">
                                <p class="font-medium whitespace-nowrap">
                                    {{ $alert->created_at->diffForHumans() }}
                                </p>
                                <p class="text-xs text-muted whitespace-nowrap">
                                    {{ $alert->created_at }}
                                </p>
                            </td>
                            <td class="td text-right">
                                @if ($alert->read_at)
                                    <p class="font-medium whitespace-nowrap">
                                        {{ $alert->read_at->diffForHumans() }}
                                    </p>
                                    <p class="text-xs text-muted whitespace-nowrap">
                                        {{ $alert->read_at }}
                                    </p>
                                @else
                                    <form action="{{ route('alerts.read', $alert->alert_id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-secondary btn-sm">
                                            Mark as read
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="td py-10 text-center text-muted">
                                No alerts. Anything expiring, running low, or
                                due for maintenance will show up here.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $data->links('vendor.pagination.tailwind') }}</div>
    </div>
</x-app-layout>
