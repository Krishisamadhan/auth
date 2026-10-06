<?php

namespace App\Http\Controllers;

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


    public function store(Request $request)
    {
        $validated = $request->validate([
            'state' => 'required', 
            'district' => 'required',
            'tehsil' => 'required',
            'village' => 'required',
            'panchayat' => 'required',
            'pincode' => 'required|digits:6',

            'mobile_no' => 'required|integer',
            'address' => 'required',
         'operation_level' => 'nullable|string',

            'image' => 'required|image|mimes:jpg, jpeg,png,webp|max:2048'

        ]);

        $validated['user_id'] = auth()->id();

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
