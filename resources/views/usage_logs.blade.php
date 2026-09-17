<!DOCTYPE html>
<html>

<head>
    <title>Chemsphere | Usage Logs</title>
</head>

<body>
    <h1>Usage Logs</h1>

    @if (session('message') || session('error') || $errors->any())
    <div style="color: red;">
        {{ session('error') ?? session('message') ?? $errors->first() }}
    </div>
    @endif

    <form action="{{ route('usage_logs') }}" method="GET">
        <input type="text" name="search" value="{{ request('search') }}" size="100" placeholder="Search notes...">

        <br>
        <label>Search by ID:</label>
        <br>
        <label for="search_usage_log_id_min">Min</label>
        <input id="search_usage_log_id_min" type="number" name="search_usage_log_id_min" value="{{ request('search_usage_log_id_min') }}" min="1" step="1" size="20" placeholder="min…">
        <br>
        <label for="search_usage_log_id_max">Max</label>
        <input id="search_usage_log_id_max" type="number" name="search_usage_log_id_max" value="{{ request('search_usage_log_id_max') }}" min="1" step="1" size="20" placeholder="max…">

        <br>
        <label>Search by Created By:</label>
        <br>
        <label for="search_created_by_min">Min</label>
        <input id="search_created_by_min" type="number" name="search_created_by_min" value="{{ request('search_created_by_min') }}" min="1" step="1" size="20" placeholder="min…">
        <br>
        <label for="search_created_by_max">Max</label>
        <input id="search_created_by_max" type="number" name="search_created_by_max" value="{{ request('search_created_by_max') }}" min="1" step="1" size="20" placeholder="max…">

        <br>
        <label>Search by Location ID:</label>
        <br>
        <label for="search_location_id_min">Min</label>
        <input id="search_location_id_min" type="number" name="search_location_id_min" value="{{ request('search_location_id_min') }}" min="1" step="1" size="20" placeholder="min…">
        <br>
        <label for="search_location_id_max">Max</label>
        <input id="search_location_id_max" type="number" name="search_location_id_max" value="{{ request('search_location_id_max') }}" min="1" step="1" size="20" placeholder="max…">

        <br>
        <label for="search_item_type">Search by Safety Class:</label>
        <select id="search_item_type" name="search_item_type[]" size="1" multiple>
            @foreach (\App\ItemType::cases() as $class)
            <option value="{{ $class->value }}" @selected(in_array($class->value, (array) request('search_item_type', old('search_item_type', $user->search_item_type->value ?? $user->search_item_type ?? []))))>
                {{ $class->value }}
            </option>
            @endforeach
        </select>

        <br>
        <label>Search by Item ID:</label>
        <br>
        <label for="search_item_id_min">Min</label>
        <input id="search_item_id_min" type="number" name="search_item_id_min" value="{{ request('search_item_id_min') }}" min="1" step="1" size="20" placeholder="min…">
        <br>
        <label for="search_item_id_max">Max</label>
        <input id="search_item_id_max" type="number" name="search_item_id_max" value="{{ request('search_item_id_max') }}" min="1" step="1" size="20" placeholder="max…">

        <br>
        <label>Search by Quantity used:</label>
        <br>
        <label for="search_quantity_used_min">Min</label>
        <input id="search_quantity_used_min" type="number" name="search_quantity_used_min" value="{{ request('search_quantity_used_min') }}" min="0" step="0.001" size="20" placeholder="min…">
        <br>
        <label for="search_quantity_used_max">Max</label>
        <input id="search_quantity_used_max" type="number" name="search_quantity_used_max" value="{{ request('search_quantity_used_max') }}" min="0" step="0.001" size="20" placeholder="max…">

        <br>
        <label>Search by Quantity Remaining:</label>
        <br>
        <label for="search_quantity_remaining_min">Min</label>
        <input id="search_quantity_remaining_min" type="number" name="search_quantity_remaining_min" value="{{ request('search_quantity_remaining_min') }}" min="0" step="0.001" size="20" placeholder="min…">
        <br>
        <label for="search_quantity_remaining_max">Max</label>
        <input id="search_quantity_remaining_max" type="number" name="search_quantity_remaining_max" value="{{ request('search_quantity_remaining_max') }}" min="0" step="0.001" size="20" placeholder="max…">

        <br>
        <button type="reset">Clear</button>
        <br>
        <button type="submit">Search</button>

        @if(request('search'))
        <a href="{{ route('usage_logs') }}">Clear</a>
        @endif
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Created By</th>
                <th>Location ID</th>
                <th>Item Type</th>
                <th>Item ID</th>
                <th>Quantity Used</th>
                <th>Quantity Remaining</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $usage_log)
            <tr>
                <td>{{ $usage_log->usage_log_id }}</td>
                <td>{{ $usage_log->created_by }}</td>
                <td>{{ $usage_log->location_id }}</td>
                <td>{{ $usage_log->item_type }}</td>
                <td>{{ $usage_log->item_id }}</td>
                <td>{{ $usage_log->quantity_used }}</td>
                <td>{{ $usage_log->quantity_remaining }}</td>
                <td>{{ $usage_log->notes }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $data->links('pagination::bootstrap-5') }}

    <br>
    <a href="{{ route('welcome') }}">Return</a>
</body>

</html>
