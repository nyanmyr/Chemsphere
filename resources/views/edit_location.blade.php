<!DOCTYPE html>
<html>

<head>
    <title>Chemsphere | Locations</title>
</head>

<body>
    <h1>Edit Location</h1>

    <form action="{{ route('locations.update', $location->location_id) }}" method="POST">
        @csrf
        @method('PUT')
        <label for="location_name">Name</label>
        <br>
        <input type="text" id="location_name" name="location_name" value="{{
            old('location_name', $location->location_name)
        }}" required>
        <br>
        <label for="description">Description</label>
        <br>
        <textarea id="description" name="description" rows="5" cols="40">{{old('location_name', $location->description)}}</textarea>
        <br>
        <button type="submit">Update</button>
    </form>

    <br>
    <a href="{{ route('locations') }}">Cancel</a>
</body>

</html>
