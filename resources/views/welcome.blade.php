<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50">
    <nav class="border-b bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-4">
            <h1 class="text-xl font-bold">My Portal</h1>

            <div class="flex gap-4">
                <a href="{{ route('login') }}" class="text-gray-600 hover:text-black">Login</a>
                <a href="{{ route('register') }}" class="rounded bg-black px-4 py-2 text-white">Register</a>
            </div>
        </div>
    </nav>

    <main class="mx-auto max-w-6xl px-6 py-20">
        <div class="max-w-2xl">
            <h2 class="text-4xl font-bold text-gray-900">
                Welcome to the Portal
            </h2>

            <p class="mt-4 text-gray-600">
                Manage your activities and access your dashboard from one place.
            </p>

            <div class="mt-6 flex gap-3">
                <a href="{{ route('login') }}" class="rounded bg-black px-5 py-2.5 text-white">
                    Login
                </a>

                <a href="{{ route('register') }}" class="rounded border px-5 py-2.5">
                    Create Account
                </a>
            </div>
        </div>
    </main>
</body>
</html>