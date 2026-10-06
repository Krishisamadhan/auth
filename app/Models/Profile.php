<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['user_id', 'profile_url','state', 'district', 'tehsil', 'village', 'panchayat', 'pincode', 'mobile_no', 'address', 'operational_level'])]

class Profile extends Model
{
    //
    public function profile_user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
