<!DOCTYPE html>
<html>

<head>
    <title>Chemsphere | Users</title>
</head>

<body>
    <h1>Users</h1>

    @if (session('message') || $errors->any())
        <div style="color: red;">
            {{ session('message') ?? $errors->first() }}
        </div>
    @endif

    <form action="{{ route('users') }}" method="GET">
        <input
            type="text"
            name="search"
            value="{{ request('search') }}"
            size="100"
            placeholder="Search email..."
        >
        <button type="submit">Search</button>

        @if(request('search'))
            <a href="{{ route('users') }}">Clear</a>
        @endif
    </form>

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
                @if (Auth::user()['user_id'] != $user->user_id)
                    <td>
                        <form action="{{ route('users.edit', $user->user_id) }}" method="GET">
                            <button type="submit">Edit</button>
                        </form>
                    </td>
                @endif
            </tr>
            @endforeach
        </tbody>
    </table>

    <br>
    <a href="{{ route('welcome') }}">Return</a>
</body>

</html>
