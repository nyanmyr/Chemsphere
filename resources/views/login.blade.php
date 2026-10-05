<x-guest-layout title="Sign in" heading="Sign in">
    <form action="/login" method="POST" class="space-y-4">
        @csrf
        <div>
            <label for="email" class="label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" class="field">
        </div>
        <div>
            <label for="password" class="label">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" class="field">
        </div>
        <button type="submit" class="btn btn-primary w-full">Sign in</button>
    </form>

    <div class="my-4 text-center text-xs text-muted">
        or
    </div>
    <a href="{{ route('google.login') }}" class="btn btn-secondary w-full">
        Continue with Google
    </a>

    <p class="mt-6 text-center text-sm text-muted">
        No account yet?
        <a href="{{ route('register') }}" class="link">
            Create one
        </a>
    </p>
</x-guest-layout>
