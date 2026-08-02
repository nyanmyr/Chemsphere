<!DOCTYPE html>
<html>

<head>
    <title>Chemsphere</title>
</head>

<body>
    <h1>Welcome to Chemsphere</h1>

    @auth
        <p>Logged in as: {{ auth()->user()->email }}</p>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit">Logout</button>
        </form>
    @else
        <a href="{{ route('login') }}">Login</a>
        <br>
        <a href="{{ route('register') }}">Register</a>
    @endauth
</body>

</html>
