<!DOCTYPE html>
<html>

<head>
    <title>Chemsphere | Users</title>
</head>

<body>
    <h1>Edit User</h1>

    <div>ID: {{ old('user_id', $user->user_id) }}</div>
    <div>Email: {{ old('email', $user->email) }}</div>

    <form action="{{ route('users.update', $user->user_id) }}" method="POST">
        @csrf
        @method('PUT')

        <br>
        <label for="user_role">User Role</label>
        <br>
        <select id="user_role" name="user_role">
            @foreach (\App\UserRole::cases() as $class)
                <option value="{{ $class->value }}" @selected(old('user_role', $user->user_role->value ?? $user->user_role) == $class->value)>{{ $class->value }}</option>
            @endforeach
        </select>

        @error('user_role')
            <div>{{ $message }}</div>
        @enderror

        <button type="submit">Update</button>
    </form>

    <br>
    <a href="{{ route('users') }}">Cancel</a>
</body>

</html>
