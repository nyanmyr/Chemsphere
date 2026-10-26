@auth
    @php
        $isAdmin = Auth::user()->user_role->isRole(\App\Enums\UserRole::ADMIN);
        $tiles = [
            ['inventory', 'Inventory', 'Search stock, log usage, and check expiry dates.'],
            ['locations', 'Locations', 'See where chemicals and equipment are kept.'],
            ['equipment', 'Equipment', 'Check availability and log equipment use.'],
            ['alerts', 'Alerts', 'Review expiring, expired, and low-stock chemicals.'],
        ];
        if ($isAdmin) {
            $tiles = array_merge($tiles, [
                ['users', 'Users', 'Approve accounts and change roles.'],
                ['usage_logs', 'Usage logs', 'See who used what, and when.'],
                ['audit_logs', 'Audit logs', 'Review every change made in the system.'],
            ]);
        }
    @endphp

    <x-app-layout title="Home">
        {{-- Only set for approved users; see DashboardController. --}}
        @isset($usedToday)
            @php
                $n = fn($v) => rtrim(rtrim(number_format((float) $v, 3), '0'), '.');
                $tone = fn($type) => in_array($type, [\App\Enums\AlertType::EXPIRED, \App\Enums\AlertType::OUT_OF_STOCK], true)
                    ? 'badge-danger'
                    : 'badge-warning';
            @endphp

            {{-- Keep this element rendered even when empty: app.js swaps it by id on live updates. --}}
            <section id="used-today" class="mt-8" aria-labelledby="used-today-title">
                <div class="mb-3 flex flex-wrap items-baseline justify-between gap-x-3">
                    <h2 id="used-today-title" class="text-lg font-semibold tracking-tight">
                        Chemicals Used Today
                    </h2>
                    <p class="text-sm text-muted">{{ $today->format('l, M j') }}</p>
                </div>

                <div class="card max-h-96 overflow-auto">
                    <table class="w-full text-sm">
                        <thead class="border-b border-line">
                            <tr>
                                <th class="th">Chemical</th>
                                <th class="th">Remaining</th>
                                <th class="th">Time Used</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-line">
                            @forelse ($usedToday as $row)
                                @php $c = $row['chemical']; @endphp
                                <tr>
                                    <td class="td">
                                        @if ($c)
                                            <p class="font-medium">{{ $c->chemical_name }}</p>
                                            <p class="text-xs text-muted">
                                                #{{ $c->chemical_id }}, batch / lot number {{ $c->batch_number }}
                                            </p>
                                        @else
                                            <p class="font-medium text-muted">Deleted chemical</p>
                                            <p class="text-xs text-muted">#{{ $row['id'] }}</p>
                                        @endif
                                    </td>
                                    <td class="td whitespace-nowrap tabular-nums">
                                        @if ($c)
                                            {{ $n($c->current_quantity) }}
                                            <span class="text-muted">/ {{ $n($c->quantity_received) }} containers</span>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td class="td whitespace-nowrap tabular-nums">
                                        {{ $row['last_used_at']->format('g:i A') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="td py-10 text-center text-muted">
                                        No chemicals have been used today.
                                        <a href="{{ route('inventory') }}" class="link">Open inventory</a>
                                        to log usage.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="mt-8" aria-labelledby="attention-title">
                <h2 id="attention-title" class="mb-3 text-lg font-semibold tracking-tight">
                    Chemicals Needing Attention
                </h2>

                <x-filter-bar :action="route('welcome')" placeholder="Search name, batch / lot number, or brand"
                    :choices="['search_alert_type' => ['Status', $alertTypes]]" :sortable="false" />

                @php $filtered = filled(request('search')) || filled(request('search_alert_type')); @endphp

                <div id="attention-table">
                    <p class="mb-2 text-sm text-muted">
                        {{ $attention->total() }} {{ $attention->total() === 1 ? 'chemical' : 'chemicals' }}
                    </p>

                    <div class="card overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="border-b border-line">
                                <tr>
                                    <th class="th">Chemical</th>
                                    <th class="th">Status</th>
                                    <th class="th">Stock</th>
                                    <th class="th">Expiration Date</th>
                                    <th class="th">Location</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-line">
                                @forelse ($attention as $row)
                                    @php
                                        $c = $row['chemical'];
                                        $has = fn($type) => in_array($type, $row['statuses'], true);
                                        $initial = (float) $c->quantity_received;
                                        $current = (float) $c->current_quantity;
                                        $pct = $initial > 0 ? max(0, min(100, $current / $initial * 100)) : 0;
                                        $bar = $has(\App\Enums\AlertType::OUT_OF_STOCK)
                                            ? 'bg-red-600'
                                            : ($has(\App\Enums\AlertType::LOW_STOCK) ? 'bg-amber-500' : 'bg-reagent-600');
                                    @endphp
                                    <tr>
                                        <td class="td">
                                            <p class="font-medium">{{ $c->chemical_name }}</p>
                                            <p class="text-xs text-muted">
                                                #{{ $c->chemical_id }}, batch {{ $c->batch_number }},
                                                {{ $c->brand_name }}
                                            </p>
                                        </td>
                                        <td class="td">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach ($row['statuses'] as $type)
                                                    <span class="badge {{ $tone($type) }}">
                                                        {{ ucfirst($type->value) }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="td">
                                            <div class="h-1.5 w-24 overflow-hidden rounded-full bg-line" role="img"
                                                aria-label="{{ round($pct) }}% remaining">
                                                <div class="h-full rounded-full {{ $bar }}" style="width: {{ $pct }}%"></div>
                                            </div>
                                            <p class="mt-1 whitespace-nowrap tabular-nums">
                                                {{ $n($current) }}
                                                <span class="text-muted">of {{ $n($initial) }}</span>
                                            </p>
                                        </td>
                                        <td class="td whitespace-nowrap">
                                            <span class="tabular-nums">{{ $c->expiration_date->format('M j, Y') }}</span>
                                            <p class="text-xs text-muted">{{ $c->expiration_date->diffForHumans() }}</p>
                                        </td>
                                        <td class="td">
                                            <p class="font-medium">{{ $c->location?->location_name }}</p>
                                            <p class="text-xs text-muted">#{{ $c->location?->location_id }}</p>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="td py-10 text-center text-muted">
                                            @if ($filtered)
                                                No chemicals match these filters.
                                                <a href="{{ route('welcome') }}" class="link">Clear filters</a>
                                            @else
                                                Nothing needs attention right now. Expired, low-stock, and
                                                out-of-stock chemicals will show up here.
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">{{ $attention->links('pagination::tailwind') }}</div>
                </div>
            </section>
        @endisset

        <h2 id="attention-title" class="mb-3 text-lg font-semibold tracking-tight">
            Tabs
        </h2>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($tiles as [$route, $label, $text])
                <a href="{{ route($route) }}" class="card p-5 hover:border-reagent-600">
                    <h2 class="flex items-center gap-2 font-semibold">
                        {{ $label }}
                        @if ($route === 'alerts')
                            <x-alert-dot :count="Auth::user()->unreadAlertsCount()" />
                        @endif
                    </h2>
                    <p class="mt-1 text-sm text-muted">{{ $text }}</p>
                </a>
            @endforeach
        </div>
    </x-app-layout>
@else
    <x-guest-layout title="Welcome" heading="Know what's in your lab.">
        <p class="text-sm text-muted">
            Track chemicals and equipment, log usage, and get alerted before anything expires or runs out.
        </p>
        <div class="mt-6 space-y-2">
            <a href="{{ route('login') }}" class="btn btn-primary w-full">
                Sign in
            </a>
            <a href="{{ route('register') }}" class="btn btn-secondary w-full">
                Create account
            </a>
        </div>
    </x-guest-layout>
@endauth
