<x-layouts::app :title="__('Create New Tag')">
    <form action="{{ route('tags.store') }}" method="POST" class="max-w-sm space-y-6">
        @csrf

        <flux:input 
            name="name"
            label="Tag Name"
            :value="old('name')"
            placeholder="e.g., Artificial Intelligence"
            description="Choose a descriptive name for this topic."
            required
        />

        <div class="flex items-center justify-end gap-3">
            <flux:button type="submit" variant="primary">
                Create Tag
            </flux:button>
        </div>
    </form>
</x-layouts::app>
