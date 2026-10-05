@php
    $isAdmin = Auth::user()->user_role->isRole(\App\Enums\UserRole::ADMIN);

    // name => [label, input type, step]; request names are search_{name}_min / _max
    $ranges = [
        'chemical_id' => ['Chemical ID', 'number', '1'],
        'location_id' => ['Location ID', 'number', '1'],
        'created_by' => ['Created by (user ID)', 'number', '1'],
        'volume_per_unit' => ['Volume per unit', 'number', '0.001'],
        'initial_quantity' => ['Initial quantity', 'number', '0.001'],
        'current_quantity' => ['Current quantity', 'number', '0.001'],
        'expiration_date' => ['Expiration date', 'date', null],
        'arrival_date' => ['Arrival date', 'date', null],
    ];
    $choices = [
        'search_safety_classes' => ['Safety class', \App\Enums\SafetyClass::cases()],
        'search_ghs_symbols' => ['GHS symbol', \App\Enums\GHSSymbol::cases()],
        'search_unit' => ['Unit', \App\Enums\Unit::cases()],
    ];

    $list = fn ($v) => collect(is_array($v) ? $v : explode(',', (string) $v))->map(fn ($s) => trim($s))->filter();
    $filters = collect(request()->except('page'))->filter(fn ($v) => $v !== null && $v !== '' && $v !== []);
@endphp

<x-app-layout title="Inventory">
    <x-slot:actions>
        @if ($isAdmin)
        <a href="{{ route('inventory.create') }}" class="btn btn-primary">Add chemical</a>
        @endif
    </x-slot:actions>

    <x-filter-bar :action="route('inventory')" placeholder="Search name, batch, or brand"
        :ranges="$ranges"
        :choices="$choices" sortable />

    <div id="inventory-table">
        <div class="card overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-line">
                    <tr>
                        <th class="th">Chemical</th>
                        <th class="th">Location</th>
                        <th class="th">Stock</th>
                        <th class="th">Expires</th>
                        <th class="th">Hazards</th>
                        <th class="th"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($data as $c)
                    @php
                        $initial = (float) $c->initial_quantity;
                        $current = (float) $c->current_quantity;
                        $pct = $initial > 0 ? max(0, min(100, $current / $initial * 100)) : 0;
                        $bar = $current <= 0 ? 'bg-red-600' : ($pct < 20 ? 'bg-amber-500' : 'bg-reagent-600');
                        $exp = \Illuminate\Support\Carbon::parse($c->expiration_date);
                        $expired = $exp->isPast();
                        $soon = ! $expired && $exp->isBefore(now()->addDays(30));
                    @endphp
                    <tr>
                        <td class="td">
                            <p class="font-medium">{{ $c->chemical_name }}</p>
                            <p class="text-xs text-muted">#{{ $c->chemical_id }}, batch {{ $c->batch_number }}, {{ $c->brand_name }}</p>
                        </td>
                        <td class="td">
                            <p class="font-medium">{{ $c->location?->location_name }}</p>
                            <p class="text-xs text-muted">#{{ $c->location?->location_id }}</p>
                        </td>
                        <td class="td">
                            <div class="h-1.5 w-24 overflow-hidden rounded-full bg-line" role="img" aria-label="{{ round($pct) }}% remaining">
                                <div class="h-full rounded-full {{ $bar }}" style="width: {{ $pct }}%"></div>
                            </div>
                            <p class="mt-1 whitespace-nowrap tabular-nums">{{ rtrim(rtrim(number_format($current, 3), '0'), '.') }}
                                <span class="text-muted">of {{ rtrim(rtrim(number_format($initial, 3), '0'), '.') }} {{ $c->unit }}</span>
                            </p>
                        </td>
                        <td class="td whitespace-nowrap">
                            <span class="tabular-nums">{{ $exp->format('M j, Y') }}</span>
                            @if ($expired)<span class="badge badge-danger ml-1">Expired</span>
                            @elseif ($soon)<span class="badge badge-warning ml-1">Soon</span>@endif
                        </td>
                        <td class="td">
                            <div class="flex max-w-56 flex-wrap gap-1">
                                @foreach ($list($c->safety_classes) as $class)<span class="badge badge-warning">{{ $class }}</span>@endforeach
                                @foreach ($list($c->ghs_symbols) as $ghs)<span class="badge badge-neutral">{{ $ghs }}</span>@endforeach
                            </div>
                        </td>
                        <td class="td">
                            <div class="flex justify-end gap-1.5">
                                <a href="{{ route('inventory.use.edit', $c->chemical_id) }}" class="btn btn-secondary btn-sm">Use</a>
                                @if ($isAdmin)
                                <a href="{{ route('inventory.edit', $c->chemical_id) }}" class="btn btn-secondary btn-sm">Edit</a>
                                <form action="{{ route('inventory.delete', $c->chemical_id) }}" method="POST" onsubmit="return confirm(@js('Delete ' . $c->chemical_name . '? This can\'t be undone.'))">
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
                            No chemicals match{{ $filters->isNotEmpty() ? ' these filters' : ' yet' }}.
                            @if ($filters->isNotEmpty())<a href="{{ route('inventory') }}" class="link">Clear filters</a>@endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">{{ $data->links('pagination::tailwind') }}</div>
    </div>
</x-app-layout>
