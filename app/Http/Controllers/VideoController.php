<?php

namespace App\Http\Controllers;

use App\Jobs\ProcessVideo;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VideoController extends Controller
{
    private const MAX_BYTES = 2147483648; // 2 GB

    public function index()
    {
        return view('videos.index', [
            'videos' => Video::latest()->get(),
        ]);
    }

    public function status(Video $video)
    {
        return response()->json($video->only(['id', 'status', 'progress', 'error']));
    }

    public function show(Video $video)
    {
        abort_unless($video->status === 'ready', 404);

        return view('videos.show', compact('video'));
    }

    // ---------- Chunked upload ----------

    // Kaun se chunks server par aa chuke hain
    public function chunkStatus(Request $request)
    {
        $id = $this->uploadId($request->query('id'));

        $received = collect(Storage::disk('local')->files("chunks/{$id}"))
            ->map(fn ($f) => (int) basename($f))
            ->sort()->values();

        return response()->json(['received' => $received]);
    }

    // Ek chunk save
    public function chunk(Request $request)
    {
        $request->validate([
            'id'    => 'required|string',
            'index' => 'required|integer|min:0|max:5000',
            'chunk' => 'required|file|max:10240', // 10 MB
        ]);

        $id = $this->uploadId($request->input('id'));

        $request->file('chunk')->storeAs("chunks/{$id}", (string) $request->integer('index'), 'local');

        return response()->json(['ok' => true]);
    }

    // Sab chunks aa gaye: jodo, validate karo, job dispatch karo
    public function complete(Request $request)
    {
        $data = $request->validate([
            'id'    => 'required|string',
            'total' => 'required|integer|min:1|max:5000',
            'title' => 'required|string|max:255',
        ]);

        $id    = $this->uploadId($data['id']);
        $disk  = Storage::disk('local');
        $total = (int) $data['total'];

        // Sab chunks maujood hain?
        for ($i = 0; $i < $total; $i++) {
            if (! $disk->exists("chunks/{$id}/{$i}")) {
                return response()->json(['message' => "Chunk {$i} missing"], 422);
            }
        }

        // Merge (stream, RAM nahi khata)
        $disk->makeDirectory('videos/original');
        $rel = 'videos/original/' . Str::uuid() . '.mp4';
        $out = fopen($disk->path($rel), 'wb');

        for ($i = 0; $i < $total; $i++) {
            $in = fopen($disk->path("chunks/{$id}/{$i}"), 'rb');
            stream_copy_to_stream($in, $out);
            fclose($in);
        }
        fclose($out);

        // Validate: size + asli file type
        $abs  = $disk->path($rel);
        $mime = (new \finfo(FILEINFO_MIME_TYPE))->file($abs);

        if (filesize($abs) > self::MAX_BYTES || $mime !== 'video/mp4') {
            $disk->delete($rel);
            $disk->deleteDirectory("chunks/{$id}");

            return response()->json(['message' => 'Sirf MP4, max 2 GB'], 422);
        }

        $disk->deleteDirectory("chunks/{$id}");

        $video = Video::create([
            'title'         => $data['title'],
            'original_path' => $rel,
        ]);

        ProcessVideo::dispatch($video->id);

        return response()->json(['ok' => true, 'id' => $video->id]);
    }

    // Path traversal se bachne ke liye strict id
    private function uploadId(?string $id): string
    {
        abort_unless($id && preg_match('/^[A-Za-z0-9]{16,64}$/', $id), 422, 'Invalid upload id');

        return $id;
    }
}