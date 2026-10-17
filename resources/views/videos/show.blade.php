<!doctype html>
<html>
<head><meta charset="utf-8"><title>{{ $video->title }}</title></head>
<body style="font-family:sans-serif;max-width:900px;margin:30px auto">
    <h2>{{ $video->title }}</h2>
    <video controls preload="metadata" poster="{{ $video->thumb_url }}" style="width:100%">
        <source src="{{ $video->url }}" type="video/mp4">
    </video>
    <p><a href="{{ route('videos.index') }}">Back</a></p>
</body>
</html>
