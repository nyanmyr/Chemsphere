@php
    $formatQuantity = fn ($value) => rtrim(rtrim(number_format((float) $value, 3), '0'), '.');
@endphp

<x-app-layout :title="$chemical->chemical_name">
    <svg class="absolute h-0 w-0 overflow-hidden" aria-hidden="true" focusable="false">
        <symbol id="chemical-container-icon" viewBox="0 0 24 32">
            <g fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 2h6M10 2v10L4 25a3.5 3.5 0 0 0 3.2 5h9.6a3.5 3.5 0 0 0 3.2-5l-6-13V2" />
                <path d="M7 21h10M5.5 25h13" />
            </g>
        </symbol>
    </svg>

    <x-slot:actions>
        @if ($isAdmin)
            <a href="{{ route('inventory.chemical.edit', $chemical->chemical_id) }}" class="btn btn-secondary">Edit Chemical</a>
            <button type="button" class="btn btn-primary" onclick="document.getElementById('add-batch-dialog').showModal()">Add batch</button>
        @endif
        <a href="{{ route('inventory') }}" class="btn btn-secondary">Back to inventory</a>
    </x-slot:actions>

    <section class="card mb-5 p-5 sm:p-6">
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="text-xs uppercase tracking-wide text-muted">Chemical #{{ $chemical->chemical_id }}</p>
                <h2 class="mt-1 text-xl font-semibold">{{ $chemical->chemical_name }}</h2>
                <p class="mt-1 text-sm text-muted">
                    {{ $formatQuantity($chemical->quantity_per_unit) }} {{ $chemical->unit }} per container
                    · {{ $chemical->containers->sum('current_quantity') }} containers currently in stock
                </p>
            </div>
            <div class="flex flex-wrap gap-1">
                @foreach (explode(',', (string) $chemical->safety_classes) as $class)
                    @if (trim($class) !== '') <span class="badge badge-warning">{{ trim($class) }}</span> @endif
                @endforeach
                @foreach (explode(',', (string) $chemical->ghs_symbols) as $symbol)
                    @if (trim($symbol) !== '') <span class="badge badge-neutral">{{ trim($symbol) }}</span> @endif
                @endforeach
            </div>
        </div>
    </section>

    @if ($isAdmin)
        <dialog id="add-batch-dialog" @if ($errors->any()) open @endif class="m-auto w-[calc(100%-2rem)] max-w-3xl rounded-xl border border-line bg-white p-0 text-ink shadow-xl backdrop:bg-ink/40">
            <div class="flex items-start justify-between gap-4 border-b border-line p-5 sm:p-6">
                <div>
                    <h2 class="text-lg font-semibold">Add batch</h2>
                    <p class="mt-1 text-sm text-muted">Record a received lot with its brand, location, dates, and container count.</p>
                </div>
                <button type="button" class="btn btn-secondary btn-sm" aria-label="Close add batch dialog" onclick="document.getElementById('add-batch-dialog').close()">Close</button>
            </div>
            <form method="POST" action="{{ route('inventory.containers.store', $chemical->chemical_id) }}" class="grid gap-4 p-5 sm:grid-cols-2 sm:p-6 lg:grid-cols-3">
                @csrf
                <div>
                    <label class="label" for="location_id">Storage location</label>
                    <select class="field w-full" id="location_id" name="location_id" required>
                        <option value="">Choose location</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->location_id }}" @selected(old('location_id') == $location->location_id)>{{ $location->location_name }} (#{{ $location->location_id }})</option>
                        @endforeach
                    </select>
                </div>
                <div><label class="label" for="batch_number">Batch / lot number</label><input class="field w-full" id="batch_number" name="batch_number" required value="{{ old('batch_number') }}"></div>
                <div><label class="label" for="brand_name">Brand</label><input class="field w-full" id="brand_name" name="brand_name" required value="{{ old('brand_name') }}"></div>
                <div><label class="label" for="quantity_received">Containers received</label><input class="field w-full" id="quantity_received" name="quantity_received" type="number" min="0" step="1" required value="{{ old('quantity_received', 1) }}"></div>
                <div><label class="label" for="date_received">Date received</label><input class="field w-full" id="date_received" name="date_received" type="date" required value="{{ old('date_received', today()->toDateString()) }}"></div>
                <div><label class="label" for="expiration_date">Expiration date</label><input class="field w-full" id="expiration_date" name="expiration_date" type="date" required value="{{ old('expiration_date') }}"></div>
                @if ($errors->any())
                    <div class="rounded-md bg-red-50 p-3 text-sm text-red-700 sm:col-span-2 lg:col-span-3"><ul class="list-inside list-disc">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                <div class="flex items-center justify-end gap-2 sm:col-span-2 lg:col-span-3">
                    <button type="button" class="btn btn-secondary" onclick="document.getElementById('add-batch-dialog').close()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save batch</button>
                </div>
            </form>
        </dialog>
    @endif

    <div class="mb-3 flex items-end justify-between gap-3">
        <h3 class="text-lg font-semibold">Batches</h3>
        <span class="text-sm text-muted">{{ $chemical->containers->count() }} total batches</span>
    </div>

    <div class="space-y-4">
        @forelse ($chemical->containers->sortBy('expiration_date') as $container)
            @php
                $status = $containerStatuses[$container->chemical_container_id] ?? null;
                $pct = $container->quantity_received > 0 ? max(0, min(100, $container->current_quantity / $container->quantity_received * 100)) : 0;
                $shownIcons = min((int) $container->current_quantity, 100);
            @endphp
            <article class="card p-5 sm:p-6">
                <div class="flex flex-wrap items-start justify-between gap-4">
                    <div class="min-w-0 flex-1">
                        <h4 class="font-semibold">Batch / lot {{ $container->batch_number }}</h4>
                        <p class="text-sm text-muted">{{ $container->brand_name }} · {{ $container->location?->location_name }} · Container record #{{ $container->chemical_container_id }}</p>
                        <div class="mt-2 flex flex-wrap gap-x-4 gap-y-1 text-sm">
                            <span>Received: {{ $container->date_received?->format('M j, Y') }}</span>
                            <span>Expires: {{ $container->expiration_date?->format('M j, Y') }}</span>
                            @if ($status)<span class="badge {{ $status === \App\Enums\AlertType::EXPIRED ? 'badge-danger' : 'badge-warning' }}">{{ ucfirst($status->value) }}</span>@endif
                        </div>
                    </div>
                    <div class="min-w-36">
                        <p class="text-right text-lg font-semibold tabular-nums">{{ $container->current_quantity }} <span class="text-sm font-normal text-muted">/ {{ $container->quantity_received }}</span></p>
                        <div class="mt-1 h-1.5 overflow-hidden rounded-full bg-line"><div class="h-full {{ $container->current_quantity <= 0 ? 'bg-red-600' : ($pct < 20 ? 'bg-amber-500' : 'bg-reagent-600') }}" style="width: {{ $pct }}%"></div></div>
                        <p class="mt-1 text-right text-xs text-muted">containers remaining</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('inventory.containers.use', $container->chemical_container_id) }}"
                    class="mt-4 border-t border-line pt-4" data-container-selection-form data-batch-name="{{ $container->batch_number }}">
                    @csrf
                    @if ($container->current_quantity > 0)
                        <fieldset>
                            <legend class="label mb-2">Select containers to use <span class="font-normal text-muted">(select one or more)</span></legend>
                            <div class="flex flex-wrap items-center gap-1.5" aria-label="Select containers from batch {{ $container->batch_number }}">
                                @for ($i = 1; $i <= $shownIcons; $i++)
                                    <label class="cursor-pointer rounded-md p-1 transition hover:bg-reagent-50">
                                        <input class="peer sr-only" type="checkbox" name="selected_containers[]" value="{{ $i }}" aria-label="Select container {{ $i }} from batch {{ $container->batch_number }}">
                                        <svg class="h-8 w-6 rounded-md text-reagent-700 peer-checked:bg-reagent-100 peer-checked:outline peer-checked:outline-2 peer-checked:outline-reagent-600 peer-focus-visible:outline peer-focus-visible:outline-2 peer-focus-visible:outline-offset-2 peer-focus-visible:outline-reagent-700" aria-hidden="true"><use href="#chemical-container-icon" /></svg>
                                    </label>
                                @endfor
                                @if ($container->current_quantity > $shownIcons)
                                    <span class="ml-1 text-xs text-muted">+{{ $container->current_quantity - $shownIcons }} more not shown; select up to 100 per use</span>
                                @endif
                            </div>
                            <p class="mt-2 text-sm text-muted">{{ $formatQuantity($chemical->quantity_per_unit) }} {{ $chemical->unit }} per container</p>
                        </fieldset>
                        <div class="mt-4 flex flex-wrap items-end gap-3">
                            <div class="min-w-48 flex-1"><label class="label" for="notes-{{ $container->chemical_container_id }}">Usage notes (optional)</label><input class="field w-full" id="notes-{{ $container->chemical_container_id }}" name="notes" placeholder="Purpose or experiment"></div>
                            <button type="submit" class="btn btn-primary" data-use-selected-button disabled>Use selected (0)</button>
                        </div>
                    @else
                        <p class="text-sm text-muted">This batch is out of stock.</p>
                    @endif
                </form>

                @if ($isAdmin)
                    <div class="mt-3 flex flex-wrap justify-end gap-2">
                        <a href="{{ route('inventory.containers.edit', $container->chemical_container_id) }}" class="btn btn-secondary">Edit batch</a>
                        <form method="POST" action="{{ route('inventory.containers.delete', $container->chemical_container_id) }}" onsubmit="return confirm(@js('Delete batch ' . $container->batch_number . '? This cannot be undone.'))">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete batch</button>
                        </form>
                    </div>
                @endif
            </article>
        @empty
            <div class="card p-5 py-10 text-center text-muted">No batches have been added yet.@if ($isAdmin) Use the Add batch button to record the first delivery.@endif</div>
        @endforelse
    </div>

    <script>
        document.querySelectorAll('[data-container-selection-form]').forEach((form) => {
            const boxes = Array.from(form.querySelectorAll('input[name="selected_containers[]"]'));
            const button = form.querySelector('[data-use-selected-button]');
            if (!button) return;
            const updateButton = () => {
                const count = boxes.filter((box) => box.checked).length;
                button.disabled = count === 0;
                button.textContent = `Use selected (${count})`;
            };
            boxes.forEach((box) => box.addEventListener('change', updateButton));
            form.addEventListener('submit', (event) => {
                const count = boxes.filter((box) => box.checked).length;
                if (count < 1) {
                    event.preventDefault();
                    return;
                }
                if (!confirm(`Log use of ${count} container${count === 1 ? '' : 's'} from batch ${form.dataset.batchName}?`)) {
                    event.preventDefault();
                }
            });
            updateButton();
        });
    </script>
</x-app-layout>
