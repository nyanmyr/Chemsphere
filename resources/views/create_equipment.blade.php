<!DOCTYPE html>
<html>

<head>
    <title>Chemsphere | Equipment</title>
</head>

<body>
    <h1>Create Equipment</h1>

    <form action="{{ route('equipment.store') }}" method="POST">
        @csrf
        <label for="location_id">Location ID</label>
        <br>
        <input type="number" id="location_id" name="location_id" required>

        @error('location_id')
            <div>{{ $message }}</div>
        @enderror

        <br>
        <label for="equipment_name">Name</label>
        <br>
        <input type="text" id="equipment_name" name="equipment_name" required>

        @error('equipment_name')
            <div>{{ $message }}</div>
        @enderror

        <br>
        <label for="model">Model</label>
        <br>
        <input type="text" id="model" name="model" required>

        @error('model')
            <div>{{ $message }}</div>
        @enderror

        <br>
        <label for="serial_id">Serial ID</label>
        <br>
        <input type="text" id="serial_id" name="serial_id" required>

        @error('serial_id')
            <div>{{ $message }}</div>
        @enderror

        <br>
        <label for="status">Status</label>
        <br>
        <select id="status" name="status">
            @foreach (\App\EquipmentStatus::cases() as $class)
                <option value="{{ $class->value }}">{{ $class->value }}</option>
            @endforeach
        </select>

        @error('status')
            <div>{{ $message }}</div>
        @enderror

        <br>
        <label for="quantity">Quantity</label>
        <br>
        <input type="number" id="quantity" name="quantity" step="0.001" min="0" max="9999999999" required>

        @error('quantity')
            <div>{{ $message }}</div>
        @enderror

        <br>
        <label for="purchase_date">Purchase Date</label>
        <br>
        <input type="date" id="purchase_date" name="purchase_date" required>

        @error('purchase_date')
            <div>{{ $message }}</div>
        @enderror

        <br>
        <label for="warranty_expiration">Warranty Expiration</label>
        <br>
        <input type="date" id="warranty_expiration" name="warranty_expiration" required>

        @error('warranty_expiration')
            <div>{{ $message }}</div>
        @enderror

        <br>
        <label for="last_maintenance">Last Maintenance</label>
        <br>
        <input type="date" id="last_maintenance" name="last_maintenance" required>

        @error('last_maintenance')
            <div>{{ $message }}</div>
        @enderror

        <br>
        <label for="next_maintenance">Next Maintenance</label>
        <br>
        <input type="date" id="next_maintenance" name="next_maintenance" required>

        @error('next_maintenance')
            <div>{{ $message }}</div>
        @enderror

        <br>
        <button>Create</button>
    </form>

    <br>
    <a href="{{ route('equipment') }}">Cancel</a>
</body>

</html>
