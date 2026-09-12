<!DOCTYPE html>
<html>

<head>
    <title>Chemsphere | Inventory</title>
</head>

<body>
    <h1>Inventory</h1>

    @error('current_quantity')
    <div style="color: red;">{{ $message }}</div>
    @enderror

    <form action="{{ route('inventory') }}" method="GET">
        <input type="text" name="search" value="{{ request('search') }}" size="100" placeholder="Search name, batch, or brand...">

        <br>
        <label>Search by ID:</label>
        <br>
        <label for="search_chemical_id_min">Min</label>
        <input id="search_chemical_id_min" type="number" name="search_chemical_id_min" value="{{ request('search_chemical_id_min') }}" min="1" step="1" size="20" placeholder="min…">
        <br>
        <label for="search_chemical_id_max">Max</label>
        <input id="search_chemical_id_max" type="number" name="search_chemical_id_max" value="{{ request('search_chemical_id_max') }}" min="1" step="1" size="20" placeholder="max…">

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
        <label>Search by Volume Per Unit:</label>
        <br>
        <label for="search_volume_per_unit_min">Min</label>
        <input id="search_volume_per_unit_min" type="number" name="search_volume_per_unit_min" value="{{ request('search_volume_per_unit_min') }}" min="0" step="0.001" size="20" placeholder="min…">
        <br>
        <label for="search_volume_per_unit_max">Max</label>
        <input id="search_volume_per_unit_max" type="number" name="search_volume_per_unit_max" value="{{ request('search_volume_per_unit_max') }}" min="0" step="0.001" size="20" placeholder="max…">

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
        <label for="search_safety_classes">Search by Safety Class:</label>
        <select id="search_safety_classes" name="search_safety_classes[]" size="1" multiple>
            @foreach (\App\SafetyClass::cases() as $class)
            <option value="{{ $class->value }}" @selected(in_array($class->value, (array) request('search_safety_classes', old('search_safety_classes', $user->search_safety_classes->value ?? $user->search_safety_classes ?? []))))>
                {{ $class->value }}
            </option>
            @endforeach
        </select>

        <br>
        <label for="search_ghs_symbols">Search by GHS Symbol:</label>
        <select id="search_ghs_symbols" name="search_ghs_symbols[]" size="1" multiple>
            @foreach (\App\GHSSymbol::cases() as $class)
            <option value="{{ $class->value }}" @selected(in_array($class->value, (array) request('search_ghs_symbols', old('search_ghs_symbols', $user->search_ghs_symbols->value ?? $user->search_ghs_symbols ?? []))))>
                {{ $class->value }}
            </option>
            @endforeach
        </select>

        <br>
        <label for="search_unit">Search by Unit:</label>
        <select id="search_unit" name="search_unit[]" size="1" multiple>
            @foreach (\App\Unit::cases() as $class)
            <option value="{{ $class->value }}" @selected(in_array($class->value, (array) request('search_unit', old('search_unit', $user->search_unit->value ?? $user->search_unit ?? []))))>
                {{ $class->value }}
            </option>
            @endforeach
        </select>

        <button type="submit">Search</button>

        @if(request('search'))
        <a href="{{ route('inventory') }}">Clear</a>
        @endif
    </form>

    <table>
        <thead>
            <tr>
                <th>Chemical ID</th>
                <th>Location ID</th>
                <th>Created By</th>
                <th>Name</th>
                <th>Batch Number</th>
                <th>Brand Name</th>
                <th>Volume Per Unit</th>
                <th>Initial Quantity</th>
                <th>Current Quantity</th>
                <th>Expiration Date</th>
                <th>Arrival Date</th>
                <th>Safety Classes</th>
                <th>GHS Symbols</th>
                <th>Unit</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $chemical)
            <tr>
                <td>{{ $chemical->chemical_id }}</td>
                <td>{{ $chemical->location_id }}</td>
                <td>{{ $chemical->created_by }}</td>
                <td>{{ $chemical->chemical_name }}</td>
                <td>{{ $chemical->batch_number }}</td>
                <td>{{ $chemical->brand_name }}</td>
                <td>{{ $chemical->volume_per_unit }}</td>
                <td>{{ $chemical->initial_quantity }}</td>
                <td>{{ $chemical->current_quantity }}</td>
                <td>{{ $chemical->expiration_date }}</td>
                <td>{{ $chemical->arrival_date }}</td>
                <td>{{ $chemical->safety_classes }}</td>
                <td>{{ $chemical->ghs_symbols }}</td>
                <td>{{ $chemical->unit }}</td>
                <td>
                    <form action="{{ route('inventory.use.edit', $chemical->chemical_id) }}" method="GET">
                        <button type="submit">Use</button>
                    </form>
                </td>
                @if ($user?->user_role?->isAdmin())
                <td>
                    <form action="{{ route('inventory.delete', $chemical->chemical_id) }}" method="POST" onsubmit="return confirm('Delete chemical?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Delete</button>
                    </form>
                </td>
                <td>
                    <form action="{{ route('inventory.edit', $chemical->chemical_id) }}" method="GET">
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
    <a href="{{ route('inventory.create') }}">Create</a>
    @endif

    <br>
    <a href="{{ route('welcome') }}">Return</a>
</body>

</html>
