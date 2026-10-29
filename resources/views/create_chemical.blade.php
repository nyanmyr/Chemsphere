<x-app-layout title="Create Chemical Category">
    <form action="{{ route('inventory.store') }}" method="POST" class="card max-w-3xl space-y-4 p-6">
        @csrf
        @include('partials.chemical-fields')
        <x-form-actions
            submit="Create Category"
            :cancel="url()->previous() === url()->current() ? route('inventory') : url()->previous(route('inventory'))"
        />
    </form>
</x-app-layout>
