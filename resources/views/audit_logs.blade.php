<!DOCTYPE html>
<html>

<head>
    <title>Chemsphere | Audit Logs</title>
</head>

<body>
    <h1>Audit Logs</h1>

    @if (session('message') || session('error') || $errors->any())
    <div style="color: red;">
        {{ session('error') ?? session('message') ?? $errors->first() }}
    </div>
    @endif

    <form action="{{ route('audit_logs') }}" method="GET">
        <input type="text" name="search" value="{{ request('search') }}" size="100" placeholder="Search target...">

        <br>
        <label>Search by ID:</label>
        <br>
        <label for="search_audit_log_id_min">Min</label>
        <input id="search_audit_log_id_min" type="number" name="search_audit_log_id_min" value="{{ request('search_audit_log_id_min') }}" min="1" step="1" size="20" placeholder="min…">
        <br>
        <label for="search_audit_log_id_max">Max</label>
        <input id="search_audit_log_id_max" type="number" name="search_audit_log_id_max" value="{{ request('search_audit_log_id_max') }}" min="1" step="1" size="20" placeholder="max…">

        <br>
        <label>Search by Created By:</label>
        <br>
        <label for="search_created_by_min">Min</label>
        <input id="search_created_by_min" type="number" name="search_created_by_min" value="{{ request('search_created_by_min') }}" min="1" step="1" size="20" placeholder="min…">
        <br>
        <label for="search_created_by_max">Max</label>
        <input id="search_created_by_max" type="number" name="search_created_by_max" value="{{ request('search_created_by_max') }}" min="1" step="1" size="20" placeholder="max…">

        <br>
        <label for="search_audit_action">Search by Safety Class:</label>
        <select id="search_audit_action" name="search_audit_action[]" size="1" multiple>
            @foreach (\App\AuditAction::cases() as $class)
            <option value="{{ $class->value }}" @selected(in_array($class->value, (array) request('search_audit_action', old('search_audit_action', $user->search_audit_action->value ?? $user->search_audit_action ?? []))))>
                {{ $class->value }}
            </option>
            @endforeach
        </select>

        <button type="submit">Search</button>

        @if(request('search'))
        <a href="{{ route('audit_logs') }}">Clear</a>
        @endif
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Created By</th>
                <th>Audit Action</th>
                <th>Target</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $audit_log)
            <tr>
                <td>{{ $audit_log->audit_log_id }}</td>
                <td>{{ $audit_log->created_by }}</td>
                <td>{{ $audit_log->audit_action }}</td>
                <td>{{ $audit_log->target }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $data->links('pagination::bootstrap-5') }}

    <br>
    <a href="{{ route('welcome') }}">Return</a>
</body>

</html>
