<x-app-layout title="Add chemical">
    <form action="{{ route('inventory.store') }}" method="POST" class="card max-w-3xl space-y-4 p-6">
        @csrf
        @include('partials.chemical-fields')
        <x-form-actions submit="Create" :cancel="route('inventory')" />
    </form>
</x-app-layout>
