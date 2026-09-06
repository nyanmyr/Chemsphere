<!DOCTYPE html>
<html>

<head>
    <title>Chemsphere | Inventory</title>
</head>

<body>
    <h1>Use Chemical</h1>

    <div>ID: {{ old('chemical_id', $chemical->chemical_id) }}</div>
    <div>Location ID: {{ old('location_id', $chemical->location_id) }}</div>
    <div>Created By: {{ old('created_by', $chemical->created_by) }}</div>
    <div>Name : {{ old('chemical_name', $chemical->chemical_name) }}</div>
    <div>Batch Number: {{ old('batch_number', $chemical->batch_number) }}</div>
    <div>Volume Per Unit: {{ old('volume_per_unit', $chemical->volume_per_unit) }}</div>
    <div>Initial Quantity: {{ old('initial_quantity', $chemical->initial_quantity) }}</div>
    <div>Current Quantity: {{ old('current_quantity', $chemical->current_quantity) }}</div>
    <div>Expiration Date: {{ old('expiration_date', $chemical->expiration_date) }}</div>
    <div>Arrival Date: {{ old('arrival_date', $chemical->arrival_date) }}</div>
    <div>Safety Classes: {{ old('safety_classes', $chemical->safety_classes) }}</div>
    <div>GHS Symbols: {{ old('ghs_symbols', $chemical->ghs_symbols) }}</div>
    <div>Unit: {{ old('unit', $chemical->unit) }}</div>

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

        <br>
        <label for="notes">Additional Notes</label>
        <br>
        <textarea id="notes" name="notes" rows="5" cols="40" placeholder="Enter text here."></textarea>

        @error('description')
            <div>{{ $message }}</div>
        @enderror

        <br>
        <button type="submit">Update</button>
    </form>

    <br>
    <a href="{{ route('inventory') }}">Cancel</a>
</body>

</html>
