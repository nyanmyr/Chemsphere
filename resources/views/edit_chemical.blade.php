<!DOCTYPE html>
<html>

<head>
    <title>Chemsphere | Inventory</title>
</head>

<body>
    <h1>Edit Inventory</h1>

    <form action="{{ route('inventory.update', $chemical->chemical_id) }}" method="POST">
        @csrf
        @method('PUT')
        <label for="location_id">Location ID</label>
        <br>
        <input type="number" id="location_id" name="location_id" value="{{
            old('location_id', $chemical->location_id)
        }}" required>

        @error('location_id')
        <div>{{ $message }}</div>
        @enderror

        <br>
        <label for="chemical_name">Name</label>
        <br>
        <input type="text" id="chemical_name" name="chemical_name" value="{{
            old('chemical_name', $chemical->chemical_name)
        }}" required>

        @error('chemical_name')
        <div>{{ $message }}</div>
        @enderror

        <br>
        <label for="batch_number">Batch Number</label>
        <br>
        <input type="text" id="batch_number" name="batch_number" value="{{
            old('batch_number', $chemical->batch_number)
        }}" required>

        @error('batch_number')
        <div>{{ $message }}</div>
        @enderror

        <br>
        <label for="brand_name">Brand Name</label>
        <br>
        <input type="text" id="brand_name" name="brand_name" value="{{
            old('brand_name', $chemical->brand_name)
        }}" required>

        @error('brand_name')
        <div>{{ $message }}</div>
        @enderror

        <br>
        <label for="volume_per_unit">Volume Per Unit</label>
        <br>
        <input type="number" id="volume_per_unit" name="volume_per_unit" step="0.001" min="0" max="9999999999" value="{{
            old('volume_per_unit', $chemical->volume_per_unit)
        }}" required>

        @error('volume_per_unit')
        <div>{{ $message }}</div>
        @enderror

        <br>
        <label for="initial_quantity">Initial Quantity</label>
        <br>
        <input type="number" id="initial_quantity" name="initial_quantity" step="0.001" min="0" max="9999999999" value="{{
            old('initial_quantity', $chemical->initial_quantity)
        }}" required>

        @error('initial_quantity')
        <div>{{ $message }}</div>
        @enderror

        <br>
        <label for="current_quantity">Current Quantity</label>
        <br>
        <input type="number" id="current_quantity" name="current_quantity" step="0.001" min="0" max="9999999999" value="{{
            old('current_quantity', $chemical->current_quantity)
        }}" required>

        @error('current_quantity')
        <div>{{ $message }}</div>
        @enderror

        <br>
        <label for="expiration_date">Expiration Date</label>
        <br>
        <input type="date" id="expiration_date" name="expiration_date" value="{{
            old('expiration_date', $chemical->expiration_date)
        }}" required>

        @error('expiration_date')
        <div>{{ $message }}</div>
        @enderror

        <br>
        <label for="arrival_date">Arrival Date</label>
        <br>
        <input type="date" id="arrival_date" name="arrival_date" value="{{
            old('arrival_date', $chemical->arrival_date)
        }}" required>

        @error('arrival_date')
        <div>{{ $message }}</div>
        @enderror

        @php
        $raw_safety_classes = $chemical->safety_classes;

        $cleaned_safety_classes = is_string($raw_safety_classes)
        ? explode(',', $raw_safety_classes)
        : ($raw_safety_classes ?? []);

        $selected_safety_classes = old(
        'safety_classes',
        collect($cleaned_safety_classes)
        ->map(fn($c) => is_object($c) ? $c->value : trim($c))
        ->all()
        );
        @endphp

        <br>
        <label>Safety Classes</label>
        <br>
        @foreach(\App\SafetyClass::cases() as $class)
        <label>
            <input type="checkbox" name="safety_classes[]" value="{{ $class->value }}" @checked(in_array($class->value, $selected_safety_classes))
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

        @php
        $raw_ghs_symbols = $chemical->ghs_symbols;

        $cleaned_ghs_symbols = is_string($raw_ghs_symbols)
        ? explode(',', $raw_ghs_symbols)
        : ($raw_ghs_symbols ?? []);

        $selected_ghs_symbols = old(
        'ghs_symbols',
        collect($cleaned_ghs_symbols)
        ->map(fn($c) => is_object($c) ? $c->value : trim($c))
        ->all()
        );
        @endphp

        <label>GHS Symbols</label><br>
        @foreach(\App\GHSSymbol::cases() as $symbol)
        <label>
            <input type="checkbox" name="ghs_symbols[]" value="{{ $symbol->value }}" @checked(in_array($symbol->value, $selected_ghs_symbols))
            >
            {{ $symbol->value }}
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
            <option value="{{ $class->value }}" @selected(old('unit', $chemical->unit->value ?? $chemical->unit) == $class->value)>{{ $class->value }}</option>
            @endforeach
        </select>

        @error('unit')
        <div>{{ $message }}</div>
        @enderror

        <button type="submit">Update</button>
    </form>

    <br>
    <a href="{{ route('inventory') }}">Cancel</a>
</body>

</html>
