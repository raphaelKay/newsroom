<x-layouts::app :title="__('Edit Tag: :name', ['name' => $tag->name])">
    <form action="{{ route('tags.update', $tag) }}" method="POST" class="max-w-sm space-y-6">
        @csrf
        @method('PUT')

        <flux:input 
            name="name"
            label="Tag Name"
            :value="old('name', $tag->name)"
            placeholder="e.g., Artificial Intelligence"
            description="Update the tag name."
            required
        />

        <div class="flex items-center justify-end gap-3">
            <flux:button href="{{ route('tags.index') }}" variant="ghost">
                Cancel
            </flux:button>
            <flux:button type="submit" variant="primary">
                Update Tag
            </flux:button>
        </div>
    </form>
</x-layouts::app>
