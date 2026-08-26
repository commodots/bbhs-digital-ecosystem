<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AdminNewsController extends Controller
{
    /**
     * Display all news articles.
     */
    public function index()
    {
        return Inertia::render('Admin/News/Index', [
            'news' => News::latest()->paginate(15),
        ]);
    }

    /**
     * Show create news form.
     */
    public function create()
    {
        return Inertia::render('Admin/News/Form', [
            'news' => null,
        ]);
    }

    /**
     * Store a new news article.
     */
    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $data['user_id'] = $request->user()->id;
        $data['slug'] = $this->uniqueSlug($data['title']);

        $data['published'] = (bool) ($data['published'] ?? false);

        if ($data['published']) {
            $data['published_at'] = now();
        } else {
            $data['published_at'] = null;
        }

        News::create($data);

        return redirect()
            ->route('admin.news.index')
            ->with('success', 'News article created successfully.');
    }

    /**
     * Show edit news form.
     */
    public function edit(News $news)
    {
        return Inertia::render('Admin/News/Form', [
            'news' => $news,
        ]);
    }

    /**
     * Update an existing news article.
     */
    public function update(Request $request, News $news)
    {
        $data = $this->validateData($request);

        $data['slug'] = $this->uniqueSlug(
            $data['title'],
            $news->id
        );

        $data['published'] = (bool) ($data['published'] ?? false);

        if ($data['published']) {
            $data['published_at'] = $news->published_at ?? now();
        } else {
            $data['published_at'] = null;
        }

        /*
        |--------------------------------------------------------------------------
        | Keep original author
        |--------------------------------------------------------------------------
        */

        unset($data['user_id']);

        $news->update($data);

        return redirect()
            ->route('admin.news.index')
            ->with('success', 'News article updated successfully.');
    }

    /**
     * Delete a news article.
     */
    public function destroy(News $news)
    {
        $news->delete();

        return back()->with(
            'success',
            'News article deleted successfully.'
        );
    }

    /**
     * Validate news data.
     */
    private function validateData(Request $request): array
    {
        return $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'category' => [
                'nullable',
                'string',
                'max:100',
            ],

            'excerpt' => [
                'nullable',
                'string',
                'max:500',
            ],

            'body' => [
                'nullable',
                'string',
            ],

            'image' => [
                'nullable',
                'string',
                'max:500',
            ],

            'published' => [
                'nullable',
                'boolean',
            ],
        ]);
    }

    /**
     * Generate a unique slug.
     */
    private function uniqueSlug(
        string $title,
        ?int $ignoreId = null
    ): string {
        $base = Str::slug($title);

        /*
        |--------------------------------------------------------------------------
        | Prevent empty slug
        |--------------------------------------------------------------------------
        */

        if (empty($base)) {
            $base = 'news-article';
        }

        $slug = $base;
        $counter = 1;

        while (
            News::where('slug', $slug)
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