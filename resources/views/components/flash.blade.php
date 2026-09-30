@php
    $flashes = collect([
        ['status', session('success'), 'border-reagent-600/30 bg-reagent-50 text-reagent-700'],
        ['status', session('info'), 'border-line bg-white text-muted'],
        ['alert', session('error'), 'border-red-300 bg-red-50 text-red-800'],
    ])->filter(fn ($f) => $f[1]);
@endphp

@foreach ($flashes as [$role, $message, $classes])
<div role="{{ $role }}" class="mb-4 rounded-md border px-4 py-3 text-sm {{ $classes }}">{{ $message }}</div>
@endforeach

@if ($errors->any())
<div role="alert" class="mb-4 rounded-md border border-red-300 bg-red-50 px-4 py-3 text-sm text-red-800">
    <ul class="list-inside list-disc space-y-0.5">
        @foreach ($errors->all() as $message)
        <li>{{ $message }}</li>
        @endforeach
    </ul>
</div>
@endif
