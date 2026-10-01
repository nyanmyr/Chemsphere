{{-- ranges: key => [label, input type, step]  (request names: search_{key}_min / _max)
     choices: request name => [label, cases] --}}
@props(['action', 'placeholder' => 'Search', 'ranges' => [], 'choices' => []])
@php
    $filters = collect(request()->except('page'))->filter(fn ($v) => $v !== null && $v !== '' && $v !== []);
@endphp

<form action="{{ $action }}" method="GET" class="card mb-6">
    <div class="flex flex-wrap gap-2 p-3">
        <input type="search" name="search" value="{{ request('search') }}" placeholder="{{ $placeholder }}" aria-label="Search" class="field min-w-56 flex-1">
        <button type="submit" class="btn btn-primary">Search</button>
        @if ($filters->isNotEmpty())
        <a href="{{ $action }}" class="btn btn-secondary">Clear filters</a>
        @endif
    </div>

    @if ($ranges || $choices)
    <details class="border-t border-line" @if ($filters->except('search')->isNotEmpty()) open @endif>
        <summary class="cursor-pointer px-4 py-2.5 text-sm font-medium text-muted hover:text-ink">More filters</summary>

        <div class="grid gap-x-6 gap-y-4 px-4 pb-4 pt-2 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ($ranges as $key => [$label, $type, $step])
            <fieldset>
                <legend class="label">{{ $label }}</legend>
                <div class="flex gap-2">
                    @foreach (['min', 'max'] as $bound)
                    <input type="{{ $type }}" name="search_{{ $key }}_{{ $bound }}" value="{{ request("search_{$key}_{$bound}") }}"
                        @if ($step) step="{{ $step }}" min="{{ $step === '1' ? 1 : 0 }}" @endif
                        aria-label="{{ $label }} {{ $bound }}" placeholder="{{ ucfirst($bound) }}" class="field">
                    @endforeach
                </div>
            </fieldset>
            @endforeach

            @foreach ($choices as $name => [$label, $cases])
            <x-chips :name="$name" :label="$label" :cases="$cases" :selected="(array) request($name, [])" class="sm:col-span-2 lg:col-span-3" />
            @endforeach
        </div>
    </details>
    @endif
</form>
