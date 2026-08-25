<x-layouts::app :title="__('Delete Tag')">
    <form action="{{ route('tags.delete', $tag) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this tag?');">
        @csrf
        @method('DELETE')

        <flux:button type="submit" variant="danger">
            Delete Tag
        </flux:button>
    </form>
</x-layouts::app>
