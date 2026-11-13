<?php

namespace App\Http\Controllers;

use App\Models\GalleryAlbum;
use Inertia\Inertia;

class GalleryController extends Controller
{
    public function index()
    {
        $albums = GalleryAlbum::where('published', true)->with(['images' => fn ($q) => $q->where('published', true)])
            ->latest()->get()->map(function ($album) {
                $url = fn ($path) => $path ? (str_starts_with($path, 'http') || str_starts_with($path, '/') ? $path : '/storage/'.ltrim($path, '/')) : null;
                return [
                    'id' => $album->id, 'title' => $album->title, 'description' => $album->description,
                    'cover_image' => $url($album->cover_image),
                    'images' => $album->images->map(fn ($image) => ['id' => $image->id, 'title' => $image->title, 'caption' => $image->caption, 'url' => $url($image->image)]),
                ];
            });
        return Inertia::render('Gallery', ['albums' => $albums]);
    }
}
