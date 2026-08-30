<!DOCTYPE html>
<html>

<head>
    <title>Chemsphere | Register</title>
</head>

<body>
    <h1>Register</h1>

    @if ($errors->any())
        <div style="color: red;">{{ $errors->first() }}</div>
    @endif

    <form action="/register" method="POST">
        @csrf
        <label>Email:</label><br>
        <input type="email" name="email" required><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Register</button>
    </form>
    <br>
    <a href="{{ route('google.login') }}">Register with Google</a>
</body>

</html>
