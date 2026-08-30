<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AdminProjectController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Projects/Index', [
            'projects' => Project::latest()->paginate(15),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Projects/Form', [
            'project' => null,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $data['slug'] = $this->uniqueSlug($data['title']);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')
                ->store('projects', 'public');
        }

        Project::create($data);

        return redirect()
            ->route('admin.projects.index')
            ->with('success', 'Project created successfully.');
    }

    public function edit(Project $project)
    {
        return Inertia::render('Admin/Projects/Form', [
            'project' => $project,
        ]);
    }

    public function update(Request $request, Project $project)
    {
        $data = $this->validateData($request);

        $data['slug'] = $this->uniqueSlug(
            $data['title'],
            $project->id
        );

        if ($request->hasFile('image')) {

            if ($project->image && !Str::startsWith($project->image, ['http://', 'https://'])) {
                Storage::disk('public')->delete($project->image);
            }

            $data['image'] = $request->file('image')
                ->store('projects', 'public');
        } else {
            unset($data['image']);
        }

        $project->update($data);

        return redirect()
            ->route('admin.projects.index')
            ->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        if ($project->image && !Str::startsWith($project->image, ['http://', 'https://'])) {
            Storage::disk('public')->delete($project->image);
        }

        $project->delete();

        return back()->with(
            'success',
            'Project deleted successfully.'
        );
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'category' => ['nullable', 'string', 'max:100'],
            'excerpt' => ['nullable', 'string', 'max:1000'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'target_amount' => ['nullable', 'numeric', 'min:0'],
            'raised_amount' => ['nullable', 'numeric', 'min:0'],
            'status' => ['required', 'string', 'max:50'],
            'starts_on' => ['nullable', 'date'],
            'ends_on' => ['nullable', 'date', 'after_or_equal:starts_on'],
            'published' => ['nullable', 'boolean'],
        ]);
    }

    private function uniqueSlug(
        string $title,
        ?int $ignoreId = null
    ): string {
        $base = Str::slug($title) ?: 'project';

        $slug = $base;
        $counter = 1;

        while (
            Project::where('slug', $slug)
                ->when(
                    $ignoreId,
                    fn ($query) =>
                        $query->where('id', '!=', $ignoreId)
                )
                ->exists()
        ) {
            $slug = $base . '-' . $counter++;
        }

        return $slug;
    }
}
