<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Profile</title>
</head>
<body>

    <h1>Create Profile</h1>

    @if ($errors->any())
        <div>
            <strong>Please fix the following errors:</strong>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('profile.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <label>State</label>
        <input type="text" name="state" value="{{ old('state') }}">
        @error('state')
            <p>{{ $message }}</p>
        @enderror
        <br>

        <label>District</label>
        <input type="text" name="district" value="{{ old('district') }}">
        @error('district')
            <p>{{ $message }}</p>
        @enderror
        <br>

        <label>Tehsil</label>
        <input type="text" name="tehsil" value="{{ old('tehsil') }}">
        @error('tehsil')
            <p>{{ $message }}</p>
        @enderror
        <br>

        <label>Village</label>
        <input type="text" name="village" value="{{ old('village') }}">
        @error('village')
            <p>{{ $message }}</p>
        @enderror
        <br>

        <label>Panchayat</label>
        <input type="text" name="panchayat" value="{{ old('panchayat') }}">
        @error('panchayat')
            <p>{{ $message }}</p>
        @enderror
        <br>

        <label>Pincode</label>
        <input type="text" name="pincode" value="{{ old('pincode') }}">
        @error('pincode')
            <p>{{ $message }}</p>
        @enderror
        <br>

        <label>Mobile No</label>
        <input type="text" name="mobile_no" value="{{ old('mobile_no') }}">
        @error('mobile_no')
            <p>{{ $message }}</p>
        @enderror
        <br>

        <label>Address</label>
        <textarea name="address">{{ old('address') }}</textarea>
        @error('address')
            <p>{{ $message }}</p>
        @enderror
        <br>

        <label>Operation Level</label>
        <input type="text" name="operation_level" value="{{ old('operation_level') }}">
        @error('operation_level')
            <p>{{ $message }}</p>
        @enderror
        <br>

        <label>Profile Image</label>
        <input type="file" name="image">
        @error('image')
            <p>{{ $message }}</p>
        @enderror
        <br>

        <button type="submit">Create Profile</button>

    </form>

</body>
</html>
