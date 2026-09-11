<!DOCTYPE html>
<html>

<head>
    <title>Chemsphere | Equipment</title>
</head>

<body>
    <h1>Equipment</h1>

    <form action="{{ route('equipment') }}" method="GET">
        <input type="text" name="search" value="{{ request('search') }}" size="100" placeholder="Search name, model, or serial...">

        <br>
        <label>Search by ID:</label>
        <br>
        <label for="equipment_id_min">Min</label>
        <input id="equipment_id_min" type="number" name="equipment_id_min" value="{{ request('equipment_id_min') }}" min="1" step="1" size="20" placeholder="min…">
        <br>
        <label for="equipment_id_max">Max</label>
        <input id="equipment_id_max" type="number" name="equipment_id_max" value="{{ request('equipment_id_max') }}" min="1" step="1" size="20" placeholder="max…">

        <br>
        <label>Search by Location ID:</label>
        <br>
        <label for="location_id_min">Min</label>
        <input id="location_id_min" type="number" name="location_id_min" value="{{ request('location_id_min') }}" min="1" step="1" size="20" placeholder="min…">
        <br>
        <label for="location_id_max">Max</label>
        <input id="location_id_max" type="number" name="location_id_max" value="{{ request('location_id_max') }}" min="1" step="1" size="20" placeholder="max…">

        <br>
        <label>Search by Created By:</label>
        <br>
        <label for="created_by_min">Min</label>
        <input id="created_by_min" type="number" name="created_by_min" value="{{ request('created_by_min') }}" min="1" step="1" size="20" placeholder="min…">
        <br>
        <label for="created_by_max">Max</label>
        <input id="created_by_max" type="number" name="created_by_max" value="{{ request('created_by_max') }}" min="1" step="1" size="20" placeholder="max…">

        <br>
        <label for="search_status">Search by Unit:</label>
        <select id="search_status" name="search_status[]" size="1" multiple>
            @foreach (\App\EquipmentStatus::cases() as $class)
            <option value="{{ $class->value }}" @selected(in_array($class->value, (array) request('search_status', old('search_status', $user->search_status->value ?? $user->search_status ?? []))))>
                {{ $class->value }}
            </option>
            @endforeach
        </select>

        <button type="submit">Search</button>

        @if(request('search'))
        <a href="{{ route('equipment') }}">Clear</a>
        @endif
    </form>

    <table>
        <thead>
            <tr>
                <th>Equipment ID</th>
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
                    <form action="{{ route('equipment.use.edit', $equipment->equipment_id) }}" method="GET">
                        <button type="submit">Use</button>
                    </form>
                </td>
                @if ($user?->user_role?->isAdmin())
                <td>
                    <form action="{{ route('equipment.delete', $equipment->equipment_id) }}" method="POST" onsubmit="return confirm('Delete equipment?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Delete</button>
                    </form>
                </td>
                <td>
                    <form action="{{ route('equipment.edit', $equipment->equipment_id) }}" method="GET">
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
