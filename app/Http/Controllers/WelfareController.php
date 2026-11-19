<?php

namespace App\Http\Controllers;

use App\Notifications\BbhsPlatformNotification;

use App\Models\User;

use App\Models\WelfareActivityLog;

use App\Models\WelfareApplication;
use App\Models\WelfareAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class WelfareController extends Controller
{
    public function index(Request $request)
    {
        $applications = WelfareApplication::where('user_id', $request->user()->id)
            ->withCount('attachments')->latest()->paginate(10);

        return Inertia::render('Welfare/Index', ['applications' => $applications]);
    }

    public function create()
    {
        return Inertia::render('Welfare/Create', [
            'categories' => ['Medical assistance', 'Bereavement', 'Emergency support', 'Financial hardship', 'Education support', 'Other'],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'category' => ['required', 'string', 'max:100'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:20', 'max:15000'],
            'amount_requested' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'attachments' => ['nullable', 'array', 'max:5'],
            'attachments.*' => ['file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:10240'],
        ]);

        $application = WelfareApplication::create([
            'user_id' => $request->user()->id,
            'reference' => 'WEL-' . now()->format('ym') . '-' . Str::upper(Str::random(7)),
            'category' => $data['category'],
            'subject' => $data['subject'],
            'description' => $data['description'],
            'amount_requested' => $data['amount_requested'] ?? null,
            'status' => 'submitted',
        ]);

        WelfareActivityLog::create([
            'welfare_application_id' => $application->id,
            'actor_id' => $request->user()->id,
            'action' => 'application_submitted',
            'details' => ['status' => 'submitted', 'category' => $application->category],
        ]);

        User::query()->where('welfare_team', true)->get()->each(function ($recipient) use ($application) {
            $recipient->notify(new BbhsPlatformNotification(
                'New confidential welfare application',
                'A new welfare application ('.$application->reference.') is ready for review.',
                'welfare', '/admin/welfare/'.$application->id,
                ['application_id' => $application->id, 'reference' => $application->reference]
            ));
        });
        User::query()->get()->filter(fn ($recipient) => $recipient->isAdmin())->each(function ($recipient) use ($application) {
            $recipient->notify(new BbhsPlatformNotification(
                'New confidential welfare application',
                'A new welfare application ('.$application->reference.') has been submitted.',
                'welfare', '/admin/welfare/'.$application->id,
                ['application_id' => $application->id, 'reference' => $application->reference]
            ));
        });

        foreach ($request->file('attachments', []) as $file) {
            $path = $file->store('welfare/' . $application->id, 'local');
            $application->attachments()->create([
                'original_name' => $file->getClientOriginalName(),
                'storage_path' => $path,
                'mime_type' => $file->getMimeType(),
                'size_bytes' => $file->getSize(),
            ]);
        }

        return redirect()->route('welfare.show', $application)->with('success', 'Your welfare application has been submitted confidentially.');
    }

    public function show(Request $request, WelfareApplication $application)
    {
        abort_unless($application->user_id === $request->user()->id || $request->user()->isAdmin(), 403);
        $application->load(['attachments:id,welfare_application_id,original_name,size_bytes', 'disbursements']);
        return Inertia::render('Welfare/Show', ['application' => $application]);
    }

    public function downloadAttachment(Request $request, WelfareApplication $application, WelfareAttachment $attachment)
    {
        abort_unless($attachment->welfare_application_id === $application->id, 404);
        abort_unless($application->user_id === $request->user()->id || $request->user()->isAdmin(), 403);
        abort_unless(Storage::disk('local')->exists($attachment->storage_path), 404);
        return Storage::disk('local')->download($attachment->storage_path, $attachment->original_name);
    }
}
