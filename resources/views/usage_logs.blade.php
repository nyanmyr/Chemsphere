<!DOCTYPE html>
<html>

<head>
    <title>Chemsphere | Usage Logs</title>
</head>

<body>
    <h1>Usage Logs</h1>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Created By</th>
                <th>Location ID</th>
                <th>Item Type</th>
                <th>Item ID</th>
                <th>Quantity Used</th>
                <th>Quantity Remaining</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $usage_log)
            <tr>
                <td>{{ $usage_log->usage_log_id }}</td>
                <td>{{ $usage_log->created_by }}</td>
                <td>{{ $usage_log->location_id }}</td>
                <td>{{ $usage_log->item_type }}</td>
                <td>{{ $usage_log->item_id }}</td>
                <td>{{ $usage_log->quantity_used }}</td>
                <td>{{ $usage_log->quantity_remaining }}</td>
                <td>{{ $usage_log->notes }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <br>
    <a href="{{ route('welcome') }}">Return</a>
</body>

</html>
