@php
    $isAdmin = Auth::user()->user_role->isRole(\App\Enums\UserRole::ADMIN);
    $list = fn ($value) => collect(explode(',', (string) $value))->map(fn ($item) => trim($item))->filter();
    $ranges = [
        'chemical_id' => ['Chemical ID', 'number', '1'],
        'location_id' => ['Location ID', 'number', '1'],
        'created_by' => ['Created by (user ID)', 'number', '1'],
        'quantity_per_unit' => ['Quantity per container', 'number', '0.01'],
        'quantity_received' => ['Quantity received (containers)', 'number', '1'],
        'current_quantity' => ['Current quantity (containers)', 'number', '1'],
        'expiration_date' => ['Expiration date', 'date', null],
        'date_received' => ['Date received', 'date', null],
    ];
    $choices = [
        'search_safety_classes' => ['Safety class', \App\Enums\SafetyClass::cases()],
        'search_ghs_symbols' => ['GHS symbol', \App\Enums\GHSSymbol::cases()],
        'search_unit' => ['Unit', \App\Enums\Unit::cases()],
        'search_alert_type' => ['Inventory status', $alertTypes],
    ];
    $activeFilters = collect(request()->except('page', 'sort'))->filter(fn ($value) => $value !== null && $value !== '' && $value !== []);
    $formatQuantity = fn ($value) => rtrim(rtrim(number_format((float) $value, 3), '0'), '.');
    $clearUrl = request()->filled('sort') ? route('inventory') . '?' . http_build_query(request()->only('sort')) : route('inventory');
@endphp

<x-app-layout title="Chemical inventory">
    <x-slot:actions>
        @if ($isAdmin)
            <a href="{{ route('inventory.create') }}" class="btn btn-primary">Add Chemical</a>
        @endif
    </x-slot:actions>

    {{-- Keep the existing filter-bar visual language while applying filters to the chemical/container model. --}}
    <form action="{{ route('inventory') }}" method="GET" class="card mb-6">
        <div class="flex flex-wrap gap-2 p-3">
            <input type="search" name="search" value="{{ request('search') }}"
                placeholder="Search name, batch / lot number, or brand" aria-label="Search inventory"
                class="field min-w-56 flex-1">
            <select name="sort" aria-label="Sort order" onchange="this.form.submit()" class="field w-auto">
                <option value="recent" @selected(request('sort', 'recent') === 'recent')>Most recent</option>
                <option value="oldest" @selected(request('sort') === 'oldest')>Oldest first</option>
                <option value="alphabetical" @selected(request('sort') === 'alphabetical')>Alphabetical (A–Z)</option>
            </select>
            <button type="submit" class="btn btn-primary">Search</button>
            @if ($activeFilters->isNotEmpty())
                <a href="{{ $clearUrl }}" class="btn btn-secondary">Clear filters</a>
            @endif
        </div>

        <details class="border-t border-line" @if ($activeFilters->except('search')->isNotEmpty()) open @endif>
            <summary class="cursor-pointer px-4 py-2.5 text-sm font-medium text-muted hover:text-ink">More filters</summary>
            <div class="grid gap-x-6 gap-y-4 px-4 pb-4 pt-2 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($ranges as $key => [$label, $type, $step])
                    <fieldset>
                        <legend class="label">{{ $label }}</legend>
                        <div class="flex gap-2">
                            @foreach (['min', 'max'] as $bound)
                                <input type="{{ $type }}" name="search_{{ $key }}_{{ $bound }}"
                                    value="{{ request("search_{$key}_{$bound}") }}"
                                    @if ($step) step="{{ $step }}" min="{{ $step === '1' ? 1 : 0 }}" @endif
                                    aria-label="{{ $label }} {{ $bound }}" placeholder="{{ ucfirst($bound) }}"
                                    class="field min-w-0 w-full">
                            @endforeach
                        </div>
                    </fieldset>
                @endforeach

                @foreach ($choices as $name => [$label, $cases])
                    <x-chips :name="$name" :label="$label" :cases="$cases" :selected="(array) request($name, [])"
                        class="sm:col-span-2 lg:col-span-3" />
                @endforeach
            </div>
        </details>
    </form>

    <div class="card overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="border-b border-line">
                <tr>
                    <th class="th">Chemical</th>
                    <th class="th">Batches</th>
                    <th class="th">Total stock</th>
                    <th class="th">Hazards</th>
                    <th class="th"><span class="sr-only">Actions</span></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-line">
                @forelse ($data as $chemical)
                    @php
                        $total = $chemical->containers->sum('current_quantity');
                        $batchCount = $chemical->containers->count();
                        $chemicalStatuses = $statuses[$chemical->chemical_id] ?? [];
                    @endphp
                    <tr>
                        <td class="td">
                            <p class="font-semibold">{{ $chemical->chemical_name }}</p>
                            <p class="text-xs text-muted">Chemical #{{ $chemical->chemical_id }} · {{ $formatQuantity($chemical->quantity_per_unit) }} {{ $chemical->unit }} per container</p>
                        </td>
                        <td class="td">
                            <p>{{ $batchCount }} {{ \Illuminate\Support\Str::plural('batch', $batchCount) }}</p>
                            <p class="text-xs text-muted">{{ $chemical->containers->pluck('brand_name')->filter()->unique()->take(3)->join(', ') ?: 'No containers recorded' }}</p>
                        </td>
                        <td class="td">
                            <div>
                                <span class="font-semibold tabular-nums">{{ $total }}</span> <span class="text-muted">containers</span>
                                <p class="text-xs text-muted">{{ $formatQuantity($chemical->quantity_per_unit) }} {{ $chemical->unit }} each</p>
                            </div>
                            @if ($chemicalStatuses)
                                <p class="mt-1 flex flex-wrap gap-1">
                                    @foreach ($chemicalStatuses as $status)
                                        <span class="badge {{ in_array($status, [\App\Enums\AlertType::EXPIRED, \App\Enums\AlertType::OUT_OF_STOCK]) ? 'badge-danger' : 'badge-warning' }}">{{ ucfirst($status->value) }}</span>
                                    @endforeach
                                </p>
                            @endif
                        </td>
                        <td class="td">
                            <div class="flex max-w-56 flex-wrap gap-1">
                                @foreach ($list($chemical->safety_classes) as $class)<span class="badge badge-warning">{{ $class }}</span>@endforeach
                                @foreach ($list($chemical->ghs_symbols) as $symbol)<span class="badge badge-neutral">{{ $symbol }}</span>@endforeach
                            </div>
                        </td>
                        <td class="td text-right"><a href="{{ route('inventory.show', $chemical->chemical_id) }}" class="btn btn-secondary btn-sm">View</a></td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="td py-10 text-center text-muted">
                            No chemicals match{{ $activeFilters->isNotEmpty() ? ' these filters' : ' yet' }}.
                            @if ($activeFilters->isNotEmpty()) <a href="{{ $clearUrl }}" class="link">Clear filters</a>
                            @elseif ($isAdmin) <a class="link" href="{{ route('inventory.create') }}">Add the first one.</a> @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $data->links('pagination::tailwind') }}</div>
</x-app-layout>
