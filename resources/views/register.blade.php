<x-guest-layout title="Create account" heading="Create your account">
    <form action="/register" method="POST" class="space-y-4">
        @csrf
        <div>
            <label for="email" class="label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email" class="field">
        </div>
        <div>
            <label for="password" class="label">Password</label>
            <input id="password" type="password" name="password" required autocomplete="new-password" class="field">
        </div>
        <button type="submit" class="btn btn-primary w-full">Create account</button>
    </form>

    <p class="mt-6 text-center text-sm text-muted">Already registered? <a href="{{ route('login') }}" class="link">Sign in</a></p>
</x-guest-layout>
