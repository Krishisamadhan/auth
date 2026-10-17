<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VideoController;

use App\Http\Controllers\Api\VideoApiController;
use Illuminate\Support\Facades\Route;


// Home
Route::get('/', function () {

    if (!auth()->check()) {
        return view('welcome');
    }

    $role = auth()->user()->role;

    if ($role === 'ngo') {
        return redirect()->route('ngo.dashboard');
    }

    if ($role === 'inspector') {
        return redirect()->route('inspector.dashboard');
    }

    if ($role === 'admin') {
        return redirect()->route('admin.dashboard');
    }

    abort(403);

})->name('home');



// Login & Register
// Sirf logged-out users ke liye
Route::middleware('guest')->group(function () {

    Route::get('/login', [AuthController::class, 'showLogin'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/register', [AuthController::class, 'showRegister'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register']);
});


// Logout
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth');


// NGO Routes
Route::middleware(['auth', 'isNgo'])->group(function () {

    Route::get('/ngo/dashboard', function () {
        return view('ngo.dashboard');
    })->name('ngo.dashboard');


    // Activities
    Route::get('/activity', [ActivityController::class, 'index'])
        ->name('activity.index');

    Route::get('/activity/create', [ActivityController::class, 'create'])
        ->name('activity.create');

    Route::post('/activity', [ActivityController::class, 'store'])
        ->name('activity.store');

    Route::get('/activity/{activity}', [ActivityController::class, 'show'])
        ->name('activity.show');
});


// Inspector Routes
Route::middleware(['auth', 'IsInspector'])->group(function () {

    Route::get('/inspector/dashboard', function () {
        return view('inspector.dashboard');
    })->name('inspector.dashboard');

});


// Admin Routes
Route::middleware(['auth', 'isAdmin'])->group(function () {

    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

});




// profile Routes



Route::middleware('auth')->prefix('profile')->group(function () {

    Route::get('/', [ProfileController::class, 'index'])
        ->name('profile.index');

    Route::get('/create', [ProfileController::class, 'create'])
        ->name('profile.create');

    Route::post('/', [ProfileController::class, 'store'])
        ->name('profile.store');

    Route::get('/{profile}', [ProfileController::class, 'show'])
        ->name('profile.show');

});




Route::get('/phpinfo', function () {
    return response()->view('phpinfo');
});




Route::get('/videos', [VideoController::class, 'index'])->name('videos.index');
Route::post('/videos', [VideoController::class, 'store'])->name('videos.store');
Route::get('/videos/{video}', [VideoController::class, 'show'])->name('videos.show');
   Route::get('/videos/{video}/status', [VideoController::class, 'status'])->name('videos.status');
   Route::get('/video-upload/status', [VideoController::class, 'chunkStatus'])->name('videos.chunk.status');
Route::post('/video-upload/chunk', [VideoController::class, 'chunk'])->name('videos.chunk');
Route::post('/video-upload/complete', [VideoController::class, 'complete'])->name('videos.complete');

// external api 


Route::get('/api/videos', [VideoApiController::class, 'index']);
Route::get('/api/videos/{video}', [VideoApiController::class, 'show']);





use App\Http\Controllers\ProjectController;

Route::resource('project',ProjectController::class);