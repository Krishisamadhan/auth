<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Illuminate\Http\Request;

class ActivityController extends Controller
{
    public function index()
    {
        // Sirf logged-in user ki activities nikal rahe hain
        $activities = Activity::where('user_id', auth()->id())->get();

        return view('activity.index', compact('activities'));
    }


    public function show(Activity $activity)
    {
        // Check kar rahe hain ki activity current logged-in user ki hai ya nahi
        if ($activity->user_id !== auth()->id()) {
            return view('activity.not-found');
        }

        return view('activity.show', compact('activity'));
    }


    public function store(Request $request)
    {
        // Form se aane wale data ko validate kar rahe hain
        $validated = $request->validate([
            'activity_title' => 'required|string',
            'activity_date' => 'required|date',
            'location' => 'required|string',

            // Actual uploaded image file
            // image = image honi chahiye
            // mimes = allowed extensions
            // max:2048 = maximum 2MB
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);


      
        $validated['user_id'] = auth()->id();


      

        $imagePath = $request->file('image')->store('activities', 'public');


        // Database mein image ka path save karenge
        $validated['image_url'] = $imagePath;


        // 'image' database column nahi hai,
        // isliye create karne se pehle array se hata rahe hain
        unset($validated['image']);


        // Activity database mein save
        Activity::create($validated);


        // Activity list par redirect
        return redirect()
            ->route('activity.index')
            ->with('success', 'Activity is created successfully');
    }


    public function create()
    {
        return view('activity.create');
    }
}