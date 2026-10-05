@php
    $n = fn($v) => rtrim(rtrim(number_format((float) $v, 3), '0'), '.');
    $list = fn($v) => collect(explode(',', (string) $v))->map(fn($s) => trim($s))->filter();
    $initial = (float) $chemical->initial_quantity;
    $current = (float) $chemical->current_quantity;
    $pct = $initial > 0 ? max(0, min(100, $current / $initial * 100)) : 0;
@endphp

<x-app-layout title="Use {{ $chemical->chemical_name }}">
    <div class="card mb-6 max-w-2xl p-5">
        <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm sm:grid-cols-3">
            <div>
                <dt class="text-muted">Batch</dt>
                <dd class="font-medium">{{ $chemical->batch_number }}</dd>
            </div>
            <div>
                <dt class="text-muted">Brand</dt>
                <dd class="font-medium">{{ $chemical->brand_name }}</dd>
            </div>
            <div>
                <dt class="text-muted">Location ID</dt>
                <dd class="font-medium tabular-nums">{{ $chemical->location?->location_id }}</dd>
            </div>
            <div>
                <dt class="text-muted">Location Name</dt>
                <dd class="font-medium tabular-nums">{{ $chemical->location?->location_name }}</dd>
            </div>
            <div>
                <dt class="text-muted">Expires</dt>
                <dd class="font-medium">{{ $chemical->expiration_date?->format('M j, Y') }}</dd>
            </div>
            <div>
                <dt class="text-muted">Arrived</dt>
                <dd class="font-medium">{{ $chemical->arrival_date?->format('M j, Y') }}</dd>
            </div>
            <div>
                <dt class="text-muted">Volume per unit</dt>
                <dd class="font-medium tabular-nums">{{ $n($chemical->volume_per_unit) }}</dd>
            </div>
        </dl>

        <div class="mt-5">
            <div class="h-1.5 overflow-hidden rounded-full bg-line" role="img"
                aria-label="{{ round($pct) }}% remaining">
                <div class="h-full rounded-full bg-reagent-600" style="width: {{ $pct }}%"></div>
            </div>
            <p class="mt-1 text-sm tabular-nums">
                {{ $n($current) }}
                <span class="text-muted">
                    of {{ $n($initial) }} {{ $chemical->unit }} remaining
                </span>
            </p>
        </div>

        <div class="mt-4 flex flex-wrap gap-1">
            @foreach ($list($chemical->safety_classes) as $class)
            <span class="badge badge-warning">
                {{ $class }}
            </span>
            @endforeach
            @foreach ($list($chemical->ghs_symbols) as $ghs)
            <span class="badge badge-neutral">
                {{ $ghs }}
            </span>
            @endforeach
        </div>
    </div>

    <form action="{{ route('inventory.use.update', $chemical->chemical_id) }}" method="POST"
        class="card max-w-2xl space-y-4 p-6">
        @csrf
        @method('PUT')
        <x-input name="use_amount" :label="'Amount used (' . $chemical->unit . ')'" type="number" step="0.001" min="0"
            :max="$chemical->current_quantity" />
        <x-textarea name="notes" label="Notes (optional)" placeholder="What was it used for?" />
        <x-form-actions submit="Log usage" :cancel="url()->previous() === url()->current() ? route('inventory') : url()->previous(route('inventory'))" />
    </form>
</x-app-layout>
