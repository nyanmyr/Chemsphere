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
        <label for="search_equipment_id_min">Min</label>
        <input id="search_equipment_id_min" type="number" name="search_equipment_id_min" value="{{ request('search_equipment_id_min') }}" min="1" step="1" size="20" placeholder="min…">
        <br>
        <label for="search_equipment_id_max">Max</label>
        <input id="search_equipment_id_max" type="number" name="search_equipment_id_max" value="{{ request('search_equipment_id_max') }}" min="1" step="1" size="20" placeholder="max…">

        <br>
        <label>Search by Location ID:</label>
        <br>
        <label for="search_location_id_min">Min</label>
        <input id="search_location_id_min" type="number" name="search_location_id_min" value="{{ request('search_location_id_min') }}" min="1" step="1" size="20" placeholder="min…">
        <br>
        <label for="search_location_id_max">Max</label>
        <input id="search_location_id_max" type="number" name="search_location_id_max" value="{{ request('search_location_id_max') }}" min="1" step="1" size="20" placeholder="max…">

        <br>
        <label>Search by Created By:</label>
        <br>
        <label for="search_created_by_min">Min</label>
        <input id="search_created_by_min" type="number" name="search_created_by_min" value="{{ request('search_created_by_min') }}" min="1" step="1" size="20" placeholder="min…">
        <br>
        <label for="search_created_by_max">Max</label>
        <input id="search_created_by_max" type="number" name="search_created_by_max" value="{{ request('search_created_by_max') }}" min="1" step="1" size="20" placeholder="max…">

        <br>
        <label for="search_status">Search by Status:</label>
        <select id="search_status" name="search_status[]" size="1" multiple>
            @foreach (\App\EquipmentStatus::cases() as $class)
            <option value="{{ $class->value }}" @selected(in_array($class->value, (array) request('search_status', old('search_status', $user->search_status->value ?? $user->search_status ?? []))))>
                {{ $class->value }}
            </option>
            @endforeach
        </select>

        <br>
        <label>Search by Initial Quantity:</label>
        <br>
        <label for="search_initial_quantity_min">Min</label>
        <input id="search_initial_quantity_min" type="number" name="search_initial_quantity_min" value="{{ request('search_initial_quantity_min') }}" min="0" step="0.001" size="20" placeholder="min…">
        <br>
        <label for="search_initial_quantity_max">Max</label>
        <input id="search_initial_quantity_max" type="number" name="search_initial_quantity_max" value="{{ request('search_initial_quantity_max') }}" min="0" step="0.001" size="20" placeholder="max…">

        <br>
        <label>Search by Current Quantity:</label>
        <br>
        <label for="search_current_quantity_min">Min</label>
        <input id="search_current_quantity_min" type="number" name="search_current_quantity_min" value="{{ request('search_current_quantity_min') }}" min="0" step="0.001" size="20" placeholder="min…">
        <br>
        <label for="search_current_quantity_max">Max</label>
        <input id="search_current_quantity_max" type="number" name="search_current_quantity_max" value="{{ request('search_current_quantity_max') }}" min="0" step="0.001" size="20" placeholder="max…">

        <br>
        <label>Search by Purchase Date:</label>
        <br>
        <label for="search_purchase_date_min">Min</label>
        <input id="search_purchase_date_min" type="date" name="search_purchase_date_min" value="{{ request('search_purchase_date_min') }}" size="20" placeholder="min…">
        <br>
        <label for="search_purchase_date_max">Max</label>
        <input id="search_purchase_date_max" type="date" name="search_purchase_date_max" value="{{ request('search_purchase_date_max') }}" size="20" placeholder="max…">

        <br>
        <label>Search by Warranty Expiration:</label>
        <br>
        <label for="search_warranty_expiration_min">Min</label>
        <input id="search_warranty_expiration_min" type="date" name="search_warranty_expiration_min" value="{{ request('search_warranty_expiration_min') }}" size="20" placeholder="min…">
        <br>
        <label for="search_warranty_expiration_max">Max</label>
        <input id="search_warranty_expiration_max" type="date" name="search_warranty_expiration_max" value="{{ request('search_warranty_expiration_max') }}" size="20" placeholder="max…">

        <br>
        <label>Search by Last Maintenance:</label>
        <br>
        <label for="search_last_maintenance_min">Min</label>
        <input id="search_last_maintenance_min" type="date" name="search_last_maintenance_min" value="{{ request('search_last_maintenance_min') }}" size="20" placeholder="min…">
        <br>
        <label for="search_last_maintenance_max">Max</label>
        <input id="search_last_maintenance_max" type="date" name="search_last_maintenance_max" value="{{ request('search_last_maintenance_max') }}" size="20" placeholder="max…">

        <br>
        <label>Search by Next Maintenance:</label>
        <br>
        <label for="search_next_maintenance_min">Min</label>
        <input id="search_next_maintenance_min" type="date" name="search_next_maintenance_min" value="{{ request('search_next_maintenance_min') }}" size="20" placeholder="min…">
        <br>
        <label for="search_next_maintenance_max">Max</label>
        <input id="search_next_maintenance_max" type="date" name="search_next_maintenance_max" value="{{ request('search_next_maintenance_max') }}" size="20" placeholder="max…">

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
