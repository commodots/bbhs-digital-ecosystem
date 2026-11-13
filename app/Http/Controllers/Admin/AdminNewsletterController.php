<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;

class AdminNewsletterController extends Controller
{
    public function index()
    {
        $subscribers = NewsletterSubscriber::orderByDesc('created_at')->paginate(30)->withQueryString();
        return Inertia::render('Admin/Newsletter/Index', [
            'subscribers' => $subscribers,
            'activeCount' => NewsletterSubscriber::whereNull('unsubscribed_at')->count(),
            'unsubscribedCount' => NewsletterSubscriber::whereNotNull('unsubscribed_at')->count(),
        ]);
    }

    public function send(Request $request)
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:180'],
            'message' => ['required', 'string', 'max:12000'],
        ]);

        $recipients = NewsletterSubscriber::whereNull('unsubscribed_at')->get();
        if ($recipients->isEmpty()) {
            return back()->withErrors(['message' => 'There are no active newsletter subscribers to send to.']);
        }

        $sent = 0;
        $failed = 0;
        foreach ($recipients as $subscriber) {
            $unsubscribeUrl = url('/newsletter/unsubscribe/' . $subscriber->unsubscribe_token);
            $body = $data['message'] . "\n\n---\nYou are receiving this email because you subscribed to BBHS updates.\nUnsubscribe: " . $unsubscribeUrl;
            try {
                Mail::raw($body, function ($mail) use ($subscriber, $data) {
                    $mail->to($subscriber->email)->subject($data['subject']);
                });
                $sent++;
            } catch (\Throwable $e) {
                report($e);
                $failed++;
            }
        }

        return back()->with('success', "Newsletter send completed. Sent: {$sent}; failed: {$failed}.");
    }

    public function export()
    {
        $filename = 'bbhs-newsletter-subscribers-' . now()->format('Ymd-His') . '.csv';
        return response()->streamDownload(function () {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Email', 'Status', 'Subscribed At', 'Unsubscribed At']);
            NewsletterSubscriber::orderBy('email')->chunk(500, function ($rows) use ($out) {
                foreach ($rows as $row) {
                    fputcsv($out, [$row->email, $row->unsubscribed_at ? 'Unsubscribed' : 'Active', $row->subscribed_at, $row->unsubscribed_at]);
                }
            });
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
