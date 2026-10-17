<?php
namespace App\Http\Controllers;
use App\Models\Project;
use App\Http\Requests\ProjectRequest;
use Illuminate\Http\Request;
class ProjectController extends Controller
{
    public function index()
    {
        $projects=Project::latest()->get();
        return view('project.index',compact('projects'));
    }
    public function create()
    {
        return redirect()->route('project.index');
    }
    public function store(ProjectRequest $request)
    {
        Project::create($request->validated());
        return redirect()->route('project.index')->with('success','Project created successfully.');
    }
    public function show(Project $project)
    {
        return redirect()->route('project.index');
    }


    public function edit(Project $project)
{
    $projects=Project::latest()->get();
    return view('project.index',compact('projects','project'));
}
    public function update(ProjectRequest $request,Project $project)
    {
        $project->update($request->validated());
        return redirect()->route('project.index')->with('success','Project updated successfully.');
    }
    public function destroy(Project $project)
    {
        $project->delete();
        return redirect()->route('project.index')->with('success','Project deleted successfully.');
    }
}