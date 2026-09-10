<!DOCTYPE html>
<html>

<head>
    <title>Chemsphere | Locations</title>
</head>

<body>
    <h1>Edit Location</h1>

    <div>ID: {{ old('location_id', $location->location_id) }}</div>
    <div>Created By: {{ old('created_by', $location->created_by) }}</div>
    <div>Created: {{ old('created_at', $location->created_at) }}</div>
    <div>Last Updated: {{ old('updated_at', $location->updated_at) }}</div>
    <br>

    <form action="{{ route('locations.update', $location->location_id) }}" method="POST">
        @csrf
        @method('PUT')
        <label for="location_name">Name</label>
        <br>
        <input type="text" id="location_name" name="location_name" value="{{ old('location_name', $location->location_name) }}" required>

        @error('location_name')
        <div>{{ $message }}</div>
        @enderror

        <br>
        <label for="description">Description</label>
        <br>
        <textarea id="description" name="description" rows="5" cols="40">{{ old('location_name', $location->description) }}</textarea>

        @error('description')
        <div>{{ $message }}</div>
        @enderror

        <br>
        <button type="submit">Update</button>
    </form>

    <br>
    <a href="{{ route('locations') }}">Cancel</a>
</body>

</html>
