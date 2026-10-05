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
                    <h2 class="font-semibold">{{ $label }}</h2>
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
