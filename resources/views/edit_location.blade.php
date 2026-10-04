<x-app-layout title="Edit location">
    <x-meta :record="$location" />
    <form action="{{ route('locations.update', $location->location_id) }}" method="POST" class="card max-w-2xl space-y-4 p-6">
        @csrf
        @method('PUT')
        @include('partials.location-fields')
        <x-form-actions
        submit="Save changes"
        :cancel="url()->previous() === url()->current() ? route('locations') : url()->previous(route('locations'))"
        />
    </form>
</x-app-layout>
