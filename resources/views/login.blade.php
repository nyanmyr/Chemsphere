<!DOCTYPE html>
<html>
<head><title>Chemsphere | Login</title></head>
<body>
    <h1>Login</h1>

    @if($errors->any())
        <div style="color: red;">{{ $errors->first() }}</div>
    @endif

    <form action="/login" method="POST">
        @csrf
        <label>Email:</label><br>
        <input type="email" name="email" required><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit">Login</button>
    </form>
    <br>
    <a href="{{ route('google.login') }}">Login with Google</a>
</body>
</html>
