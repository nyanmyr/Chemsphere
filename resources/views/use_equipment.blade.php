<!DOCTYPE html>
<html>

<head>
    <title>Chemsphere | Equipment</title>
</head>

<body>
    <h1>Use Equipment</h1>

    <div>ID: {{ old('user_id', $equipment->equipment_id) }}</div>
    <div>Location ID: {{ old('email', $equipment->location_id) }}</div>
    <div>Name : {{ old('email', $equipment->equipment_name) }}</div>
    <div>Model: {{ old('email', $equipment->model) }}</div>
    <div>Serial ID: {{ old('email', $equipment->serial_id) }}</div>
    <div>Status: {{ old('email', $equipment->status) }}</div>
    <div>Quantity: {{ old('email', $equipment->quantity) }}</div>
    <div>Purchase Date: {{ old('email', $equipment->purchase_date) }}</div>
    <div>Warranty Expiration: {{ old('email', $equipment->warranty_expiration) }}</div>
    <div>Last Maintenance: {{ old('email', $equipment->last_maintenance) }}</div>
    <div>Next Maintenance: {{ old('email', $equipment->next_maintenance) }}</div>

    <form action="{{ route('equipment.use.update', $equipment->equipment_id) }}" method="POST">
        @csrf
        @method('PUT')


        <button type="submit">Update</button>
    </form>

    <br>
    <a href="{{ route('equipment') }}">Cancel</a>
</body>

</html>
