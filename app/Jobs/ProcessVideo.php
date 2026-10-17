<?php

namespace App\Jobs;

use App\Models\Video;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class ProcessVideo implements ShouldQueue
{
    use Queueable;

    public int $timeout = 7200;
    public int $tries   = 1;

    public function __construct(public int $videoId) {}

    public function handle(): void
    {
        $video = Video::findOrFail($this->videoId);
        $video->update(['status' => 'processing']);

        $ffmpeg = config('services.ffmpeg.bin');
        $probe  = config('services.ffmpeg.probe');

        $input = Storage::disk('local')->path($video->original_path);

        Storage::disk('public')->makeDirectory('videos/processed');
        Storage::disk('public')->makeDirectory('videos/thumbs');

        $outRel   = "videos/processed/{$video->id}.mp4";
        $thumbRel = "videos/thumbs/{$video->id}.jpg";
        $output   = Storage::disk('public')->path($outRel);
        $thumb    = Storage::disk('public')->path($thumbRel);

        // 1. Duration
        $r = Process::run([$probe, '-v', 'error', '-show_entries', 'format=duration', '-of', 'csv=p=0', $input]);
        $duration = (int) round((float) trim($r->output()));

        // 2. 720p MP4
        $r = Process::timeout(7200)->run([
            $ffmpeg, '-y', '-i', $input,
            '-vf', 'scale=-2:720',
            '-c:v', 'libx264', '-crf', '24', '-preset', 'veryfast',
            '-c:a', 'aac', '-b:a', '128k',
            '-movflags', '+faststart',
            $output,
        ]);
        if ($r->failed()) {
            throw new \RuntimeException($r->errorOutput());
        }

        // 3. Thumbnail
        Process::timeout(120)->run([$ffmpeg, '-y', '-ss', '5', '-i', $input, '-frames:v', '1', $thumb]);

        $video->update([
            'processed_path'   => $outRel,
            'thumbnail_path'   => $thumbRel,
            'duration_seconds' => $duration,
            'status'           => 'ready',
        ]);
    }

    public function failed(\Throwable $e): void
    {
        Video::where('id', $this->videoId)->update([
            'status' => 'failed',
            'error'  => mb_substr($e->getMessage(), 0, 2000),
        ]);
    }
}
