<x-app-layout title="Create Equipment">
    <form action="{{ route('equipment.store') }}" method="POST" class="card max-w-3xl space-y-4 p-6">
        @csrf
        @include('partials.equipment-fields')
        <x-form-actions
        submit="Create Equipment"
        :cancel="url()->previous() === url()->current() ? route('equipment') : url()->previous(route('equipment'))"
        />
    </form>
</x-app-layout>
