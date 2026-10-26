@php
    $n = fn($v) => rtrim(rtrim(number_format((float) $v, 3), '0'), '.');
    $list = fn($v) => collect(explode(',', (string) $v))->map(fn($s) => trim($s))->filter();
    $initial = (int) $chemical->quantity_received;
    $current = (int) $chemical->current_quantity;
    $pct = $initial > 0 ? max(0, min(100, $current / $initial * 100)) : 0;

    // Every container is equivalent, so there's no point drawing hundreds of them.
    $maxShown = 100;
    $shown = min($current, $maxShown);
    $remainingAfter = $current - 1;
@endphp

<x-app-layout title="Use {{ $chemical->chemical_name }}">
    {{-- One shared container icon; every button below references it. --}}
    <svg class="absolute h-0 w-0 overflow-hidden"
        aria-hidden="true" focusable="false">
        <symbol id="container-icon" viewBox="0 0 24 32">
            <g fill="none"
            stroke="currentColor"
            stroke-width="1.5"
            stroke-linecap="round"
            stroke-linejoin="round">
                <path d="M9 2h6M10 2v10L4 25a3.5 3.5 0 0 0 3.2 5h9.6a3.5 3.5 0 0 0 3.2-5l-6-13V2"/>
                <path d="M7 21h10M5.5 25h13"/>
            </g>
        </symbol>
    </svg>

    <div class="card mb-6 max-w-2xl p-5">
        <dl class="grid grid-cols-2 gap-x-6 gap-y-3 text-sm sm:grid-cols-3">
            <div>
                <dt class="text-muted">Batch / Lot Number</dt>
                <dd class="font-medium">{{ $chemical->batch_number }}</dd>
            </div>
            <div>
                <dt class="text-muted">Brand</dt>
                <dd class="font-medium">{{ $chemical->brand_name }}</dd>
            </div>
            <div>
                <dt class="text-muted">Location Name</dt>
                <dd class="font-medium tabular-nums">{{ $chemical->location?->location_name }}</dd>
            </div>
            <div>
                <dt class="text-muted">Expiration Date</dt>
                <dd class="font-medium">{{ $chemical->expiration_date?->format('M j, Y') }}</dd>
            </div>
            <div>
                <dt class="text-muted">Date Received</dt>
                <dd class="font-medium">{{ $chemical->date_received?->format('M j, Y') }}</dd>
            </div>
            <div>
                <dt class="text-muted">{{ ucfirst($chemical->quantityType()) }} per container</dt>
                <dd class="font-medium tabular-nums">{{ $n($chemical->quantity_per_unit) }} {{ $chemical->unit }}{{ $n($chemical->quantity_per_unit) > 1 ? 's' : '' }}</dd>
            </div>
        </dl>

        <div class="mt-5">
            <div class="h-1.5 overflow-hidden rounded-full bg-line" role="img"
                aria-label="{{ round($pct) }}% remaining">
                <div class="h-full rounded-full bg-reagent-600" style="width: {{ $pct }}%"></div>
            </div>
            <p class="mt-1 text-sm tabular-nums">
                {{ $current }}
                <span class="text-muted">
                    of {{ $initial }} {{ \Illuminate\Support\Str::plural('container', $initial) }} remaining
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

    <section class="card max-w-2xl p-6" aria-labelledby="containers-heading">
        <h2 id="containers-heading" class="text-base font-semibold">Pick a container to use</h2>
        <p class="mt-1 text-sm text-muted">
            Each icon is one container. Selecting one logs a single use and takes it out of stock.
        </p>

        <ul class="mt-4 grid grid-cols-[repeat(auto-fill,minmax(4.5rem,1fr))] gap-2">
            @for ($i = 1; $i <= $shown; $i++)
                <li>
                    <button type="button" data-container aria-label="Use container {{ $i }} of {{ $current }}"
                        class="flex w-full flex-col items-center gap-1 rounded-lg border border-line bg-white px-2 py-3 text-reagent-700 transition-colors hover:border-reagent-600 hover:bg-reagent-50 hover:text-reagent-600 data-selected:border-reagent-600 data-selected:bg-reagent-50 data-selected:text-reagent-600">
                        <svg class="h-10 w-[30px]" aria-hidden="true"><use href="#container-icon" /></svg>
                        <span class="text-xs tabular-nums text-muted">#{{ $i }}</span>
                    </button>
                </li>
            @endfor
        </ul>

        @if ($current > $shown)
            <p class="mt-3 text-sm text-muted">
                Showing {{ $shown }} of {{ $current }} containers. Any one of them logs a single use.
            </p>
        @endif
    </section>

    <a href="{{ url()->previous() === url()->current() ? route('locations') : url()->previous(route('locations')) }}" class="link mt-4 inline-block text-sm">Back to inventory</a>

    <dialog id="use-dialog" @if ($errors->has('notes')) data-open @endif
        class="m-auto w-[calc(100%-2rem)] max-w-md rounded-lg border border-line bg-white p-0 text-ink backdrop:bg-ink/40">
        <form action="{{ route('inventory.use.update', $chemical->chemical_id) }}" method="POST"
            class="space-y-4 p-6">
            @csrf
            @method('PUT')

            <div>
                <h2 class="text-lg font-semibold">Log one use?</h2>
                <p class="mt-1 text-sm text-muted">
                    This uses one container of {{ $chemical->chemical_name }}.
                    {{ $remainingAfter }} {{ \Illuminate\Support\Str::plural('container', $remainingAfter) }} will remain.
                </p>
            </div>

            <x-textarea name="notes" label="Notes (optional)" placeholder="What was it used for?" />

            <div class="flex gap-2 pt-2">
                <button type="submit" class="btn btn-primary">Log usage</button>
                <button type="button" class="btn btn-secondary" data-close>Cancel</button>
            </div>
        </form>
    </dialog>

    <script>
        (() => {
            const dialog = document.getElementById('use-dialog');
            const form = dialog.querySelector('form');
            const submit = form.querySelector('[type="submit"]');
            const buttons = document.querySelectorAll('[data-container]');
            const clearSelection = () => buttons.forEach((b) => b.removeAttribute('data-selected'));

            buttons.forEach((button) => {
                button.addEventListener('click', () => {
                    clearSelection();
                    button.setAttribute('data-selected', '');
                    dialog.showModal();
                });
            });

            dialog.addEventListener('close', clearSelection);
            dialog.querySelector('[data-close]').addEventListener('click', () => dialog.close());

            // Clicking the backdrop closes the dialog, but only if the press started there too,
            // so dragging a text selection out of the notes box doesn't throw the note away.
            let pressedOnBackdrop = false;
            dialog.addEventListener('mousedown', (e) => { pressedOnBackdrop = e.target === dialog; });
            dialog.addEventListener('click', (e) => {
                if (pressedOnBackdrop && e.target === dialog) dialog.close();
            });

            // A double-click must not use up two containers.
            form.addEventListener('submit', () => { submit.disabled = true; });
            window.addEventListener('pageshow', () => { submit.disabled = false; });

            // Re-open with the note intact if the server sent the form back with an error.
            if (dialog.hasAttribute('data-open')) dialog.showModal();
        })();
    </script>
</x-app-layout>
