<!DOCTYPE html>
<html>

<head>
    <title>Chemsphere | Equipment</title>
</head>

<body>
    <h1>Equipment</h1>

    <table>
        <thead>
            <tr>
                <th>Chemical ID</th>
                <th>Location ID</th>
                <th>Created By</th>
                <th>Name</th>
                <th>Model</th>
                <th>Serial ID</th>
                <th>Status</th>
                <th>Quantity</th>
                <th>Purchase Date</th>
                <th>Warranty Expiration</th>
                <th>Last Maintenance</th>
                <th>Next Maintenance</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $equipment)
            <tr>
                <td>{{ $equipment->equipment_id }}</td>
                <td>{{ $equipment->location_id }}</td>
                <td>{{ $equipment->created_by }}</td>
                <td>{{ $equipment->equipment_name }}</td>
                <td>{{ $equipment->model }}</td>
                <td>{{ $equipment->serial_id }}</td>
                <td>{{ $equipment->status }}</td>
                <td>{{ $equipment->quantity }}</td>
                <td>{{ $equipment->purchase_date }}</td>
                <td>{{ $equipment->warranty_expiration }}</td>
                <td>{{ $equipment->last_maintenance }}</td>
                <td>{{ $equipment->next_maintenance }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <br>
    <a href="{{ route('welcome') }}">Return</a>
</body>

</html>
