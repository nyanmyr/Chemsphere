<!DOCTYPE html>
<html>

<head>
    <title>Chemsphere | Locations</title>
</head>

<body>
    <h1>Inventory</h1>

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
                </tr>
            @endforeach
        </tbody>
    </table>

    <br>
    @if($user?->user_role === 'admin')
        <a href="{{ route('create_location') }}">Create</a>
    @endif

    <br>
    <a href="{{ route('welcome') }}">Return</a>
</body>

</html>
