@php $n = fn ($v) => rtrim(rtrim(number_format((float) $v, 3), '0'), '.'); @endphp

<x-app-layout title="Usage logs">
    <x-filter-bar :action="route('usage_logs')" placeholder="Search notes"
        :ranges="[
            'usage_log_id' => ['Log ID', 'number', '1'],
            'created_by' => ['Used by (user ID)', 'number', '1'],
            'location_id' => ['Location ID', 'number', '1'],
            'item_id' => ['Item ID', 'number', '1'],
            'quantity_used' => ['Quantity used', 'number', '0.001'],
            'quantity_remaining' => ['Quantity remaining', 'number', '0.001'],
        ]"
        :choices="['search_item_type' => ['Item type', \App\Enums\ItemType::cases()]]" sortable />

    <div id="usage-logs-table">
        <div class="card overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-line">
                    <tr>
                        <th class="th">When</th>
                        <th class="th">Item</th>
                        <th class="th">Used</th>
                        <th class="th">Remaining</th>
                        <th class="th">Created By</th>
                        <th class="th">Location</th>
                        <th class="th">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($data as $log)
                    <tr>
                        <td class="td whitespace-nowrap">
                            {{ $log->created_at->format('M j, Y g:i A') }}
                            <p class="text-xs text-muted">Log #{{ $log->usage_log_id }}</p>
                        </td>
                        <td class="td whitespace-nowrap"><span class="badge badge-neutral">{{ ucfirst((string) ($log->item_type->value ?? $log->item_type)) }}</span> <span class="tabular-nums text-muted">#{{ $log->item_id }}</span></td>
                        <td class="td tabular-nums">{{ $n($log->quantity_used) }}</td>
                        <td class="td tabular-nums">{{ $n($log->quantity_remaining) }}</td>
                        <td class="td">
                            <p class="font-medium">{{ $log->user?->email }}</p>
                            <p class="text-xs text-muted">#{{ $log->user?->user_id }}</p>
                        </td>
                        <td class="td">
                            <p class="font-medium">{{ $log->location?->location_name }}</p>
                            <p class="text-xs text-muted">#{{ $log->location?->location_id }}</p>
                        </td>
                        <td class="td">
                            @if (filled($log->notes))
                            <button type="button" class="btn btn-secondary btn-sm" aria-haspopup="dialog"
                                onclick="document.getElementById('note-{{ $log->usage_log_id }}').showModal()">View notes</button>

                            <dialog id="note-{{ $log->usage_log_id }}" aria-labelledby="note-title-{{ $log->usage_log_id }}"
                                onclick="if (event.target === this) this.close()"
                                class="m-auto w-full max-w-md rounded-lg border border-line p-0 text-ink backdrop:bg-ink/40">
                                <div class="p-5">
                                    <h2 id="note-title-{{ $log->usage_log_id }}" class="font-semibold">Notes</h2>
                                    <p class="mt-0.5 text-xs text-muted">Log #{{ $log->usage_log_id }}, {{ $log->created_at->format('M j, Y g:i A') }}</p>
                                    <p class="mt-4 whitespace-pre-wrap break-words text-sm">{{ $log->notes }}</p>
                                    <form method="dialog" class="mt-5 text-right">
                                        <button class="btn btn-secondary">Close</button>
                                    </form>
                                </div>
                            </dialog>
                            @else
                            <span class="text-muted">None</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="td py-10 text-center text-muted">No usage logged yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $data->links('pagination::tailwind') }}</div>
    </div>
</x-app-layout>
