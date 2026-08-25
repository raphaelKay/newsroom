<x-layouts::app :title="__('Dashboard: ' . $article->title)">
    <div class="space-y-8 max-w-7xl mx-auto">

        {{-- Header & Meta Information --}}
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 border-b border-zinc-200 dark:border-zinc-800 pb-5">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <flux:badge size="sm" :color="$article->status === 'published' ? 'emerald' : ($article->status === 'scheduled' ? 'blue' : 'zinc')">
                        {{ ucfirst($article->status) }}
                    </flux:badge>
                    @if($article->is_breaking)
                        <flux:badge size="sm" color="red">Breaking</flux:badge>
                    @endif
                    <span class="text-xs text-zinc-500">• Published {{ $article->published_at ? $article->published_at->format('M d, Y') : 'Draft' }}</span>
                </div>

                <flux:heading size="xl" level="1" class="line-clamp-1">{{ $article->title }}</flux:heading>

                <flux:subheading class="flex items-center gap-2 text-xs">
                    <span>By {{ $article->author?->name ?: 'Unknown' }}</span>
                    <span>•</span>
                    <span class="font-medium text-zinc-700 dark:text-zinc-300">{{ $article->category->name ?? 'Uncategorized' }}</span>
                    <span>•</span>
                    <span class="inline-flex items-center gap-1 text-zinc-500">
                        <flux:icon name="clock" class="size-3.5" />
                        {{ $article->formatted_reading_time }} ({{ number_format($article->word_count) }} words)
                    </span>
                </flux:subheading>
            </div>

            {{-- Actions --}}
            <div class="flex items-center gap-2">
                <flux:button href="{{ route('articles.edit', $article) }}" variant="primary" icon="pencil-square">
                    Edit Article
                </flux:button>
                <flux:button href="{{ route('articles.show', $article) }}" variant="ghost" icon="arrow-top-right-on-square" target="_blank">
                    Preview
                </flux:button>
            </div>
        </div>

        {{-- Metrics Grid (KPI Cards) --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

            {{-- 1. Total Article Views --}}
            <div class="p-5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 space-y-2">
                <div class="flex items-center justify-between text-zinc-500">
                    <span class="text-xs font-medium uppercase tracking-wider">Total Readers</span>
                    <flux:icon name="eye" class="size-5" />
                </div>
                <div class="flex items-baseline justify-between">
                    <span class="text-3xl font-bold text-zinc-900 dark:text-white">
                        {{ number_format($article->views_count ?? 0) }}
                    </span>
                    <span class="text-xs text-zinc-500">Recorded views</span>
                </div>
            </div>

            {{-- 2. Word Count --}}
            <div class="p-5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 space-y-2">
                <div class="flex items-center justify-between text-zinc-500">
                    <span class="text-xs font-medium uppercase tracking-wider">Word Count</span>
                    <flux:icon name="document-text" class="size-5" />
                </div>
                <div class="flex items-baseline justify-between">
                    <span class="text-3xl font-bold text-zinc-900 dark:text-white">
                        {{ number_format($article->word_count) }}
                    </span>
                    <span class="text-xs text-zinc-500">Total words</span>
                </div>
            </div>

            {{-- 3. Reading Time --}}
            <div class="p-5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 space-y-2">
                <div class="flex items-center justify-between text-zinc-500">
                    <span class="text-xs font-medium uppercase tracking-wider">Est. Read Time</span>
                    <flux:icon name="clock" class="size-5" />
                </div>
                <div class="flex items-baseline justify-between">
                    <span class="text-3xl font-bold text-zinc-900 dark:text-white">
                        {{ $article->formatted_reading_time }}
                    </span>
                    <span class="text-xs text-zinc-500">@ 200 wpm</span>
                </div>
            </div>

            {{-- 4. Comments / Interactions --}}
            <div class="p-5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 space-y-2">
                <div class="flex items-center justify-between text-zinc-500">
                    <span class="text-xs font-medium uppercase tracking-wider">Comments</span>
                    <flux:icon name="chat-bubble-left-right" class="size-5" />
                </div>
                <div class="flex items-baseline justify-between">
                    <span class="text-3xl font-bold text-zinc-900 dark:text-white">
                        {{ number_format($comments->count() ?? 0) }}
                    </span>
                    <span class="text-xs text-zinc-500">Total responses</span>
                </div>
            </div>

        </div>

        {{-- Main Section Layout --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Left Column: Overview, Analytics & Comments --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Article Excerpt & Tags --}}
                <div class="p-6 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 space-y-4">
                    <flux:heading size="lg">Article Summary</flux:heading>

                    <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        {{ $article->excerpt ?? 'No summary available for this article.' }}
                    </p>

                    {{-- Tags --}}
                    <div class="flex flex-wrap gap-1.5 pt-3 border-t border-zinc-100 dark:border-zinc-800">
                        @forelse($article->tags ?? [] as $tag)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">
                                #{{ $tag->name }}
                            </span>
                        @empty
                            <span class="text-xs text-zinc-500">No tags assigned.</span>
                        @endforelse
                    </div>
                </div>

                {{-- Comments Moderation Section --}}
                <div class="p-6 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 space-y-6">
                    <div class="flex items-center justify-between border-b border-zinc-100 dark:border-zinc-800 pb-4">
                        <div>
                            <flux:heading size="lg">Comments</flux:heading>
                            <flux:subheading size="xs">Review and manage user comments posted on this article.</flux:subheading>
                        </div>
                        <flux:badge size="sm" color="zinc">{{ $comments->count() }} Comments</flux:badge>
                    </div>

                    <div class="space-y-4">
                        @forelse($comments as $comment)
                            <div class="p-4 rounded-lg border border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/30 space-y-3">
                                {{-- Comment Author --}}
                                <div class="flex items-center justify-between gap-2">
                                    <div class="flex items-center gap-2">
                                        <div class="size-7 rounded-full bg-zinc-200 dark:bg-zinc-700 flex items-center justify-center text-xs font-bold text-zinc-700 dark:text-zinc-200">
                                            {{ strtoupper(substr($comment->user?->name ?? $comment->author_name ?? 'A', 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="text-xs font-semibold text-zinc-900 dark:text-white block leading-none">
                                                {{ $comment->user?->name ?? $comment->author_name ?? 'Anonymous' }}
                                            </span>
                                            <span class="text-[10px] text-zinc-500">
                                                {{ $comment->created_at->diffForHumans() }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Comment Body --}}
                                <p class="text-xs text-zinc-700 dark:text-zinc-300 leading-relaxed">
                                    {{ $comment->content }}
                                </p>

                                {{-- Moderation Actions --}}
                                <div class="flex items-center justify-between pt-2 border-t border-zinc-200/60 dark:border-zinc-800">
                                    <span class="text-[10px] text-zinc-400 font-mono">IP: {{ $comment->ip_address ?? 'N/A' }}</span>

                                    {{-- Delete Form --}}
                                    <form action="{{ route('comments.delete', $comment) }}" method="POST" onsubmit="return confirm('Delete this comment permanently?');">
                                        @csrf
                                        @method('DELETE')
                                        <flux:button type="submit" variant="danger" size="xs" icon="trash">
                                            Remove
                                        </flux:button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-zinc-500 text-xs">
                                No comments posted yet.
                            </div>
                        @endforelse
                    </div>

                    {{-- Pagination Links --}}
                    @if($comments->hasPages())
                        <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800">
                            {{ $comments->links() }}
                        </div>
                    @endif
                </div>

                {{-- Recent View Log Preview --}}
                <div class="p-6 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 space-y-4">
                    <flux:heading size="lg">Recent Reader Log</flux:heading>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-zinc-600 dark:text-zinc-400">
                            <thead class="bg-zinc-50 dark:bg-zinc-800/50 uppercase font-semibold text-zinc-500 border-b border-zinc-200 dark:border-zinc-800">
                                <tr>
                                    <th class="px-3 py-2">IP Address</th>
                                    <th class="px-3 py-2">User Agent</th>
                                    <th class="px-3 py-2 text-right">Viewed At</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                                @forelse($article->views()->latest('viewed_at')->take(5)->get() as $view)
                                    <tr>
                                        <td class="px-3 py-2.5 font-mono text-zinc-900 dark:text-white">
                                            {{ $view->ip_address ?? 'Unknown' }}
                                        </td>
                                        <td class="px-3 py-2.5 max-w-xs truncate text-zinc-500" title="{{ $view->user_agent }}">
                                            {{ $view->user_agent ?? 'Unknown Device' }}
                                        </td>
                                        <td class="px-3 py-2.5 text-right whitespace-nowrap text-zinc-500">
                                            {{ $view->viewed_at?->diffForHumans() ?? 'N/A' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-3 py-4 text-center text-zinc-500">
                                            No views recorded yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            {{-- Right Column: Article Details & Controls --}}
            <div class="space-y-6">

                {{-- Metadata Card --}}
                <div class="p-5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 space-y-4">
                    <flux:heading size="sm">Publication Details</flux:heading>

                    <div class="space-y-3 text-xs">
                        <div class="flex justify-between py-2 border-b border-zinc-100 dark:border-zinc-800">
                            <span class="text-zinc-500">Status</span>
                            <span class="font-semibold text-zinc-900 dark:text-white capitalize">{{ $article->status }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-zinc-100 dark:border-zinc-800">
                            <span class="text-zinc-500">Published Date</span>
                            <span class="font-semibold text-zinc-900 dark:text-white">
                                {{ $article->published_at ? $article->published_at->format('M d, Y H:i') : 'Unpublished' }}
                            </span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-zinc-100 dark:border-zinc-800">
                            <span class="text-zinc-500">Author</span>
                            <span class="font-semibold text-zinc-900 dark:text-white">{{ $article->author?->name ?: 'Unknown' }}</span>
                        </div>
                        <div class="flex justify-between py-2 border-b border-zinc-100 dark:border-zinc-800">
                            <span class="text-zinc-500">Created At</span>
                            <span class="font-semibold text-zinc-900 dark:text-white">{{ $article->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex justify-between py-2">
                            <span class="text-zinc-500">Last Updated</span>
                            <span class="font-semibold text-zinc-900 dark:text-white">{{ $article->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>

                {{-- Featured Image Box --}}
                @if($article->featured_image)
                    <div class="p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 space-y-3">
                        <flux:heading size="sm">Featured Image</flux:heading>
                        <div class="aspect-video w-full rounded-lg overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                            <img src="{{ asset('storage/' . $article->featured_image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
                        </div>
                    </div>
                @endif

                {{-- Danger Zone / Actions --}}
                <div class="p-5 rounded-xl border border-red-200 dark:border-red-950 bg-red-50/40 dark:bg-red-950/20 space-y-3">
                    <flux:heading size="sm" class="text-red-700 dark:text-red-400">Danger Zone</flux:heading>

                    {{-- Modal Trigger Button --}}
                    <flux:modal.trigger name="delete-article">
                        <flux:button variant="danger" icon="trash" size="sm" class="w-full">
                            Delete Article
                        </flux:button>
                    </flux:modal.trigger>
                </div>

            </div>
        </div>

    </div>

    {{-- Flux Confirmation Modal --}}
    <flux:modal name="delete-article" class="md:w-96 space-y-6">
        <div>
            <flux:heading size="lg">Delete Article?</flux:heading>
            <flux:subheading>
                Are you sure you want to delete <span class="font-medium text-zinc-900 dark:text-white">"{{ $article->title }}"</span>? This action cannot be undone.
            </flux:subheading>
        </div>

        <form action="{{ route('articles.delete', $article) }}" method="POST">
            @csrf
            @method('DELETE')

            <div class="flex items-center gap-2">
                <flux:modal.close>
                    <flux:button variant="ghost" class="w-full">Cancel</flux:button>
                </flux:modal.close>

                <flux:button type="submit" variant="danger" class="w-full">
                    Delete
                </flux:button>
            </div>
        </form>
    </flux:modal>
</x-layouts::app>