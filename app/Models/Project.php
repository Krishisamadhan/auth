<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['project_title', 'project_description', 'project_location','started_date', 'end_date', 'status', 'remarks'])]

class Project extends Model
{
    //
    use HasUlids;
}

