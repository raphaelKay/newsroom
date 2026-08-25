<x-layouts::app :title="__('Articles')">
    <div class="space-y-6">

        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-zinc-200 dark:border-zinc-800 pb-4">
            <div class="flex items-center gap-3">
                <flux:icon name="newspaper" class="size-7 text-zinc-500" />
                <div>
                    <flux:heading size="xl">Articles</flux:heading>
                    <flux:subheading>Manage, edit, and publish news stories and posts.</flux:subheading>
                </div>
            </div>
            
            <flux:button href="{{ route('articles.create') }}" variant="primary" icon="pencil-square">
                Create Article
            </flux:button>
        </div>

        {{-- Filters & Search Bar --}}
        <form method="GET" action="{{ route('articles.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-4">
            {{-- Search Input --}}
            <div class="sm:col-span-6 md:col-span-5">
                <flux:input 
                    name="search" 
                    placeholder="Search by title..." 
                    :value="request('search')" 
                    icon="magnifying-glass"
                    clearable
                />
            </div>

            {{-- Status Filter --}}
            <div class="sm:col-span-3 md:col-span-3">
                <flux:select name="status" placeholder="All Statuses" @change="$el.form.submit()">
                    <flux:select.option value="">All Statuses</flux:select.option>
                    <flux:select.option value="draft" :selected="request('status') === 'draft'">Draft</flux:select.option>
                    <flux:select.option value="scheduled" :selected="request('status') === 'scheduled'">Scheduled</flux:select.option>
                    <flux:select.option value="published" :selected="request('status') === 'published'">Published</flux:select.option>
                </flux:select>
            </div>

            {{-- Category Filter --}}
            <div class="sm:col-span-3 md:col-span-3">
                <flux:select name="category" placeholder="All Categories" @change="$el.form.submit()">
                    <flux:select.option value="">All Categories</flux:select.option>
                    @foreach($categories as $category)
                        <flux:select.option value="{{ $category->id }}" :selected="request('category') == $category->id">
                            {{ $category->name }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </div>

            <button type="submit" class="hidden">Search</button>
        </form>

        {{-- Articles Table --}}
        <div class="border border-zinc-200 dark:border-zinc-800 rounded-lg overflow-hidden bg-white dark:bg-zinc-900 shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-zinc-600 dark:text-zinc-400">
                    <thead class="bg-zinc-50 dark:bg-zinc-800/60 text-xs uppercase font-semibold text-zinc-500 border-b border-zinc-200 dark:border-zinc-800">
                        <tr>
                            <th class="px-4 py-3">Article</th>
                            <th class="px-4 py-3">Category</th>
                            <th class="px-4 py-3">Status</th>
                            <th class="px-4 py-3">Author</th>
                            <th class="px-4 py-3">Date</th>
                            <th class="px-4 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-800">
                        @forelse($articles as $article)
                            <tr class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                                
                                {{-- Title + Image Thumbnail --}}
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3 min-w-[240px]">
                                        @if($article->featured_image)
                                            <img 
                                                src="{{ Storage::url($article->featured_image) }}" 
                                                alt="{{ $article->title }}" 
                                                class="size-11 object-cover rounded-md border border-zinc-200 dark:border-zinc-800 shrink-0"
                                            />
                                        @else
                                            <div class="size-11 rounded-md bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 flex items-center justify-center shrink-0 text-zinc-400">
                                                <flux:icon name="newspaper" class="size-5" />
                                            </div>
                                        @endif

                                        <div class="space-y-0.5">
                                            <div class="flex items-center gap-2">
                                                <a href="{{ route('articles.edit', $article->slug) }}" class="font-medium text-zinc-900 dark:text-zinc-100 hover:text-accent line-clamp-1">
                                                    {{ $article->title }}
                                                </a>
                                                @if($article->is_breaking)
                                                    <flux:badge color="red" size="sm" inset="top bottom">Breaking</flux:badge>
                                                @endif
                                            </div>
                                            <p class="text-xs text-zinc-400 line-clamp-1">{{ $article->excerpt }}</p>
                                        </div>
                                    </div>
                                </td>

                                {{-- Category --}}
                                <td class="px-4 py-3 whitespace-nowrap">
                                    <flux:badge color="zinc" size="sm">
                                        {{ $article->category->name ?? 'Uncategorized' }}
                                    </flux:badge>
                                </td>

                                {{-- Status Badge --}}
                                <td class="px-4 py-3 whitespace-nowrap">
                                    @switch($article->status)
                                        @case('published')
                                            <flux:badge color="green" size="sm">Published</flux:badge>
                                            @break
                                        @case('scheduled')
                                            <flux:badge color="amber" size="sm">Scheduled</flux:badge>
                                            @break
                                        @default
                                            <flux:badge color="zinc" size="sm">Draft</flux:badge>
                                    @endswitch
                                </td>

                                {{-- Author --}}
                                <td class="px-4 py-3 whitespace-nowrap text-xs">
                                    {{ $article->author?->name ?: 'Unknown' }}
                                </td>

                                {{-- Published Date --}}
                                <td class="px-4 py-3 whitespace-nowrap text-xs text-zinc-500">
                                    @if($article->published_at)
                                        {{ $article->published_at->format('M j, Y H:i') }}
                                    @else
                                        <span class="text-zinc-400">—</span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="px-4 py-3 whitespace-nowrap text-right">
                                    <div class="flex items-center justify-end gap-1">
                                        <flux:button 
                                            href="{{ route('articles.dashboard', $article->slug) }}" 
                                            variant="ghost" 
                                            icon="presentation-chart-line" 
                                            size="sm" 
                                            aria-label="Edit Article"
                                        />

                                        <flux:button 
                                            href="{{ route('articles.edit', $article->slug) }}" 
                                            variant="ghost" 
                                            icon="pencil-square" 
                                            size="sm" 
                                            aria-label="Edit Article"
                                        />

                                        {{-- Delete Modal Trigger --}}
                                        <form action="{{ route('articles.delete', $article->slug) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this article?');">
                                            @csrf
                                            @method('DELETE')
                                            <flux:button 
                                                type="submit" 
                                                variant="ghost" 
                                                icon="trash" 
                                                size="sm" 
                                                class="text-red-500 hover:text-red-600" 
                                                aria-label="Delete Article"
                                            />
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-zinc-500">
                                    <div class="flex flex-col items-center justify-center space-y-3">
                                        <flux:icon name="newspaper" class="size-10 text-zinc-300 dark:text-zinc-600" />
                                        <p class="font-medium text-zinc-600 dark:text-zinc-400">No articles found.</p>
                                        <flux:button href="{{ route('articles.create') }}" variant="primary" size="sm" icon="pencil-square">
                                            Create First Article
                                        </flux:button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination Footer --}}
            @if($articles->hasPages())
                <div class="p-4 border-t border-zinc-200 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-800/30">
                    {{ $articles->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>
</x-layouts::app>