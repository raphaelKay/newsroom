<?php

use App\Models\Article;
use App\Models\ArticleGallery;
use App\Models\ArticleImage;
use App\Models\ArticleVideo;
use App\Rules\MaxVideoDuration;
use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;
use getID3;

new class extends Component {
    use WithFileUploads;

    public Article $article;
    public $files = [];
    public $caption = '';
    public $altText = '';

    public function updatedFiles(): void
    {
        $this->validate([
            'files.*' => ['file', 'mimes:jpg,jpeg,png,webp,mp4,mov,webm', 'max:51200'],
        ]);
    }

    public function save(): void
    {
        $this->validate([
            'files' => ['required', 'array'],
            'caption' => ['nullable', 'string', 'max:255'],
            'altText' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($this->files as $file) {
            $mime = $file->getMimeType();
            $isImage = str_starts_with($mime, 'image/');

            DB::transaction(function () use ($file, $mime, $isImage) {
                if ($isImage) {
                    [$width, $height] = getimagesize($file->getRealPath());
                    $path = $file->store("articles/{$this->article->id}/images", 'public');

                    $mediaModel = ArticleImage::create([
                        'file_path' => $path,
                        'mime_type' => $mime,
                        'alt_text' => $this->altText,
                        'width' => $width,
                        'height' => $height,
                    ]);
                } else {
                    // Enforce 1 min 30 sec (90s) limit
                    $this->validate([
                        'files.*' => [new MaxVideoDuration(90)],
                    ]);

                    $getID3 = new getID3();
                    $fileInfo = $getID3->analyze($file->getRealPath());
                    $duration = isset($fileInfo['playtime_seconds']) ? (int) round($fileInfo['playtime_seconds']) : 0;

                    $path = $file->store("articles/{$this->article->id}/videos", 'public');

                    // --- #1: FFMEPG THUMBNAIL GENERATION HERE ---
                    $thumbPath = "articles/{$this->article->id}/videos/thumbs/" . md5($path) . '.jpg';
                    $fullVideoPath = Storage::disk('public')->path($path);
                    $fullThumbPath = Storage::disk('public')->path($thumbPath);

                    if (!file_exists(dirname($fullThumbPath))) {
                        mkdir(dirname($fullThumbPath), 0755, true);
                    }

                    try {
                        $ffmpeg = FFMpeg::create();
                        $ffmpegVideo = $ffmpeg->open($fullVideoPath);
                        $ffmpegVideo->frame(TimeCode::fromSeconds(1))->save($fullThumbPath);
                    } catch (\Exception $e) {
                        $thumbPath = null;
                    }
                    // ----------------------------------------------

                    $mediaModel = ArticleVideo::create([
                        'file_path' => $path,
                        'mime_type' => $mime,
                        'duration_seconds' => $duration,
                        'thumbnail_path' => $thumbPath,
                    ]);
                }

                $this->article->gallery()->create([
                    'media_type' => get_class($mediaModel),
                    'media_id' => $mediaModel->id,
                    'caption' => $this->caption,
                    'display_order' => $this->article->gallery()->count() + 1,
                ]);
            });
        }

        $this->reset(['files', 'caption', 'altText']);
        $this->article->unsetRelation('gallery');
    }

    public function updateOrder(array $orderedIds): void
    {
        foreach ($orderedIds as $index => $id) {
            ArticleGallery::where('id', $id)
                ->where('article_id', $this->article->id)
                ->update(['display_order' => $index + 1]);
        }

        $this->article->unsetRelation('gallery');
    }

    public function deleteMedia(int $galleryId): void
    {
        $item = ArticleGallery::where('article_id', $this->article->id)->find($galleryId);

        if (!$item) return;

        DB::transaction(function () use ($item) {
            $media = $item->media;

            if ($media) {
                if (Storage::disk('public')->exists($media->file_path)) {
                    Storage::disk('public')->delete($media->file_path);
                }

                if ($media instanceof ArticleVideo && $media->thumbnail_path && Storage::disk('public')->exists($media->thumbnail_path)) {
                    Storage::disk('public')->delete($media->thumbnail_path);
                }

                $media->delete();
            }

            $item->delete();
        });

        $this->article->unsetRelation('gallery');
    }

    public function with(): array
    {
        return [
            'galleryItems' => $this->article->gallery()->with('media')->orderBy('display_order')->get(),
        ];
    }
}; ?>

<div class="space-y-6">
    <!-- Drag & Drop Upload Zone -->
    <div 
        x-data="{ isDropping: false }"
        x-on:dragover.prevent="isDropping = true"
        x-on:dragleave.prevent="isDropping = false"
        x-on:drop.prevent="isDropping = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
        :class="isDropping ? 'border-indigo-500 bg-indigo-50' : 'border-gray-300 bg-white'"
        class="border-2 border-dashed rounded-lg p-6 text-center transition cursor-pointer"
    >
        <input type="file" wire:model="files" multiple ref="fileInput" class="hidden" id="galleryFiles" />
        
        <label for="galleryFiles" class="cursor-pointer space-y-2 block">
            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <p class="text-sm text-gray-600">Drag & drop media here, or <span class="text-indigo-600 font-semibold">browse</span></p>
            <p class="text-xs text-gray-400">Videos strictly limited to max 1m 30s (90s)</p>
        </label>

        <div wire:loading wire:target="files" class="text-xs text-indigo-600 font-medium mt-2">
            Uploading media...
        </div>
    </div>

    <!-- Metadata Panel -->
    @if(!empty($files))
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 bg-gray-50 p-4 rounded-md">
            <div>
                <label class="block text-xs font-medium text-gray-700">Caption</label>
                <input type="text" wire:model="caption" placeholder="Optional caption" class="mt-1 w-full text-sm border-gray-300 rounded-md" />
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700">Alt Text (Images only)</label>
                <input type="text" wire:model="altText" placeholder="Image description" class="mt-1 w-full text-sm border-gray-300 rounded-md" />
            </div>
            <div class="col-span-full text-right">
                <button wire:click="save" class="px-4 py-2 bg-indigo-600 text-white rounded-md text-sm font-medium">
                    Save to Gallery
                </button>
            </div>
        </div>
    @endif

    <!-- Drag to Reorder Cards -->
    <div 
        x-data
        x-init="
            new Sortable($el, {
                animation: 150,
                onEnd: () => {
                    let order = Array.from($el.children).map(el => el.dataset.id);
                    $wire.updateOrder(order);
                }
            })
        "
        class="grid grid-cols-2 md:grid-cols-4 gap-4"
    >
        @foreach($galleryItems as $item)
            <div data-id="{{ $item->id }}" class="relative group bg-white border rounded-lg p-2 shadow-sm cursor-grab active:cursor-grabbing">
                @if($item->media instanceof \App\Models\ArticleImage)
                    <img src="{{ Storage::url($item->media->file_path) }}" alt="{{ $item->media->alt_text }}" class="w-full h-32 object-cover rounded" />
                    <span class="absolute top-3 left-3 bg-blue-600 text-white text-[10px] px-1.5 py-0.5 rounded font-mono">IMG</span>
                @elseif($item->media instanceof \App\Models\ArticleVideo)
                    @if($item->media->thumbnail_path)
                        <img src="{{ Storage::url($item->media->thumbnail_path) }}" class="w-full h-32 object-cover rounded" />
                    @else
                        <div class="w-full h-32 bg-gray-900 rounded flex items-center justify-center text-white text-xs">Video</div>
                    @endif
                    <span class="absolute top-3 left-3 bg-red-600 text-white text-[10px] px-1.5 py-0.5 rounded font-mono">
                        {{ $item->media->duration_seconds }}s
                    </span>
                @endif

                @if($item->caption)
                    <p class="text-xs text-gray-600 mt-1 truncate">{{ $item->caption }}</p>
                @endif

                <button 
                    wire:click="deleteMedia({{ $item->id }})" 
                    wire:confirm="Remove this media item?"
                    class="absolute top-3 right-3 bg-red-600 text-white p-1 rounded-full opacity-0 group-hover:opacity-100 transition"
                >
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>
        @endforeach
    </div>
</div>