<!DOCTYPE html>
<html>

<head>
    <title>Chemsphere | Equipment</title>
</head>

<body>
    <h1>Use Equipment</h1>

    <div>ID: {{ old('equipment_id', $equipment->equipment_id) }}</div>
    <div>Location ID: {{ old('location_id', $equipment->location_id) }}</div>
    <div>Name : {{ old('equipment_name', $equipment->equipment_name) }}</div>
    <div>Model: {{ old('model', $equipment->model) }}</div>
    <div>Serial ID: {{ old('serial_id', $equipment->serial_id) }}</div>
    <div>Status: {{ old('status', $equipment->status) }}</div>
    <div>Quantity: {{ old('quantity', $equipment->quantity) }}</div>
    <div>Purchase Date: {{ old('purchase_date', $equipment->purchase_date) }}</div>
    <div>Warranty Expiration: {{ old('warranty_expiration', $equipment->warranty_expiration) }}</div>
    <div>Last Maintenance: {{ old('last_maintenance', $equipment->last_maintenance) }}</div>
    <div>Next Maintenance: {{ old('next_maintenance', $equipment->next_maintenance) }}</div>

    <form action="{{ route('equipment.use.update', $equipment->equipment_id) }}" method="POST">
        @csrf
        @method('PUT')
        <br>
        <label for="notes">Additional Notes</label>
        <br>
        <textarea id="notes" name="notes" rows="5" cols="40" placeholder="Enter text here."></textarea>
        <br>

        @error('description')
            <div>{{ $message }}</div>
        @enderror

        <button type="submit">Update</button>
    </form>

    <br>
    <a href="{{ route('equipment') }}">Cancel</a>
</body>

</html>
