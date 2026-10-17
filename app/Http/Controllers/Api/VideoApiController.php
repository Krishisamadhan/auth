<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Video;

class VideoApiController extends Controller
{
    public function index()
    {
        $videos = Video::where('status', 'ready')->latest()->get()->map(fn ($v) => $this->format($v));

        return response()->json(['data' => $videos]);
    }

    public function show(Video $video)
    {
        abort_unless($video->status === 'ready', 404);

        return response()->json(['data' => $this->format($video)]);
    }

    private function format(Video $v): array
    {
        return [
            'id'       => $v->id,
            'title'    => $v->title,
            'duration' => $v->duration_seconds,
            'thumb'    => $v->thumb_url,
            'stream'   => $v->url,
        ];
    }
}