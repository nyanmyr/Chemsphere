<x-app-layout title="Edit user">
    <dl class="mb-6 grid grid-cols-2 gap-x-6 text-sm sm:max-w-md">
        <div><dt class="text-muted">ID</dt><dd class="font-medium tabular-nums">{{ $user->user_id }}</dd></div>
        <div class="min-w-0"><dt class="text-muted">Email</dt><dd class="truncate font-medium">{{ $user->email }}</dd></div>
    </dl>

    <form action="{{ route('users.update', $user->user_id) }}" method="POST" class="card max-w-md space-y-4 p-6">
        @csrf
        @method('PUT')
        <x-select name="user_role" label="Role" :options="\App\UserRole::cases()" :value="$user->user_role->value" />
        <x-form-actions submit="Save changes" :cancel="url()->previous(route('users'))" />
    </form>
</x-app-layout>
