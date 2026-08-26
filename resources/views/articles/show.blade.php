<x-layouts::app :title="$article->title">
    <div class="max-w-4xl mx-auto space-y-10 py-6">

        {{-- Main Article Container --}}
        <article class="space-y-6">
            {{-- Category & Meta --}}
            <div class="flex items-center gap-2 text-xs text-zinc-500">
                <span class="font-medium text-zinc-800 dark:text-zinc-200 uppercase tracking-wider">
                    {{ $article->category->name ?? 'Uncategorized' }}
                </span>
                <span>•</span>
                <time datetime="{{ $article->published_at?->toIso8601String() }}">
                    {{ $article->published_at ? $article->published_at->format('M d, Y') : 'Draft' }}
                </time>
                <span>•</span>
                <span>{{ $article->formatted_reading_time }}</span>
            </div>

            {{-- Title --}}
            <flux:heading size="xl" level="1" class="text-3xl sm:text-4xl font-extrabold text-zinc-900 dark:text-white leading-tight">
                {{ $article->title }}
            </flux:heading>

            {{-- Author Meta --}}
            <div class="flex items-center gap-3 pt-2">
                <div class="size-10 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center font-bold text-zinc-700 dark:text-zinc-200">
                    {{ strtoupper(substr($article->author?->name ?? 'A', 0, 1)) }}
                </div>
                <div>
                    <span class="text-sm font-semibold text-zinc-900 dark:text-white block">
                        {{ $article->author?->name ?? 'Anonymous' }}
                    </span>
                    <span class="text-xs text-zinc-500">
                        Author
                    </span>
                </div>
            </div>

            {{-- Featured Image --}}
            @if($article->featured_image)
                <div class="aspect-video w-full rounded-2xl overflow-hidden bg-zinc-100 dark:bg-zinc-800 my-6">
                    <img src="{{ asset('storage/' . $article->featured_image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
                </div>
            @endif

            {{-- Body Content --}}
            <div class="prose dark:prose-invert max-w-none text-zinc-800 dark:text-zinc-200 leading-relaxed space-y-4">
                {!! $article->body !!}
            </div>

            {{-- Tags --}}
            @if($article->tags && $article->tags->count() > 0)
                <div class="flex flex-wrap gap-2 pt-6 border-t border-zinc-200 dark:border-zinc-800">
                    @foreach($article->tags as $tag)
                        <span class="px-3 py-1 rounded-full text-xs font-medium bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">
                            #{{ $tag->name }}
                        </span>
                    @endforeach
                </div>
            @endif
        </article>

        {{-- Comments Section --}}
        <section class="space-y-8 pt-8 border-t border-zinc-200 dark:border-zinc-800">
            <div class="flex items-center justify-between">
                <flux:heading size="lg" level="2">
                    Discussion ({{ $comments->total() }})
                </flux:heading>
            </div>

            {{-- Comment Form (Only displayed if user is logged in & authorized to comment) --}}
            @auth
                @can('create', App\Models\Comment::class)
                    <form action="" method="POST" class="space-y-4">
                        @csrf
                        <div class="space-y-2">
                            <flux:textarea 
                                name="content" 
                                rows="3" 
                                placeholder="Share your thoughts on this article..." 
                                required
                            />
                            @error('content')
                                <p class="text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="flex justify-end">
                            <flux:button type="submit" variant="primary" icon="paper-airplane">
                                Post Comment
                            </flux:button>
                        </div>
                    </form>
                @else
                    <div class="p-4 rounded-xl bg-zinc-100 dark:bg-zinc-800/50 text-zinc-600 dark:text-zinc-400 text-xs text-center">
                        Your account does not have permission to leave comments on this article.
                    </div>
                @endcan
            @else
                <div class="p-4 rounded-xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-center space-y-2">
                    <p class="text-xs text-zinc-600 dark:text-zinc-400">You must be logged in to join the conversation.</p>
                    <flux:button href="{{ route('login') }}" size="sm" variant="ghost">Log In</flux:button>
                </div>
            @endauth

            {{-- Comments List --}}
            <div class="space-y-6">
                @forelse($comments as $comment)
                    <div class="p-5 rounded-2xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 space-y-4">
                        
                        {{-- Top-Level Comment Header --}}
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="size-8 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center text-xs font-bold text-zinc-700 dark:text-zinc-200">
                                    {{ strtoupper(substr($comment->author?->name ?? $comment->author_name ?? 'A', 0, 1)) }}
                                </div>
                                <div>
                                    <span class="text-xs font-semibold text-zinc-900 dark:text-white block">
                                        {{ $comment->author?->name ?? $comment->author_name ?? 'Anonymous' }}
                                    </span>
                                    <span class="text-[10px] text-zinc-500">
                                        {{ $comment->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>

                            @can('delete', $comment)
                                <form action="{{ route('comments.delete', $comment) }}" method="POST" onsubmit="return confirm('Delete this comment?');">
                                    @csrf
                                    @method('DELETE')
                                    <flux:button type="submit" variant="ghost" size="xs" icon="trash" class="text-red-500 hover:text-red-600" />
                                </form>
                            @endcan
                        </div>

                        {{-- Comment Body --}}
                        <p class="text-xs text-zinc-700 dark:text-zinc-300 leading-relaxed">
                            {{ $comment->content ?? $comment->body }}
                        </p>

                        {{-- Nested Replies --}}
                        @if($comment->replies && $comment->replies->count() > 0)
                            <div class="ml-6 pl-4 border-l-2 border-zinc-100 dark:border-zinc-800 space-y-4 pt-2">
                                @foreach($comment->replies as $reply)
                                    <div class="space-y-2">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-2">
                                                <div class="size-6 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center text-[10px] font-bold text-zinc-600 dark:text-zinc-300">
                                                    {{ strtoupper(substr($reply->author?->name ?? $reply->author_name ?? 'A', 0, 1)) }}
                                                </div>
                                                <span class="text-xs font-medium text-zinc-900 dark:text-white">
                                                    {{ $reply->author?->name ?? $reply->author_name ?? 'Anonymous' }}
                                                </span>
                                                <span class="text-[10px] text-zinc-500">• {{ $reply->created_at->diffForHumans() }}</span>
                                            </div>

                                            @can('delete', $reply)
                                                <form action="{{ route('comments.delete', $reply) }}" method="POST" onsubmit="return confirm('Delete reply?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <flux:button type="submit" variant="ghost" size="xs" icon="trash" class="text-red-500 hover:text-red-600" />
                                                </form>
                                            @endcan
                                        </div>
                                        <p class="text-xs text-zinc-600 dark:text-zinc-400 pl-8">
                                            {{ $reply->content ?? $reply->body }}
                                        </p>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                    </div>
                @empty
                    <div class="text-center py-10 text-zinc-500 text-xs">
                        No comments yet. Be the first to start the discussion!
                    </div>
                @endforelse
            </div>

            {{-- Top-Level Comments Pagination --}}
            @if($comments->hasPages())
                <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800">
                    {{ $comments->links() }}
                </div>
            @endif
        </section>

    </div>
</x-layouts::app>