<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen items-center justify-center bg-gray-50 px-4">
    <div class="w-full max-w-md rounded-lg bg-white p-8 shadow-sm">
        <h1 class="text-2xl font-bold">Create Account</h1>
        <p class="mt-1 text-sm text-gray-500">Register as NGO or Inspector</p>

        @if ($errors->any())
            <div class="mt-4 rounded bg-red-50 p-3 text-sm text-red-600">
                <ul class="list-inside list-disc">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ url('/register') }}" class="mt-6 space-y-4">
            @csrf

            <div>
                <label class="mb-1 block text-sm font-medium">Name</label>
                <input
                    type="text"
                    name="name"
                    value="{{ old('name') }}"
                    required
                    class="w-full rounded border px-3 py-2 outline-none focus:border-black"
                >
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium">Email</label>
                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    class="w-full rounded border px-3 py-2 outline-none focus:border-black"
                >
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium">Password</label>
                <input
                    type="password"
                    name="password"
                    required
                    class="w-full rounded border px-3 py-2 outline-none focus:border-black"
                >
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium">Confirm Password</label>
                <input
                    type="password"
                    name="password_confirmation"
                    required
                    class="w-full rounded border px-3 py-2 outline-none focus:border-black"
                >
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium">Role</label>
                <select
                    name="role"
                    required
                    class="w-full rounded border px-3 py-2 outline-none focus:border-black"
                >
                    <option value="">Select Role</option>
                    <option value="ngo" @selected(old('role') === 'ngo')>NGO</option>
                    <option value="inspector" @selected(old('role') === 'inspector')>Inspector</option>
                </select>
            </div>

            <button type="submit" class="w-full rounded bg-black py-2.5 text-white">
                Register
            </button>
        </form>

        <p class="mt-5 text-center text-sm text-gray-600">
            Already have an account?
            <a href="{{ route('login') }}" class="font-medium text-black">Login</a>
        </p>
    </div>
</body>
</html>