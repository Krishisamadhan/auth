
<!DOCTYPE html>
<html>
<head>
    <title>Projects </title>
    <style>
*{box-sizing:border-box}
body{font-family:Arial,sans-serif;background:#f4f6f9;margin:0;padding:25px;color:#333}
h2{color:#222;margin-bottom:20px}
form{margin-bottom:15px}
input,textarea,select{width:100%;max-width:500px;padding:10px;margin-top:5px;border:1px solid #ccc;border-radius:5px;font-size:15px}
textarea{height:90px;resize:vertical}
button{background:#2563eb;color:white;border:none;padding:10px 18px;border-radius:5px;cursor:pointer}
button:hover{background:#1d4ed8}
a{display:inline-block;text-decoration:none;background:#16a34a;color:white;padding:8px 12px;border-radius:5px}
a:hover{background:#15803d}
table{width:100%;border-collapse:collapse;background:white;margin-top:20px}
th,td{border:1px solid #ddd;padding:12px;text-align:left}
th{background:#1e293b;color:white}
tr:nth-child(even){background:#f8fafc}
td form{display:inline-block;margin:5px 0}
td form button{background:#dc2626}
td form button:hover{background:#b91c1c}
p{color:#15803d}
hr{border:0;border-top:1px solid #ddd;margin:30px 0}
@media(max-width:768px){body{padding:12px}table{display:block;overflow-x:auto;white-space:nowrap}input,textarea,select{max-width:100%}}
</style>
</head>
<body>
    {{-- isset($project) check karta hai ki edit ke liye project mila hai ya nahi --}}
    {{-- Project mila toh Edit form, nahi mila toh Create form --}}
    <h2>{{ isset($project) ? 'Edit Project' : 'Create Project' }}</h2>

    {{-- Session se success message dikhana --}}
    @if(session('success'))
        <p>{{ session('success') }}</p>
    @endif

    {{-- Validation errors dikhana --}}
    @if($errors->any())
        @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    @endif

    {{-- isset($project) true toh update route, false toh store route --}}
    <form action="{{ isset($project) ? route('project.update',$project->id) : route('project.store') }}" method="POST">
        @csrf
        {{-- HTML form directly PUT request nahi bhej sakta, isliye Laravel method spoofing use karta hai --}}
        {{-- Edit mein PUT, Create mein POST --}}
        @if(isset($project))
            @method('PUT')
        @endif

        {{-- old() validation fail hone par purani value rakhta hai --}}
        <input type="text" name="project_title" placeholder="Project Title" value="{{ old('project_title',$project->project_title ?? '') }}" required>
        <br><br>
        <textarea name="project_description" placeholder="Project Description" required>{{ old('project_description',$project->project_description ?? '') }}</textarea>
        <br><br>
        <input type="text" name="project_location" placeholder="Project Location" value="{{ old('project_location',$project->project_location ?? '') }}" required>
        <br><br>
        <label>Start Date</label>
        <input type="date" name="started_date" value="{{ old('started_date',$project->started_date ?? '') }}" required>
        <br><br>
        <label>End Date</label>
        <input type="date" name="end_date" value="{{ old('end_date',$project->end_date ?? '') }}" required>
        <br><br>
        <textarea name="remarks" placeholder="Remarks">{{ old('remarks',$project->remarks ?? '') }}</textarea>
        <br><br>
        <select name="status">
            @foreach(['pending','ongoing','completed'] as $status)
                <option value="{{ $status }}" @selected(old('status',$project->status ?? 'pending')===$status)>{{ ucfirst($status) }}</option>
            @endforeach
        </select>
        <br><br>
        {{-- Button ka text bhi Create/Edit ke hisaab se badlega --}}
        <button type="submit">{{ isset($project) ? 'Update' : 'Save' }}</button>
        @if(isset($project))
            {{-- Edit cancel karke listing par wapas jaana --}}
            <a href="{{ route('project.index') }}">Cancel</a>
        @endif
    </form>

    <hr>
    <h2>All Projects</h2>
    <table border="1" cellpadding="8">
        <thead>
            <tr>
                <th>Title</th>
                <th>Description</th>
                <th>Location</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Status</th>
                <th>Remarks</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            {{-- Controller se aaye projects ko ek-ek karke dikhana --}}
            @forelse($projects as $item)
                <tr>
                    <td>{{ $item->project_title }}</td>
                    <td>{{ $item->project_description }}</td>
                    <td>{{ $item->project_location }}</td>
                    <td>{{ $item->started_date }}</td>
                    <td>{{ $item->end_date }}</td>
                    <td>{{ $item->status }}</td>
                    <td>{{ $item->remarks }}</td>
                    <td>
                        {{-- Edit click karne par project.edit route chalega --}}
                        <a href="{{ route('project.edit',$item->id) }}">Edit</a>
                        {{-- Delete ke liye form zaroori hai, kyunki DELETE method chahiye --}}
                        <form action="{{ route('project.destroy',$item->id) }}" method="POST" onsubmit="return confirm('Delete this project?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            @empty
                {{-- Koi project nahi hai toh ye message --}}
                <tr>
                    <td colspan="8">No projects found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
