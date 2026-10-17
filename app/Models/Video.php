<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Video extends Model
{
    protected $fillable = [
        'title', 'original_path', 'processed_path',
        'thumbnail_path', 'duration_seconds',
        'status', 'progress', 'error',
    ];

    public function getUrlAttribute(): ?string
    {
        return $this->processed_path ? Storage::disk('public')->url($this->processed_path) : null;
    }

    public function getThumbUrlAttribute(): ?string
    {
        return $this->thumbnail_path ? Storage::disk('public')->url($this->thumbnail_path) : null;
    }
}