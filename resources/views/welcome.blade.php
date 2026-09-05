<!DOCTYPE html>
<html>

<head>
    <title>Chemsphere</title>
</head>

<body>
    <h1>Welcome to Chemsphere</h1>

    @if (session('message') || $errors->any())
        <div style="color: red;">
            {{ session('message') ?? $errors->first() }}
        </div>
    @endif

    @auth
        <p>Logged in as: {{ auth()->user()->email }}</p>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit">Logout</button>
        </form>
        <a href="{{ route('inventory') }}">Inventory</a>
        <a href="{{ route('locations') }}">Locations</a>
        <a href="{{ route('equipment') }}">Equipment</a>
        {{-- @if (auth()->user()->user_role->isRole(\App\UserRole::ADMIN)) --}}
            <a href="{{ route('users') }}">Manage Users</a>
        {{-- @endif --}}

    @else
        <a href="{{ route('login') }}">Login</a>
        <br>
        <a href="{{ route('register') }}">Register</a>
    @endauth
</body>

</html>
