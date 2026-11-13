<?php

namespace App\Http\Controllers;

use App\Models\NewsletterSubscriber;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $validated = $request->validate(['email' => ['required', 'email:rfc', 'max:255']]);
        $email = mb_strtolower(trim($validated['email']));
        $subscriber = NewsletterSubscriber::firstOrNew(['email' => $email]);
        if (!$subscriber->exists) {
            $subscriber->unsubscribe_token = Str::random(48);
        }
        $subscriber->subscribed_at = now();
        $subscriber->unsubscribed_at = null;
        $subscriber->save();

        return back()->with('newsletter_success', 'Thank you for subscribing to BBHS updates.');
    }

    public function unsubscribe(string $token)
    {
        $subscriber = NewsletterSubscriber::where('unsubscribe_token', $token)->firstOrFail();
        $subscriber->update(['unsubscribed_at' => now()]);
        return response('You have been unsubscribed from BBHS newsletter updates.', 200)
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }
}
