<!DOCTYPE html>
<html>

<head>
    <title>Chemsphere | Inventory</title>
</head>

<body>
    <h1>Inventory</h1>

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
                <th>Current Number</th>
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
                    @if ($user?->user_role?->isAdmin())
                        <td>
                            <form
                                action="{{ route('inventory.delete', $chemical->chemical_id) }}"
                                method="POST" onsubmit="return confirm('Delete chemical?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit">Delete</button>
                            </form>
                        </td>
                        <td>
                            <form
                                action="{{ route('inventory.edit', $chemical->chemical_id) }}"
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
        <a href="{{ route('inventory.create') }}">Create</a>
    @endif

    <br>
    <a href="{{ route('welcome') }}">Return</a>
</body>

</html>
