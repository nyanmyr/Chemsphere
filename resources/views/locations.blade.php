<!DOCTYPE html>
<html>

<head>
    <title>Chemsphere | Locations</title>
</head>

<body>
    <h1>Locations</h1>

    <form action="{{ route('locations') }}" method="GET">
        <input type="text" name="search" value="{{ request('search') }}" size="100" placeholder="Search name or description...">
        <button type="submit">Search</button>

        @if(request()->filled('search'))
        <a href="{{ route('locations') }}">Clear</a>
        @endif
    </form>

    <form action="{{ route('locations.search.created_by', ['type' => 'created_by']) }}" method="GET">
        <label>Search by Created By:</label>
        <br>

        <label for="min">Min</label>
        <input id="min" type="number" name="min" value="{{ request('min') }}" min="0" step="1" size="20" placeholder="min…">
        <br>

        <label for="max">Max</label>
        <input id="max" type="number" name="max" value="{{ request('max') }}" min="0" step="1" size="20" placeholder="max…">

        <button type="submit">Search</button>

        @if(request()->filled('min') || request()->filled('max'))
        <a href="{{ route('locations') }}">Clear</a>
        @endif
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
