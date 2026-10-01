<x-app-layout title="Edit chemical">
    <x-meta :record="$chemical" />
    <form action="{{ route('inventory.update', $chemical->chemical_id) }}" method="POST" class="card max-w-3xl space-y-4 p-6">
        @csrf
        @method('PUT')
        @include('partials.chemical-fields')
        <x-form-actions submit="Save changes" :cancel="route('inventory')" />
    </form>
</x-app-layout>
