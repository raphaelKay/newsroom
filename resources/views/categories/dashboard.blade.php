<x-layouts::app :title="__('Category Dashboard: :name', ['name' => $category->name])">
    <div class="space-y-8 w-full">
        
        {{-- Header & Back Navigation --}}
        <div class="flex items-center justify-between">
            <div class="space-y-1">
                <div class="flex items-center gap-2">
                    <flux:button href="{{ route('categories.index') }}" variant="ghost" size="sm" icon="arrow-left" />
                    <flux:heading size="xl">{{ $category->name }}</flux:heading>
                </div>
                <flux:subheading>Overview, subcategories, metrics, and linked articles.</flux:subheading>
            </div>

            <div class="flex items-center gap-2">
                @can('update', $category)
                    <flux:button href="{{ route('categories.edit', $category->slug) }}" variant="subtle" size="sm" icon="pencil">
                        Edit Category
                    </flux:button>
                @endcan
            </div>
        </div>

        {{-- Category Summary & Stats Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Overview Details Card --}}
            <div class="md:col-span-2 p-5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-sm space-y-4">
                <flux:heading size="lg">Category Details</flux:heading>
                
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-zinc-500 dark:text-zinc-400 block text-xs uppercase font-medium">Slug</span>
                        <code class="text-xs bg-zinc-100 dark:bg-zinc-800 px-2 py-1 rounded mt-1 inline-block">{{ $category->slug }}</code>
                    </div>

                    <div>
                        <span class="text-zinc-500 dark:text-zinc-400 block text-xs uppercase font-medium">Parent Category</span>
                        <p class="font-medium text-zinc-800 dark:text-zinc-200 mt-1">
                            @if($category->parent)
                                <a href="{{ route('categories.dashboard', $category->parent->slug) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                    {{ $category->parent->name }}
                                </a>
                            @else
                                <span class="text-zinc-400">None (Top Level)</span>
                            @endif
                        </p>
                    </div>

                    <div>
                        <span class="text-zinc-500 dark:text-zinc-400 block text-xs uppercase font-medium">Created On</span>
                        <p class="font-medium text-zinc-800 dark:text-zinc-200 mt-1">
                            {{ $category->created_at?->format('M d, Y') ?? 'N/A' }}
                        </p>
                    </div>

                    @if($category->description)
                        <div class="col-span-2">
                            <span class="text-zinc-500 dark:text-zinc-400 block text-xs uppercase font-medium">Description</span>
                            <p class="text-zinc-700 dark:text-zinc-300 mt-1 leading-relaxed">
                                {{ $category->description }}
                            </p>
                        </div>
                    @endif
                </div>

                {{-- Subcategories Section --}}
                @if($category->children && $category->children->isNotEmpty())
                    <div class="pt-3 border-t border-zinc-100 dark:border-zinc-800">
                        <span class="text-zinc-500 dark:text-zinc-400 block text-xs uppercase font-medium mb-2">Subcategories</span>
                        <div class="flex flex-wrap gap-2">
                            @foreach($category->children as $child)
                                <a href="{{ route('categories.dashboard', $child->slug) }}" class="px-2.5 py-1 text-xs font-medium rounded-md bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 hover:bg-zinc-200 dark:hover:bg-zinc-700">
                                    {{ $child->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            {{-- Metric Card --}}
            <div class="p-5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-sm flex flex-col justify-between">
                <div>
                    <flux:subheading size="sm" class="uppercase tracking-wider font-semibold">Total Articles</flux:subheading>
                    <div class="text-4xl font-extrabold text-zinc-900 dark:text-zinc-100 mt-3">
                        {{ $articles->total() }}
                    </div>
                </div>

                <div class="pt-4 border-t border-zinc-100 dark:border-zinc-800 text-xs text-zinc-500">
                    Updated {{ $category->updated_at?->diffForHumans() }}
                </div>
            </div>
        </div>

        {{-- Paginated Articles Section --}}
        <div class="space-y-4">
            <flux:heading size="lg">Articles in this Category</flux:heading>

            @if($articles->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                    @foreach($articles as $article)
                        <div class="flex flex-col justify-between p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-sm hover:shadow-md transition-shadow h-full">
                            
                            <div class="space-y-2">
                                <a href="{{ route('articles.show', $article->slug ?? $article->id) }}" class="text-base font-semibold text-zinc-900 dark:text-zinc-100 hover:underline line-clamp-2">
                                    {{ $article->title }}
                                </a>
                                
                                @if($article->excerpt)
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400 line-clamp-3">
                                        {{ $article->excerpt }}
                                    </p>
                                @endif
                            </div>

                            <div class="flex items-center justify-between pt-3 border-t border-zinc-100 dark:border-zinc-800/60 text-xs text-zinc-500 mt-4">
                                <span>{{ $article->created_at?->format('M d, Y') }}</span>
                                <a href="{{ route('articles.show', $article->slug ?? $article->id) }}" class="text-indigo-600 dark:text-indigo-400 font-medium hover:underline">
                                    Read &rarr;
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Pagination Links --}}
                <div class="pt-2">
                    {{ $articles->links() }}
                </div>
            @else
                <div class="p-8 text-center border border-dashed border-zinc-300 dark:border-zinc-800 rounded-xl">
                    <flux:subheading>No articles found in this category yet.</flux:subheading>
                </div>
            @endif
        </div>

    </div>
</x-layouts::app>