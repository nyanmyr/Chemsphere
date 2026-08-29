<!DOCTYPE html>
<html>

<head>
    <title>Chemsphere | Inventory</title>
</head>

<body>
    <h1>Create Chemical</h1>

    <form action="{{ route('inventory.store') }}" method="POST">
        @csrf
        <label for="location_id">Location ID</label>
        <br>
        <input type="number" id="location_id" name="location_id" required>

        @error('location_id')
        <div>{{ $message }}</div>
        @enderror

        <br>
        <label for="chemical_name">Name</label>
        <br>
        <input type="text" id="chemical_name" name="chemical_name" required>

        @error('chemical_name')
        <div>{{ $message }}</div>
        @enderror

        <br>
        <label for="batch_number">Batch Number</label>
        <br>
        <input type="text" id="batch_number" name="batch_number" required>

        @error('batch_number')
        <div>{{ $message }}</div>
        @enderror

        <br>
        <label for="brand_name">Brand Name</label>
        <br>
        <input type="text" id="brand_name" name="brand_name" required>

        @error('brand_name')
        <div>{{ $message }}</div>
        @enderror

        <br>
        <label for="volume_per_unit">Volume Per Unit</label>
        <br>
        <input type="number" id="volume_per_unit" name="volume_per_unit" step="0.001" min="0" max="9999999999" required>

        @error('volume_per_unit')
        <div>{{ $message }}</div>
        @enderror

        <br>
        <label for="initial_quantity">Initial Quantity</label>
        <br>
        <input type="number" id="initial_quantity" name="initial_quantity" step="0.001" min="0" max="9999999999" required>

        @error('initial_quantity')
        <div>{{ $message }}</div>
        @enderror

        <br>
        <label for="current_quantity">Current Quantity</label>
        <br>
        <input type="number" id="current_quantity" name="current_quantity" step="0.001" min="0" max="9999999999" required>

        @error('current_quantity')
        <div>{{ $message }}</div>
        @enderror

        <br>
        <label for="expiration_date">Expiration Date</label>
        <br>
        <input type="date" id="expiration_date" name="expiration_date" required>

        @error('expiration_date')
        <div>{{ $message }}</div>
        @enderror

        <br>
        <label for="arrival_date">Arrival Date</label>
        <br>
        <input type="date" id="arrival_date" name="arrival_date" required>

        @error('arrival_date')
        <div>{{ $message }}</div>
        @enderror

        <br>
        <label>Safety Classes</label>
        <br>
        @foreach(\App\SafetyClass::cases() as $class)
            <label>
                <input
                    type="checkbox"
                    name="safety_classes[]"
                    value="{{ $class->value }}"
                    @checked(in_array($class->value, old('safety_classes', [])))
                >
                {{ $class->value }}
            </label><br>
        @endforeach

        @error('safety_classes')
            <div>{{ $message }}</div>
        @enderror
        @error('safety_classes.*')
            <div>{{ $message }}</div>
        @enderror

        <label>GHS Symbols</label>
        <br>
        @foreach(\App\GHSSymbol::cases() as $class)
            <label>
                <input
                    type="checkbox"
                    name="ghs_symbols[]"
                    value="{{ $class->value }}"
                    @checked(in_array($class->value, old('ghs_symbols', [])))
                >
                {{ $class->value }}
            </label><br>
        @endforeach

        @error('ghs_symbols')
            <div>{{ $message }}</div>
        @enderror
        @error('ghs_symbols.*')
            <div>{{ $message }}</div>
        @enderror

        <label for="unit">Unit</label>
        <br>
        <select id="unit" name="unit">
            @foreach(\App\Unit::cases() as $class)
                <option value="{{ $class->value }}">{{ $class->value }}</option>
            @endforeach
        </select>

        @error('unit')
            <div>{{ $message }}</div>
        @enderror

        <br>
        <button>Create</button>
    </form>

    <br>
    <a href="{{ route('inventory') }}">Cancel</a>
</body>

</html>
