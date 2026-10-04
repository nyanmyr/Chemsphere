@php
    $isAdmin = Auth::user()->user_role->isRole(\App\Enums\UserRole::ADMIN);
@endphp

<x-app-layout title="Locations">
    <x-slot:actions>
        @if ($isAdmin)<a href="{{ route('locations.create') }}" class="btn btn-primary">Add location</a>@endif
    </x-slot:actions>

    <x-filter-bar :action="route('locations')" placeholder="Search name or description"
        :ranges="['location_id' => ['Location ID', 'number', '1'], 'created_by' => ['Created by (user ID)', 'number', '1']]" />

    <div id="locations-table">
        <div class="card overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-line">
                    <tr>
                        <th class="th">ID</th>
                        <th class="th">Name</th>
                        <th class="th">Description</th>
                        <th class="th">Created by</th>
                        <th class="th"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($data as $location)
                    <tr>
                        <td class="td tabular-nums text-muted">{{ $location->location_id }}</td>
                        <td class="td font-medium">{{ $location->location_name }}</td>
                        <td class="td max-w-md text-muted">{{ $location->description }}</td>
                        <td class="td tabular-nums text-muted">{{ $location->created_by }}</td>
                        <td class="td">
                            @if ($isAdmin)
                            <div class="flex justify-end gap-1.5">
                                <a href="{{ route('locations.edit', $location->location_id) }}" class="btn btn-secondary btn-sm">Edit</a>
                                <form action="{{ route('locations.delete', $location->location_id) }}" method="POST" onsubmit="return confirm(@js('Delete ' . $location->location_name . '?'))">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                </form>
                            </div>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="td py-10 text-center text-muted">No locations found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $data->links('pagination::tailwind') }}</div>
    </div>
</x-app-layout>
