<!DOCTYPE html>
<html>

<head>
    <title>Chemsphere | Audit Logs</title>
</head>

<body>
    <h1>Audit Logs</h1>

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

    <br>
    <a href="{{ route('welcome') }}">Return</a>
</body>

</html>
