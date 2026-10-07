<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileRequest;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    //
public function index()
{


    $profile = Profile::where('user_id', auth()->id())->first();

    return view('profile.index', compact('profile'));
}


    public function show(Profile $profile)
    {
        if($profile->user_id != auth()->id())
            {
                return abort(403);
            }
            return view('profile.show', compact('profile'));
    }


    public function store(ProfileRequest $request)
    {
        $validated = $request->validated();

        $validated['user_id'] = auth()->id();
        $this->authorize('view', Profile::class);

        $imagePath = $request->file('image')->store('profile', 'public');




        $validated['profile_url'] = $imagePath;
         unset($validated['image']);

        Profile::create($validated);

        return redirect()->route('profile.index')->with('success' , 'profile updated successfully');

       

    }

    public function create()
    {
        return view('profile.create');
    }

}
