<x-layouts::app :title="__('Create New Category')">
    <form action="{{ route('categories.store') }}" method="POST" class="max-w-sm space-y-6">
        @csrf

        <flux:input 
            name="name"
            label="Category Name"
            :value="old('name')"
            placeholder="e.g., Artificial Intelligence"
            description="Choose a descriptive name for this topic."
            required
        />

        <flux:select 
            name="parent_id" 
            label="Parent Category"
            placeholder="Select parent (Optional)"
            description="Leave unselected for a top-level category."
        >
            <flux:select.option value="">None (Top Level)</flux:select.option>
            @foreach($categories as $category)
                <flux:select.option value="{{ $category->id }}" :selected="old('parent_id') == $category->id">
                    {{ $category->name }}
                </flux:select.option>
            @endforeach
        </flux:select>

        <div class="flex items-center justify-end gap-3">
            <flux:button type="submit" variant="primary">
                Create Category
            </flux:button>
        </div>
    </form>
</x-layouts::app>