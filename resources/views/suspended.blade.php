<x-guest-layout title="Account suspended" heading="Your account has been suspended">
    <p class="text-sm text-muted">Your account is currently under review. Check later for updates.</p>

    @auth
    <form action="{{ route('logout') }}" method="POST" class="mt-6">
        @csrf
        <button type="submit" class="btn btn-secondary w-full">Sign out</button>
    </form>
    @endauth
</x-guest-layout>
