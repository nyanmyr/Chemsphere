<!DOCTYPE html>
<html>

<head>
    <title>Chemsphere | Users</title>
</head>

<body>
    <h1>Users</h1>

    @if (session('message') || session('error') || $errors->any())
    <div style="color: red;">
        {{ session('error') ?? session('message') ?? $errors->first() }}
    </div>
    @endif

    <form action="{{ route('users') }}" method="GET">
        <input type="text" name="search" value="{{ request('search') }}" size="100" placeholder="Search email...">

        <br>
        <label for="search_user_role">Search by Role:</label>
        <select id="search_user_role" name="search_user_role[]" size="1" multiple>
            @foreach (\App\UserRole::cases() as $class)
            <option value="{{ $class->value }}" @selected(in_array($class->value, (array) request('search_user_role', old('search_user_role', $user->search_user_role->value ?? $user->search_user_role ?? []))))>
                {{ $class->value }}
            </option>
            @endforeach
        </select>

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
