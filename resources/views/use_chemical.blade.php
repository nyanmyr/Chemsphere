<!DOCTYPE html>
<html>

<head>
    <title>Chemsphere | Inventory</title>
</head>

<body>
    <h1>Use Chemical</h1>

    <div>ID: {{ old('user_id', $chemical->chemical_id) }}</div>
    <div>Location ID: {{ old('email', $chemical->location_id) }}</div>
    <div>Created By: {{ old('email', $chemical->created_by) }}</div>
    <div>Name : {{ old('email', $chemical->chemical_name) }}</div>
    <div>Batch Number: {{ old('email', $chemical->batch_number) }}</div>
    <div>Volume Per Unit: {{ old('email', $chemical->volume_per_unit) }}</div>
    <div>Initial Quantity: {{ old('email', $chemical->initial_quantity) }}</div>
    <div>Current Quantity: {{ old('email', $chemical->current_quantity) }}</div>
    <div>Expiration Date: {{ old('email', $chemical->expiration_date) }}</div>
    <div>Arrival Date: {{ old('email', $chemical->arrival_date) }}</div>
    <div>Safety Classes: {{ old('email', $chemical->safety_classes) }}</div>
    <div>GHS Symbols: {{ old('email', $chemical->ghs_symbols) }}</div>
    <div>Unit: {{ old('email', $chemical->unit) }}</div>

    <form action="{{ route('inventory.use.update', $chemical->chemical_id) }}" method="POST">
        @csrf
        @method('PUT')
        <br>
        <label for="use_amount">Use Amount</label>
        <br>
        <input type="number" id="use_amount" name="use_amount" step="0.001" min="0"
            max="{{ old('current_quantity', $chemical->current_quantity) }}" value="0.000" required>

        @error('use_amount')
            <div>{{ $message }}</div>
        @enderror

        <button type="submit">Update</button>
    </form>

    <br>
    <a href="{{ route('inventory') }}">Cancel</a>
</body>

</html>
