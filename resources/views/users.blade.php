<!DOCTYPE html>
<html>

<head>
    <title>Chemsphere | Users</title>
</head>

<body>
    <h1>Users</h1>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>User Role</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $user)
            <tr>
                <td>{{ $user->user_id }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->user_role }}</td>
                <td>
                    <form action="{{ route('users.edit', $user->user_id) }}" method="GET">
                        <button type="submit">Edit</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <br>
    <a href="{{ route('welcome') }}">Return</a>
</body>

</html>
