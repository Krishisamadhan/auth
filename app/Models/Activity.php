<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['activity_title','user_id', 'activity_date','location', 'image_url'])]

class Activity extends Model
{
    //

        public function activity_user()
        {
            return $this->belongsTo(User::class, 'user_id');
        }

}
