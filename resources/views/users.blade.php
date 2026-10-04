@php
    $tone = ['pending' => 'badge-warning', 'admin' => 'badge-ok', 'user' => 'badge-neutral'];
@endphp

<x-app-layout title="Users">
    <x-filter-bar :action="route('users')" placeholder="Search email"
        :ranges="['user_id' => ['User ID', 'number', '1']]"
        :choices="['search_user_role' => ['Role', \App\Enums\UserRole::cases()]]" sortable />

    <div id="users-table">
        <div class="card overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-line">
                    <tr>
                        <th class="th">ID</th>
                        <th class="th">Email</th>
                        <th class="th">Role</th>
                        <th class="th"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($data as $row)
                    @php $role = $row->user_role->value; @endphp
                    <tr>
                        <td class="td tabular-nums text-muted">{{ $row->user_id }}</td>
                        <td class="td font-medium">{{ $row->email }}
                            @if (Auth::id() === $row->user_id)<span class="ml-1 text-xs font-normal text-muted">(you)</span>@endif
                        </td>
                        <td class="td"><span class="badge {{ $tone[$role] ?? 'badge-neutral' }}">{{ ucfirst($role) }}</span></td>
                        <td class="td text-right">
                            @if (Auth::id() !== $row->user_id)
                            <a href="{{ route('users.edit', $row->user_id) }}" class="btn btn-secondary btn-sm">{{ $role === 'pending' ? 'Review' : 'Edit role' }}</a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="td py-10 text-center text-muted">No users found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $data->links('pagination::tailwind') }}</div>
    </div>
</x-app-layout>
