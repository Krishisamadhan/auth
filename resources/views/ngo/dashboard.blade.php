<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NGO Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50">
  <nav class="border-b bg-white">
    <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
        <h1 class="font-bold">NGO Dashboard</h1>

        <form method="POST" action="{{ url('/logout') }}">
            @csrf
            <button class="text-sm text-red-600">Logout</button>
        </form>
    </div>
</nav>

<main class="mx-auto max-w-6xl px-6 py-10">

    <h2 class="text-2xl font-bold">
        Welcome, {{ auth()->user()->name }}
    </h2>

    <p class="mt-2 text-gray-600">
        You are logged in as NGO.
    </p>

    <div class="mt-8 flex gap-4">

        <a href="{{ route('activity.index') }}"
           class="rounded-lg bg-blue-600 px-5 py-3 text-white hover:bg-blue-700">
            View Activities
        </a>

        <a href="{{ route('activity.create') }}"
           class="rounded-lg bg-green-600 px-5 py-3 text-white hover:bg-green-700">
            Create Activity
        </a>

         <a href="{{ route('profile.create') }}"
           class="rounded-lg bg-red-600 px-5 py-3 text-white hover:bg-red-700">
            Profile
        </a>

    </div>

</main>
</body>
</html>