<x-app-layout title="Add location">
    <form action="{{ route('locations.store') }}" method="POST" class="card max-w-2xl space-y-4 p-6">
        @csrf
        @include('partials.location-fields')
        <x-form-actions
        submit="Log usage"
        :cancel="url()->previous() === url()->current() ? route('locations') : url()->previous(route('locations'))"
        />
    </form>
</x-app-layout>
