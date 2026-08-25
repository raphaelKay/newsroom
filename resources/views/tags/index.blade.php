<x-layouts::app :title="__('Tags')">
    <div class="w-full space-y-6">
        
        {{-- Responsive Grid Layout --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($tags as $tag)
                <div class="flex flex-col justify-between p-4 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-900 shadow-sm hover:shadow-md transition-shadow h-full">
                    
                    {{-- Card Header: Tag Name & Show Link --}}
                    <div class="mb-4">
                        <a href="{{ route('tags.show', $tag->slug) }}" class="text-base font-semibold text-zinc-900 dark:text-zinc-100 hover:underline line-clamp-1">
                            {{ $tag->name }}
                        </a>
                    </div>

                    {{-- Card Footer: Action Buttons --}}
                    <div class="flex items-center justify-between pt-3 border-t border-zinc-100 dark:border-zinc-800/60 gap-2 mt-auto">
                        <div class="flex items-center gap-1.5">
                            {{-- Dashboard Link --}}
                            @can('viewDashboard', $tag)
                                <flux:button href="{{ route('tags.dashboard', $tag->slug) }}" variant="subtle" size="sm" icon="squares-2x2">
                                    Dashboard
                                </flux:button>
                            @endcan

                            {{-- Edit Link --}}
                            @can('update', $tag)
                                <flux:button href="{{ route('tags.edit', $tag->slug) }}" variant="subtle" size="sm" icon="pencil">
                                    Edit
                                </flux:button>
                            @endcan
                        </div>

                        {{-- Delete Trigger --}}
                        @can('delete', $tag)
                            <flux:modal.trigger name="delete-tag-{{ $tag->slug }}">
                                <flux:button variant="ghost" size="sm" icon="trash" class="text-red-600 hover:text-red-700 hover:bg-red-50 dark:hover:bg-red-950/30" />
                            </flux:modal.trigger>
                        @endcan
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Modals Rendered Outside the Grid Container --}}
        @foreach($tags as $tag)
            @can('delete', $tag)
                <flux:modal name="delete-tag-{{ $tag->slug }}" class="max-w-md space-y-6">
                    <div>
                        <flux:heading size="lg">Delete Tag?</flux:heading>
                        <flux:subheading>
                            Are you sure you want to delete <strong class="text-zinc-900 dark:text-zinc-100">'{{ $tag->name }}'</strong>? This action cannot be undone.
                        </flux:subheading>
                    </div>

                    <div class="flex justify-end space-x-3">
                        <flux:modal.close>
                            <flux:button variant="ghost">Cancel</flux:button>
                        </flux:modal.close>

                        <form action="{{ route('tags.delete', $tag->slug) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <flux:button type="submit" variant="danger">
                                Delete Tag
                            </flux:button>
                        </form>
                    </div>
                </flux:modal>
            @endcan
        @endforeach

        {{-- Pagination --}}
        <div class="pt-2">
            {{ $tags->links() }}
        </div>
        
    </div>
</x-layouts::app>