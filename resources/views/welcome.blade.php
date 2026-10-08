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

        @isset($usedToday)
            @php
                $n = fn($v) => rtrim(rtrim(number_format((float) $v, 3), '0'), '.');
            @endphp

            <section id="used-today" class="mt-8" aria-labelledby="used-today-title">
                <div class="mb-3 flex flex-wrap items-baseline justify-between gap-x-3">
                    <h2 id="used-today-title" class="text-lg font-semibold tracking-tight">
                        Chemicals used today
                    </h2>
                    <p class="text-sm text-muted">{{ $today->format('l, M j') }}</p>
                </div>

                <div class="card max-h-96 overflow-auto">
                    <table class="w-full text-sm">
                        <thead class="border-b border-line">
                            <tr>
                                <th class="th">Chemical</th>
                                <th class="th">Used</th>
                                <th class="th">Remaining</th>
                                <th class="th">Last used</th>
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
                                                #{{ $c->chemical_id }}, batch {{ $c->batch_number }}
                                            </p>
                                        @else
                                            <p class="font-medium text-muted">Deleted chemical</p>
                                            <p class="text-xs text-muted">#{{ $row['id'] }}</p>
                                        @endif
                                    </td>
                                    <td class="td whitespace-nowrap tabular-nums">
                                        {{ $n($row['total_used']) }}
                                        <span class="text-muted">{{ $c?->unit }}</span>
                                        <p class="text-xs text-muted">
                                            {{ $row['times_used'] }} {{ $row['times_used'] === 1 ? 'use' : 'uses' }}
                                        </p>
                                    </td>
                                    <td class="td whitespace-nowrap tabular-nums">
                                        @if ($c)
                                            {{ $n($c->current_quantity) }}
                                            <span class="text-muted">{{ $c->unit }}</span>
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
        @endisset
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
