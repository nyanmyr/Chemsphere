<!DOCTYPE html>
<html>

<head>
    <title>Chemsphere | Locations</title>
</head>

<body>
    <h1>Create Location</h1>

    <form action="{{ route('locations.store') }}" method="POST">
        @csrf
        <label for="location_name">Name</label>
        <br>
        <input type="text" id="location_name" name="location_name" required>

        @error('location_name')
        <div>{{ $message }}</div>
        @enderror

        <br>
        <label for="description">Description</label>
        <br>
        <textarea id="description" name="description" rows="5" cols="40">Enter text here.</textarea>
        <br>
        <button>Create</button>

        @error('description')
        <div>{{ $message }}</div>
        @enderror
    </form>

    <br>
    <a href="{{ route('locations') }}">Cancel</a>
</body>

</html>
