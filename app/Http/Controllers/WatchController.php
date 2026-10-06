<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class WatchController extends Controller
{
    //



    public function index()
{
    $activities = Activity::where('user_id', auth()->id())->get();

    return view('activity.index', compact('activities'));
}





//     public function show(Activity $activity)
// {
//     if ($activity->user_id !== auth()->id()) {
//         abort(403);
//     }

//     return view('activity.show', compact('activity'));
// }



public function show(Activity $activity)
{
    if ($activity->user_id !== auth()->id()) {
        return view('activity.not-found');
    }

    return view('activity.show', compact('activity'));
}


 



    public function store(Request $request)
{
    $validated = $request->validate([
        'activity_title' => 'required|string',
        'activity_date' => 'required|date',
        'location' => 'required|string',
        'image_url' => 'required|string',
    ]);

    $validated['user_id'] = auth()->id();

    Activity::create($validated);

    return redirect()
        ->route('activity.index')
        ->with('success', 'Activity is created successfully');
}




    public function create()
    {
        return view('activity.create');
    }

}
