<!DOCTYPE html>
<html>

<head>
    <title>Chemsphere | Locations</title>
</head>

<body>
    <h1>Locations</h1>

    <form action="{{ route('locations') }}" method="GET">
        <input type="text" name="search" value="{{ request('search') }}" size="100" placeholder="Search name or description...">

        <br>
        <label>Search by ID:</label>
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
        <button type="submit">Search</button>
    </form>

    @if(session('error'))
    <div style="color: red;">{{ session('error') }}</div>
    @endif

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Created By</th>
                <th>Name</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $location)
            <tr>
                <td>{{ $location->location_id }}</td>
                <td>{{ $location->created_by }}</td>
                <td>{{ $location->location_name }}</td>
                <td>{{ $location->description }}</td>
                @if ($user?->user_role?->isAdmin())
                <td>
                    <form action="{{ route('locations.delete', $location->location_id) }}" method="POST" onsubmit="return confirm('Delete location?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Delete</button>
                    </form>
                </td>
                <td>
                    <form action="{{ route('locations.edit', $location->location_id) }}" method="GET">
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
    <a href="{{ route('locations.create') }}">Create</a>
    @endif

    <br>
    <a href="{{ route('welcome') }}">Return</a>
</body>

</html>
