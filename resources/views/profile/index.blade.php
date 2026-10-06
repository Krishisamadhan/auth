<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>
</head>
<body>

    <h1>My Profile</h1>


    @if (session('success'))
    <p style="color: green;">
        {{ session('success') }}
    </p>
@endif


    @if ($profile)

        <p>State: {{ $profile->state }}</p>
        <p>District: {{ $profile->district }}</p>
        <p>Tehsil: {{ $profile->tehsil }}</p>
        <p>Village: {{ $profile->village }}</p>
        <p>Panchayat: {{ $profile->panchayat }}</p>
        <p>Pincode: {{ $profile->pincode }}</p>
        <p>Mobile: {{ $profile->mobile_no }}</p>
        <p>Address: {{ $profile->address }}</p>
        <p>Operation Level: {{ $profile->operation_level }}</p>

        @if ($profile->profile_url)
            <img
                src="{{ asset('storage/' . $profile->profile_url) }}"
                alt="Profile Image"
                width="200"
            >
        @endif

    @else

        <p>Profile not created yet.</p>

        <a href="{{ route('profile.create') }}">
            Create Profile
        </a>

    @endif

</body>
</html>
