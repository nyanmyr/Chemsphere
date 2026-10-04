@php
    $main = [['inventory', 'Inventory'], ['locations', 'Locations'], ['equipment', 'Equipment'], ['alerts', 'Alerts']];
    $admin = [['users', 'Users'], ['usage_logs', 'Usage logs'], ['audit_logs', 'Audit logs']];
    $isAdmin = Auth::user()?->user_role?->isRole(\App\Enums\UserRole::ADMIN);
@endphp

<a href="{{ route('welcome') }}" class="hidden items-center gap-2 px-5 py-5 font-semibold lg:flex">
    <svg class="size-6 text-reagent-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 3h6M10 3v6l-5 9a2 2 0 0 0 1.8 3h10.4A2 2 0 0 0 19 18l-5-9V3M7.5 15h9" /></svg>
    Chemsphere
</a>

<nav class="flex-1 space-y-6 px-3 py-3" aria-label="Main">
    @foreach (['' => $main, 'Admin' => $isAdmin ? $admin : []] as $group => $links)
    @if ($links)
    <div class="space-y-0.5">
        @if ($group)<p class="px-2 pb-1 text-xs font-medium text-muted">{{ $group }}</p>@endif
        @foreach ($links as [$route, $label])
        <a href="{{ route($route) }}" @if (request()->routeIs($route . '*')) aria-current="page" @endif
            class="block rounded-md px-2.5 py-1.5 text-sm {{ request()->routeIs($route . '*') ? 'bg-reagent-50 font-medium text-reagent-700' : 'text-muted hover:bg-paper hover:text-ink' }}">
            {{ $label }}
        </a>
        @endforeach
    </div>
    @endif
    @endforeach
</nav>

<div class="border-t border-line p-4">
    <p class="truncate text-sm" title="{{ Auth::user()?->email }}">{{ Auth::user()?->email }}</p>
    <form action="{{ route('logout') }}" method="POST" class="mt-2">
        @csrf
        <button type="submit" class="text-sm font-medium text-muted hover:text-ink">Sign out</button>
    </form>
</div>
