@php
    $isAdmin = Auth::user()->user_role->isRole(\App\Enums\UserRole::ADMIN);
    $ranges = [
        'chemical_id' => ['Category ID', 'number', '1'],
        'location_id' => ['Location ID', 'number', '1'],
        'created_by' => ['Created by (user ID)', 'number', '1'],
        'quantity_per_unit' => ['Quantity per unit', 'number', '0.01'],
        'quantity_received' => ['Quantity received', 'number', '1'],
        'current_quantity' => ['Current quantity', 'number', '1'],
        'expiration_date' => ['Expiration date', 'date', null],
        'date_received' => ['Date received', 'date', null],
    ];
    $choices = [
        'search_safety_classes' => ['Safety class', \App\Enums\SafetyClass::cases()],
        'search_ghs_symbols' => ['GHS symbol', \App\Enums\GHSSymbol::cases()],
        'search_unit' => ['Unit', \App\Enums\Unit::cases()],
        'search_alert_type' => ['Expiration status', $alertTypes],
    ];

    $list = fn ($value) => collect(is_array($value) ? $value : explode(',', (string) $value))->map(fn ($item) => trim($item))->filter();
    $filters = collect(request()->except('page'))->filter(fn ($value) => $value !== null && $value !== '' && $value !== []);
    $n = fn ($value) => rtrim(rtrim(number_format((float) $value, 3), '0'), '.');
@endphp

<x-app-layout title="Inventory">
    <x-slot:actions>
        @if ($isAdmin)
            <a href="{{ route('inventory.create') }}" class="btn btn-primary">Create Chemical Category</a>
        @endif
    </x-slot:actions>

    <x-filter-bar :action="route('inventory')" placeholder="Search chemical name, batch / lot number, or brand" :ranges="$ranges"
        :choices="$choices" sortable />

    <div id="inventory-table">
        <div class="card overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-line">
                    <tr>
                        <th class="th">Chemical Category</th>
                        <th class="th">Batches</th>
                        <th class="th">Total Stock</th>
                        <th class="th">Next Expiration</th>
                        <th class="th">Hazards</th>
                        <th class="th"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($data as $chemical)
                        @php
                            $initial = (float) ($chemical->quantity_received_total ?? 0);
                            $current = (float) ($chemical->current_quantity_total ?? 0);
                            $pct = $initial > 0 ? max(0, min(100, $current / $initial * 100)) : 0;
                            $bar = $current <= 0 ? 'bg-red-600' : ($pct < 20 ? 'bg-amber-500' : 'bg-reagent-600');
                            $exp = $chemical->next_expiration_date ? \Illuminate\Support\Carbon::parse($chemical->next_expiration_date) : null;
                        @endphp
                        <tr>
                            <td class="td">
                                <p class="font-semibold">{{ $chemical->chemical_name }}</p>
                                <p class="text-xs text-muted">Category #{{ $chemical->chemical_id }}</p>
                            </td>
                            <td class="td">
                                <p class="font-medium tabular-nums">{{ $chemical->batches_count }}</p>
                                <p class="text-xs text-muted">{{ \Illuminate\Support\Str::plural('batch', $chemical->batches_count) }} recorded</p>
                            </td>
                            <td class="td">
                                <div class="h-1.5 w-24 overflow-hidden rounded-full bg-line" role="img" aria-label="{{ round($pct) }}% remaining">
                                    <div class="h-full rounded-full {{ $bar }}" style="width: {{ $pct }}%"></div>
                                </div>
                                <p class="mt-1 whitespace-nowrap tabular-nums">
                                    {{ $n($current) }} <span class="text-muted">of {{ $n($initial) }} containers</span>
                                </p>
                            </td>
                            <td class="td whitespace-nowrap">
                                @if ($exp)
                                    <span class="tabular-nums">{{ $exp->format('M j, Y') }}</span>
                                    @if ($exp->lte(today()))
                                        <span class="badge badge-danger ml-1">Expired</span>
                                    @elseif ($exp->lte(today()->addMonthsNoOverflow(3)))
                                        <span class="badge badge-warning ml-1">Close to expiration</span>
                                    @endif
                                @else
                                    <span class="text-muted">No batches yet</span>
                                @endif
                            </td>
                            <td class="td">
                                <div class="flex max-w-56 flex-wrap gap-1">
                                    @foreach ($list($chemical->safety_classes) as $class)
                                        <span class="badge badge-warning">{{ $class }}</span>
                                    @endforeach
                                    @foreach ($list($chemical->ghs_symbols) as $ghs)
                                        <span class="badge badge-neutral">{{ $ghs }}</span>
                                    @endforeach
                                </div>
                            </td>
                            <td class="td">
                                <div class="flex justify-end gap-1.5">
                                    <a href="{{ route('inventory.show', $chemical->chemical_id) }}" class="btn btn-secondary btn-sm">Open batches</a>
                                    @if ($isAdmin)
                                        <a href="{{ route('inventory.edit', $chemical->chemical_id) }}" class="btn btn-secondary btn-sm">Edit</a>
                                        <form action="{{ route('inventory.delete', $chemical->chemical_id) }}" method="POST"
                                            onsubmit="return confirm(@js('Delete ' . $chemical->chemical_name . ' and all of its batches? This can\'t be undone.'))">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="td py-10 text-center text-muted">
                                No chemical categories match{{ $filters->isNotEmpty() ? ' these filters' : ' yet' }}.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $data->links('pagination::tailwind') }}</div>
    </div>
</x-app-layout>
