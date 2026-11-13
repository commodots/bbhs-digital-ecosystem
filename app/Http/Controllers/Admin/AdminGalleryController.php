<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GalleryAlbum;
use App\Models\GalleryImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AdminGalleryController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Gallery/Index', [
            'albums' => GalleryAlbum::withCount('images')->with('images')->latest()->get(),
        ]);
    }

    public function storeAlbum(Request $request)
    {
        $data = $request->validate([
            'title' => ['required','string','max:255'],
            'description' => ['nullable','string','max:5000'],
            'cover_image' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:5120'],
            'published' => ['nullable','boolean'],
        ]);
        $base = Str::slug($data['title']) ?: 'album';
        $slug = $base; $i = 1;
        while (GalleryAlbum::where('slug', $slug)->exists()) $slug = $base.'-'.($i++);
        $data['slug'] = $slug;
        if ($request->hasFile('cover_image')) $data['cover_image'] = $request->file('cover_image')->store('gallery/albums','public');
        GalleryAlbum::create($data);
        return back()->with('success','Gallery album created.');
    }

    public function updateAlbum(Request $request, GalleryAlbum $album)
    {
        $data = $request->validate([
            'title' => ['required','string','max:255'],
            'description' => ['nullable','string','max:5000'],
            'cover_image' => ['nullable','image','mimes:jpg,jpeg,png,webp','max:5120'],
            'published' => ['nullable','boolean'],
        ]);
        if ($request->hasFile('cover_image')) {
            if ($album->cover_image) Storage::disk('public')->delete($album->cover_image);
            $data['cover_image'] = $request->file('cover_image')->store('gallery/albums','public');
        } else unset($data['cover_image']);
        $album->update($data);
        return back()->with('success','Album updated.');
    }

    public function uploadImages(Request $request, GalleryAlbum $album)
    {
        $data = $request->validate(['images' => ['required','array','min:1','max:20'], 'images.*' => ['required','image','mimes:jpg,jpeg,png,webp','max:5120']]);
        $sort = (int) $album->images()->max('sort_order');
        foreach ($data['images'] as $file) {
            $album->images()->create(['image' => $file->store('gallery/images','public'), 'title' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME), 'sort_order' => ++$sort, 'published' => true]);
        }
        return back()->with('success', count($data['images']).' image(s) uploaded.');
    }

    public function deleteImage(GalleryImage $image)
    {
        Storage::disk('public')->delete($image->image);
        $image->delete();
        return back()->with('success','Image deleted.');
    }

    public function destroyAlbum(GalleryAlbum $album)
    {
        foreach ($album->images as $image) Storage::disk('public')->delete($image->image);
        if ($album->cover_image) Storage::disk('public')->delete($album->cover_image);
        $album->delete();
        return back()->with('success','Album and its images deleted.');
    }
}
