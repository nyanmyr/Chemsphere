@php
    $isAdmin = Auth::user()->user_role->isRole(\App\Enums\UserRole::ADMIN);
    $list = fn ($value) => collect(explode(',', (string) $value))->map(fn ($item) => trim($item))->filter();
    $n = fn ($value) => rtrim(rtrim(number_format((float) $value, 3), '0'), '.');
@endphp

<x-app-layout title="{{ $chemical->chemical_name }} Batches">
    <x-slot:actions>
        @if ($isAdmin)
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('inventory.edit', $chemical->chemical_id) }}" class="btn btn-secondary">Edit Category</a>
                <a href="{{ route('inventory.batch.create', $chemical->chemical_id) }}" class="btn btn-primary">Add Batch</a>
            </div>
        @endif
    </x-slot:actions>

    <a href="{{ route('inventory') }}" class="link text-sm">← Back to inventory</a>

    <section class="card mt-4 p-5">
        <p class="text-xs font-medium uppercase tracking-wide text-muted">#{{ $chemical->chemical_id }}</p>
        <h2 class="mt-1 text-xl font-semibold tracking-tight">{{ $chemical->chemical_name }}</h2>
        <div class="mt-3 flex flex-wrap gap-1">
            @foreach ($list($chemical->safety_classes) as $class)
                <span class="badge badge-warning">{{ $class }}</span>
            @endforeach
            @foreach ($list($chemical->ghs_symbols) as $ghs)
                <span class="badge badge-neutral">{{ $ghs }}</span>
            @endforeach
        </div>
        <p class="mt-3 text-sm text-muted">Each batch below has its own lot number, brand, storage location, stock, and expiration date.</p>
    </section>

    <section class="mt-6" id="chemical-batches-table">
        <div class="mb-3 flex flex-wrap items-baseline justify-between gap-3">
            <h2 class="text-lg font-semibold">Batches <span class="text-muted">({{ $chemical->batches->count() }})</span></h2>
        </div>

        <div class="card overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-line">
                    <tr>
                        <th class="th">Batch / Lot</th>
                        <th class="th">Brand</th>
                        <th class="th">Location</th>
                        <th class="th">Stock</th>
                        <th class="th">Expiration Date</th>
                        <th class="th"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($chemical->batches as $batch)
                        @php
                            $initial = (float) $batch->quantity_received;
                            $current = (float) $batch->current_quantity;
                            $pct = $initial > 0 ? max(0, min(100, $current / $initial * 100)) : 0;
                            $bar = $current <= 0 ? 'bg-red-600' : ($pct < 20 ? 'bg-amber-500' : 'bg-reagent-600');
                            $expiration = $statuses[$batch->chemical_batch_id] ?? null;
                        @endphp
                        <tr>
                            <td class="td">
                                <p class="font-semibold">{{ $batch->batch_number }}</p>
                                <p class="text-xs text-muted">#{{ $batch->chemical_batch_id }}</p>
                            </td>
                            <td class="td">{{ $batch->brand_name }}</td>
                            <td class="td">
                                <p class="font-medium">{{ $batch->location?->location_name }}</p>
                                <p class="text-xs text-muted">#{{ $batch->location?->location_id }}</p>
                            </td>
                            <td class="td">
                                <div class="h-1.5 w-24 overflow-hidden rounded-full bg-line" role="img" aria-label="{{ round($pct) }}% remaining">
                                    <div class="h-full rounded-full {{ $bar }}" style="width: {{ $pct }}%"></div>
                                </div>
                                <p class="mt-1 whitespace-nowrap tabular-nums">
                                    {{ $n($current) }} <span class="text-muted">of {{ $n($initial) }}</span>
                                </p>
                                <p class="whitespace-nowrap text-xs text-muted tabular-nums">
                                    {{ $n($batch->quantity_per_unit) }} {{ $batch->unit }} per container
                                </p>
                            </td>
                            <td class="td whitespace-nowrap">
                                {{ $batch->expiration_date?->format('M j, Y') }}
                                @if ($expiration === \App\Enums\AlertType::EXPIRED)
                                    <span class="badge badge-danger ml-1">Expired</span>
                                @elseif ($expiration === \App\Enums\AlertType::EXPIRING_SOON)
                                    <span class="badge badge-warning ml-1">Expiring soon</span>
                                @endif
                            </td>
                            <td class="td">
                                <div class="flex justify-end gap-1.5">
                                    <a href="{{ route('inventory.use.edit', $batch->chemical_batch_id) }}" class="btn btn-secondary btn-sm">View</a>
                                    @if ($isAdmin)
                                        <form action="{{ route('inventory.batch.delete', $batch->chemical_batch_id) }}" method="POST"
                                            onsubmit="return confirm(@js('Delete batch ' . $batch->batch_number . '? This can\'t be undone.'))">
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
                                No batches have been added to this category yet.
                                @if ($isAdmin)
                                    <a href="{{ route('inventory.batch.create', $chemical->chemical_id) }}" class="link">Add the first batch</a>.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</x-app-layout>
