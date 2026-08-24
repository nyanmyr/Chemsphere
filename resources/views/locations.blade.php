<!DOCTYPE html>
<html>

<head>
    <title>Chemsphere | Locations</title>
</head>

<body>
    <h1>Locations</h1>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $location)
            <tr>
                <td>{{ $location->location_id }}</td>
                <td>{{ $location->location_name }}</td>
                <td>{{ $location->description }}</td>
                <td>
                    <form action="{{
                        route(
                            'locations.delete',
                            $location->location_id
                        )
                    }}"
                    method="POST"
                    onsubmit="return confirm('Delete location?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit">Delete</button>
                    </form>
                </td>
                <td>
                    <form action="{{
                        route(
                            'locations.edit',
                            $location->location_id
                        )
                    }}" method="GET">
                        <button type="submit">Edit</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <br>
    @if($user?->user_role?->isAdmin())
    <a href="{{ route('locations.create') }}">Create</a>
    @endif

    <br>
    <a href="{{ route('welcome') }}">Return</a>
</body>

</html>
