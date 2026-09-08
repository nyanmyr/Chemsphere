<!DOCTYPE html>
<html>

<head>
    <title>Chemsphere | Equipment</title>
</head>

<body>
    <h1>Equipment</h1>

    <form action="{{ route('equipment') }}" method="GET">
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            size="100"
            placeholder="Search name, model, or serial..."
        >
        <button type="submit">Search</button>

        @if(request('search'))
            <a href="{{ route('equipment') }}">Clear</a>
        @endif
    </form>

    <table>
        <thead>
            <tr>
                <th>Chemical ID</th>
                <th>Location ID</th>
                <th>Created By</th>
                <th>Name</th>
                <th>Model</th>
                <th>Serial ID</th>
                <th>Status</th>
                <th>Initial Quantity</th>
                <th>Current Quantity</th>
                <th>Purchase Date</th>
                <th>Warranty Expiration</th>
                <th>Last Maintenance</th>
                <th>Next Maintenance</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $equipment)
                <tr>
                    <td>{{ $equipment->equipment_id }}</td>
                    <td>{{ $equipment->location_id }}</td>
                    <td>{{ $equipment->created_by }}</td>
                    <td>{{ $equipment->equipment_name }}</td>
                    <td>{{ $equipment->model }}</td>
                    <td>{{ $equipment->serial_id }}</td>
                    <td>{{ $equipment->status }}</td>
                    <td>{{ $equipment->initial_quantity }}</td>
                    <td>{{ $equipment->current_quantity }}</td>
                    <td>{{ $equipment->purchase_date }}</td>
                    <td>{{ $equipment->warranty_expiration }}</td>
                    <td>{{ $equipment->last_maintenance }}</td>
                    <td>{{ $equipment->next_maintenance }}</td>
                    <td>
                        <form
                            action="{{ route('equipment.use.edit', $equipment->equipment_id) }}"
                            method="GET">
                            <button type="submit">Use</button>
                        </form>
                    </td>
                    @if ($user?->user_role?->isAdmin())
                        <td>
                            <form
                                action="{{ route('equipment.delete', $equipment->equipment_id) }}"
                                method="POST" onsubmit="return confirm('Delete equipment?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Delete</button>
                            </form>
                        </td>
                        <td>
                            <form
                                action="{{ route('equipment.edit', $equipment->equipment_id) }}"
                                method="GET">
                                <button type="submit">Edit</button>
                            </form>
                        </td>
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>

    <br>
    @if ($user?->user_role?->isAdmin())
        <a href="{{ route('equipment.create') }}">Create</a>
    @endif

    <br>
    <a href="{{ route('welcome') }}">Return</a>
</body>

</html>
