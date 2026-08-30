<!DOCTYPE html>
<html>

<head>
    <title>Chemsphere | Equipment</title>
</head>

<body>
    <h1>Edit Equipment</h1>

    <form action="{{ route('equipment.update', $equipment->equipment_id) }}" method="POST">
        @csrf
        @method('PUT')
        <label for="location_id">Location ID</label>
        <br>
        <input type="number" id="location_id" name="location_id" value="{{ old('location_id', $equipment->location_id) }}"
            required>

        @error('location_id')
            <div>{{ $message }}</div>
        @enderror

        <br>
        <label for="equipment_name">Name</label>
        <br>
        <input type="text" id="equipment_name" name="equipment_name"
            value="{{ old('equipment_name', $equipment->equipment_name) }}" required>

        @error('equipment_name')
            <div>{{ $message }}</div>
        @enderror

        <br>
        <label for="model">Model</label>
        <br>
        <input type="text" id="model" name="model" value="{{ old('equipment_name', $equipment->model) }}"
            required>

        @error('model')
            <div>{{ $message }}</div>
        @enderror

        <br>
        <label for="serial_id">Serial ID</label>
        <br>
        <input type="text" id="serial_id" name="serial_id" value="{{ old('serial_id', $equipment->serial_id) }}"
            required>

        @error('serial_id')
            <div>{{ $message }}</div>
        @enderror

        <br>
        <label for="status">Status</label>
        <br>
        <select id="status" name="status">
            @foreach (\App\EquipmentStatus::cases() as $class)
                <option value="{{ $class->value }}" @selected(old('status', $equipment->status->value ?? $equipment->status) == $class->value)>{{ $class->value }}</option>
            @endforeach
        </select>

        @error('status')
            <div>{{ $message }}</div>
        @enderror

        <br>
        <label for="quantity">Quantity</label>
        <br>
        <input type="number" id="quantity" name="quantity" step="0.001" min="0" max="9999999999"
            value="{{ old('quantity', $equipment->quantity) }}" required>

        @error('quantity')
            <div>{{ $message }}</div>
        @enderror

        <br>
        <label for="purchase_date">Purchase Date</label>
        <br>
        <input type="date" id="purchase_date" name="purchase_date"
            value="{{ old('purchase_date', $equipment->purchase_date) }}" required>

        @error('purchase_date')
            <div>{{ $message }}</div>
        @enderror

        <br>
        <label for="warranty_expiration">Warranty Expiration</label>
        <br>
        <input type="date" id="warranty_expiration" name="warranty_expiration"
            value="{{ old('warranty_expiration', $equipment->warranty_expiration) }}" required>

        @error('warranty_expiration')
            <div>{{ $message }}</div>
        @enderror

        <br>
        <label for="last_maintenance">Last Maintenance</label>
        <br>
        <input type="date" id="last_maintenance" name="last_maintenance"
            value="{{ old('last_maintenance', $equipment->last_maintenance) }}" required>

        @error('last_maintenance')
            <div>{{ $message }}</div>
        @enderror

        <br>
        <label for="next_maintenance">Next Maintenance</label>
        <br>
        <input type="date" id="next_maintenance" name="next_maintenance"
            value="{{ old('next_maintenance', $equipment->next_maintenance) }}" required>

        @error('next_maintenance')
            <div>{{ $message }}</div>
        @enderror

        <br>
        <button>Update</button>
    </form>

    <br>
    <a href="{{ route('equipment') }}">Cancel</a>
</body>

</html>
