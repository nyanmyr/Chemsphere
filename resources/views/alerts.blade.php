@php
    $tone = fn ($type) => in_array($type?->value, ['expired', 'out of stock']) ? 'badge-danger' : 'badge-warning';
@endphp

<x-app-layout title="Alerts">
    <div id="alerts-table">
        <div class="card overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-line">
                    <tr>
                        <th class="th">Alert</th>
                        <th class="th">Chemical</th>
                        <th class="th">Received</th>
                        <th class="th"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($data as $alert)
                    <tr class="{{ $alert->read_at ? 'text-muted' : '' }}">
                        <td class="td">
                            <span class="badge {{ $tone($alert->type) }}">{{ ucfirst($alert->type?->value ?? 'alert') }}</span>
                            <p class="mt-1 {{ $alert->read_at ? '' : 'font-medium' }}">{{ $alert->message }}</p>
                        </td>
                        <td class="td">{{ $alert->chemical?->chemical_name }}</td>
                        <td class="td whitespace-nowrap">{{ $alert->created_at->diffForHumans() }}</td>
                        <td class="td text-right">
                            @if ($alert->read_at)
                            <span class="text-xs">Read {{ $alert->read_at->diffForHumans() }}</span>
                            @else
                            <form action="{{ route('alerts.read', $alert->alert_id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-secondary btn-sm">Mark as read</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="td py-10 text-center text-muted">No alerts. Anything expiring or running low will show up here.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $data->links('pagination::tailwind') }}</div>
    </div>
</x-app-layout>
