<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    <h1>Activity Details</h1>

<h2>{{ $activity->activity_title }}</h2>

<p>
    <strong>Date:</strong>
    {{ $activity->activity_date }}
</p>

<p>
    <strong>Location:</strong>
    {{ $activity->location }}
</p>

<p>
    <strong>User ID:</strong>
    {{ $activity->user_id }}
</p>

@if ($activity->image_url)
    <img src="{{ $activity->image_url }}" alt="{{ $activity->activity_title }}" width="300">
@endif

<br><br>

<a href="{{ route('activity.index') }}">Back to Activities</a>
</body>
</html>