<!DOCTYPE html>
<html>

<head>
    <title>Chemsphere | Inventory</title>
</head>

<body>
    <h1>Inventory</h1>

    <table>
        <thead>
            <tr>
                <th>Chemical ID</th>
                <th>Chemical Name</th>
                <th>Hazard Color</th>
                <th>Amount</th>
                <th>Opened</th>
                <th>Expiration Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $chemical)
                <tr>
                    <td>{{ $chemical->chemical_id }}</td>
                    <td>{{ $chemical->chemical_name }}</td>
                    <td>{{ $chemical->hazard_color }}</td>
                    <td>{{ $chemical->amount }}</td>
                    <td>{{ $chemical->opened }}</td>
                    <td>{{ $chemical->expiration_date }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
