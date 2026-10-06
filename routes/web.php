<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProfileController;
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
