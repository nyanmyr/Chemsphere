@props(['count' => 0])
<span data-unread-dot
    class="{{ $count < 1 ? 'invisible' : '' }} inline-block size-2 shrink-0 rounded-full bg-red-600 ring-2 ring-white">
    <span class="sr-only">Unread alerts</span>
</span>
