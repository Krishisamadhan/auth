<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Activity</title>
</head>
<body>
    <h1>Create Activity</h1>

    <form action="{{ route('activity.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <label>Activity Title</label>
        <input type="text" name="activity_title" value="{{ old('activity_title') }}">
        @error('activity_title')
            <p>{{ $message }}</p>
        @enderror

        <br><br>

        <label>Activity Date</label>
        <input type="date" name="activity_date" value="{{ old('activity_date') }}">
        @error('activity_date')
            <p>{{ $message }}</p>
        @enderror

        <br><br>

        <label>Location</label>
        <input type="text" name="location" value="{{ old('location') }}">
        @error('location')
            <p>{{ $message }}</p>
        @enderror

        <br><br>

        <label>Activity Image</label>
        <input type="file" name="image">
        @error('image')
            <p>{{ $message }}</p>
        @enderror

        <br><br>

        <button type="submit">Create Activity</button>
    </form>

    <br>

    <a href="{{ route('activity.index') }}">Back to Activities</a>
</body>
</html>
