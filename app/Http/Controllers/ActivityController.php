<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActivityRequest;
use App\Models\Activity;
use App\Services\ActivityService;
use Illuminate\Http\Request;

class ActivityController extends Controller
{

   public function __construct(private ActivityService $activityService) {

    }

    public function index()
    {
        // OLD:
        // Sirf logged-in user ki activities filter kar rahe the.
        // Ye authorization check nahi tha.
        //
        // $activities = Activity::where('user_id', auth()->id())->get();


        // NEW:
        // ActivityPolicy ke viewAny() ko check karega.
        // Matlab: kya current user Activity ki list dekh sakta hai?
        $this->authorize('viewAny', Activity::class);


        // Same old query:
        // Sirf logged-in user ki activities
        $activities = Activity::where('user_id', auth()->id())->get();

        return view('activity.index', compact('activities'));
    }


    public function show(Activity $activity)
    {
        // OLD:
        // Manually check kar rahe the ki activity current user ki hai ya nahi.
        //
        // if ($activity->user_id !== auth()->id()) {
        //     return view('activity.not-found');
        // }


        // NEW:
        // Ab ye check ActivityPolicy ke view() method mein hoga.
        // $activity = jis particular Activity ko check karna hai.
        $this->authorize('view', $activity);


        return view('activity.show', compact('activity'));
    }


    public function store(StoreActivityRequest $request)
    {
        // NEW:
        // ActivityPolicy ke create() method ko check karega.
        // Matlab: kya current user new Activity create kar sakta hai?
        $this->authorize('create', Activity::class);


        // Form se aane wale data ko validate kar rahe hain
        $validated = $request->validated();


        $validated['user_id'] = auth()->id();

         $this->activityService->create($validated);
      


        // Activity list par redirect
        return redirect()
            ->route('activity.index')
            ->with('success', 'Activity is created successfully');
    }


    public function create()
    {
        // NEW:
        // ActivityPolicy ke create() method ko check karega.
        // Matlab: kya current user Activity create karne ka form khol sakta hai?
        $this->authorize('create', Activity::class);


        return view('activity.create');
    }
}
