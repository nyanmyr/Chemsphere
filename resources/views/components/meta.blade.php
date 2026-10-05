@props(['record'])

<dl class="mb-6 grid grid-cols-2 gap-x-6 gap-y-3 text-sm sm:grid-cols-4">
    <div>
        <dt class="text-muted">
            ID
        </dt>
        <dd class="font-medium tabular-nums">
            {{ $record->getKey() }}
        </dd>
    </div>
    <div>
        <dt class="text-muted">
            Created by
        </dt>
        <dd class="font-medium tabular-nums">
            {{ $record->user?->email }}, #{{ $record->user?->user_id }}
        </dd>
    </div>
    <div>
        <dt class="text-muted">
            Created
        </dt>
        <dd class="font-medium">
            {{ $record->created_at?->format('M j, Y g:i A') }}
        </dd>
    </div>
    <div>
        <dt class="text-muted">
            Last updated
        </dt>
        <dd class="font-medium">
            {{ $record->updated_at?->format('M j, Y g:i A') }}
        </dd>
    </div>
</dl>
