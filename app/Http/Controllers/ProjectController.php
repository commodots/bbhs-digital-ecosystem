<?php

namespace App\Http\Controllers;

use App\Models\FundraisingCampaign;
use App\Models\Project;
use Inertia\Inertia;

class ProjectController extends Controller
{
    private function image(?string $path): string
    {
        if (!$path) return '/images/bbhs/school/bbhs-school-gate.jpg';
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) return $path;
        return '/storage/' . ltrim($path, '/');
    }

    private function campaignData(FundraisingCampaign $campaign): array
    {
        $target = (int) $campaign->target_amount_kobo;
        $raised = (int) $campaign->raised_amount_kobo;
        return [
            'id' => $campaign->id, 'title' => $campaign->title, 'slug' => $campaign->slug,
            'excerpt' => $campaign->excerpt, 'image' => $this->image($campaign->image),
            'target_amount' => $target / 100, 'raised_amount' => $raised / 100,
            'progress' => $target > 0 ? min(100, round($raised / $target * 100)) : 0,
            'donor_count' => $campaign->donations()->where('status', 'success')->count(),
        ];
    }

    private function data(Project $project): array
    {
        $target = (float) $project->target_amount;
        $raised = (float) $project->raised_amount;
        return [
            'id' => $project->id, 'title' => $project->title, 'slug' => $project->slug,
            'category' => $project->category, 'excerpt' => $project->excerpt,
            'description' => $project->description, 'image' => $this->image($project->image),
            'target_amount' => $target, 'raised_amount' => $raised,
            'progress' => $target > 0 ? min(100, round(($raised / $target) * 100)) : 0,
            'status' => $project->status,
            'starts_on' => optional($project->starts_on)->format('M d, Y'),
            'ends_on' => optional($project->ends_on)->format('M d, Y'),
            'campaigns' => $project->fundraisingCampaigns()->where('published', true)
                ->where('status', 'active')->latest()->get()
                ->map(fn ($campaign) => $this->campaignData($campaign))->values(),
        ];
    }

    public function index()
    {
        $projects = Project::where('published', true)->latest()->paginate(12)
            ->through(fn ($project) => $this->data($project));
        $standaloneCampaigns = FundraisingCampaign::where('published', true)
            ->where('status', 'active')->whereNull('project_id')->latest()->get()
            ->map(fn ($campaign) => $this->campaignData($campaign))->values();

        return Inertia::render('Projects', [
            'projects' => $projects,
            'standaloneCampaigns' => $standaloneCampaigns,
        ]);
    }

    public function show(Project $project)
    {
        abort_unless($project->published, 404);
        return Inertia::render('ProjectDetail', ['project' => $this->data($project)]);
    }
}
