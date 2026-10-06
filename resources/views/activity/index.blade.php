<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Activities</title>
    <style>
        .image_url{
            width:300px;
            height:200px;
            object-fit: cover;
        }
    </style>
</head>
<body>

    @foreach ($activities as $activity )

     <p>Date: {{ $activity->activity_date }}</p>
     
    <p>title: {{ $activity->activity_title }}</p>
   
 <a href="{{ route('activity.show', $activity->id) }}">
        <img
            src="{{ $activity->image_url }}"
            class="image_url"
            alt="{{ $activity->activity_title }}"
        >
    </a>
     <p>location: {{ $activity->location }}</p>
     <hr>
     
     
    
        
    @endforeach

    
</body>
</html>