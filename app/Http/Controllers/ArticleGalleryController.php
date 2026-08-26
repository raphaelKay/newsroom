<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleGallery;
use App\Models\ArticleImage;
use App\Models\ArticleVideo;
use App\Rules\MaxVideoDuration;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use getID3;

class ArticleGalleryController extends Controller
{
    public function store(Request $request, Article $article): RedirectResponse
    {
        $request->validate([
            'media' => ['required', 'file', 'mimes:jpg,jpeg,png,webp,mp4,mov,webm', 'max:51200'],
            'caption' => ['nullable', 'string', 'max:255'],
            'alt_text' => ['nullable', 'string', 'max:255'],
        ]);

        $file = $request->file('media');
        $mime = $file->getMimeType();
        $isImage = str_starts_with($mime, 'image/');

        DB::transaction(function () use ($request, $article, $file, $mime, $isImage) {
            if ($isImage) {
                [$width, $height] = getimagesize($file->getRealPath());
                $path = $file->store("articles/{$article->id}/images", 'public');

                $mediaModel = ArticleImage::create([
                    'file_path' => $path,
                    'mime_type' => $mime,
                    'alt_text' => $request->input('alt_text'),
                    'width' => $width,
                    'height' => $height,
                ]);
            } else {
                // Video validation for max 90 seconds (1 min 30 sec)
                $request->validate([
                    'media' => [new MaxVideoDuration(90)],
                ]);

                $getID3 = new getID3();
                $fileInfo = $getID3->analyze($file->getRealPath());
                $duration = isset($fileInfo['playtime_seconds']) ? (int) round($fileInfo['playtime_seconds']) : 0;
                $path = $file->store("articles/{$article->id}/videos", 'public');

                $mediaModel = ArticleVideo::create([
                    'file_path' => $path,
                    'mime_type' => $mime,
                    'duration_seconds' => $duration,
                ]);
            }

            // Create morph gallery entry
            $article->gallery()->create([
                'media_type' => get_class($mediaModel),
                'media_id' => $mediaModel->id,
                'caption' => $request->input('caption'),
                'display_order' => $article->gallery()->count() + 1,
            ]);
        });

        return back()->with('success', 'Media attached successfully.');
    }

    public function delete(ArticleGallery $galleryItem): RedirectResponse
    {
        DB::transaction(function () use ($galleryItem) {
            $media = $galleryItem->media;

            if ($media && Storage::disk('public')->exists($media->file_path)) {
                Storage::disk('public')->delete($media->file_path);
            }

            $media?->delete();
            $galleryItem->delete();
        });

        return back()->with('success', 'Media item removed.');
    }
}