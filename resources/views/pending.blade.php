<x-guest-layout title="Awaiting approval" heading="Your account is awaiting approval">
    <p class="text-sm text-muted">
        An admin needs to approve your account before you can use the inventory. Check back
        once they have.
    </p>

    @auth
        <form action="{{ route('logout') }}" method="POST" class="mt-6">
            @csrf
            <button type="submit" class="btn btn-secondary w-full">
                Sign out
            </button>
        </form>
    @endauth
</x-guest-layout>
