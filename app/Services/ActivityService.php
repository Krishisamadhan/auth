<?php

namespace App\Services;

use App\Models\Activity;

class ActivityService
{
    public function create(array $data): Activity
    {
        $imagePath = $data['image']->store('activities', 'public');

        $data['image_url'] = $imagePath;

        unset($data['image']);

        return Activity::create($data);

  
    }
}
