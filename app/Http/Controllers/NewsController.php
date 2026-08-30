<?php

namespace App\Http\Controllers;

use App\Models\News;
use Inertia\Inertia;

class NewsController extends Controller
{
    public function index()
    {
        return Inertia::render('News', [
            'news' => News::where('published', true)
                ->orderByDesc('published_at')
                ->orderByDesc('created_at')
                ->paginate(12)
                ->through(fn ($item) => [
                    'id' => $item->id,
                    'title' => $item->title,
                    'slug' => $item->slug,
                    'category' => $item->category,
                    'excerpt' => $item->excerpt,
                    'image' => $item->image,
                    'published_at' => optional($item->published_at)->format('M d, Y'),
                ]),
        ]);
    }

    public function show(News $news)
    {
        abort_unless($news->published, 404);

        return Inertia::render('NewsDetail', [
            'news' => [
                'id' => $news->id,
                'title' => $news->title,
                'slug' => $news->slug,
                'category' => $news->category,
                'excerpt' => $news->excerpt,
                'body' => $news->body,
                'image' => $news->image,
                'published_at' => optional($news->published_at)->format('M d, Y'),
            ],
        ]);
    }
}
