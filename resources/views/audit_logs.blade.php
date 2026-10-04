@php $tone = ['CREATE' => 'badge-ok', 'UPDATE' => 'badge-neutral', 'DELETE' => 'badge-danger']; @endphp

<x-app-layout title="Audit logs">
    <x-filter-bar :action="route('audit_logs')" placeholder="Search target"
        :ranges="['audit_log_id' => ['Log ID', 'number', '1'], 'created_by' => ['Made by (user ID)', 'number', '1']]"
        :choices="['search_audit_action' => ['Action', \App\AuditAction::cases()]]" />

    <div id="audit-logs-table">
        <div class="card overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="border-b border-line">
                    <tr>
                        <th class="th">When</th>
                        <th class="th">Action</th>
                        <th class="th">Target</th>
                        <th class="th">Created By</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-line">
                    @forelse ($data as $log)
                    @php $action = (string) ($log->audit_action->value ?? $log->audit_action); @endphp
                    <tr>
                        <td class="td whitespace-nowrap">
                            {{ $log->created_at->format('M j, Y g:i A') }}
                            <p class="text-xs text-muted">Log #{{ $log->audit_log_id }}</p>
                        </td>
                        <td class="td"><span class="badge {{ $tone[$action] ?? 'badge-neutral' }}">{{ $action }}</span></td>
                        <td class="td">{{ $log->target }}</td>
                        <td class="td">
                            <p class="font-medium">{{ $log->user?->email }}</p>
                            <p class="text-xs text-muted">#{{ $log->user?->user_id }}</p>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="td py-10 text-center text-muted">Nothing recorded yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $data->links('pagination::tailwind') }}</div>
    </div>
</x-app-layout>
