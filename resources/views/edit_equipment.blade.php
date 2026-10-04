<x-app-layout title="Edit equipment">
    <x-meta :record="$equipment" />
    <form action="{{ route('equipment.update', $equipment->equipment_id) }}" method="POST" class="card max-w-3xl space-y-4 p-6">
        @csrf
        @method('PUT')
        @include('partials.equipment-fields')
        <x-form-actions
        submit="Save changes"
        :cancel="url()->previous() === url()->current() ? route('equipment') : url()->previous(route('equipment'))"
        />
    </form>
</x-app-layout>
