<x-layouts::app :title="__('Edit Category: :name', ['name' => $category->name])">
    <form action="{{ route('categories.update', $category->slug) }}" method="POST" class="max-w-sm space-y-6">
        @csrf
        @method('PUT')

        {{-- Category Name --}}
        <flux:input 
            name="name"
            label="Category Name"
            :value="old('name', $category->name)"
            placeholder="e.g., Artificial Intelligence"
            description="Update the name for this category."
            required
        />

        {{-- Parent Category Selection --}}
        <flux:select 
            name="parent_id" 
            label="Parent Category"
            placeholder="Select parent (Optional)"
            description="Leave unselected for a top-level category."
        >
            <flux:select.option value="">None (Top Level)</flux:select.option>
            @foreach($categories as $item)
                {{-- Exclude current category from parent list --}}
                @if($item->id !== $category->id)
                    <flux:select.option 
                        value="{{ $item->id }}" 
                        :selected="old('parent_id', $category->parent_id) == $item->id"
                    >
                        {{ $item->name }}
                    </flux:select.option>
                @endif
            @endforeach
        </flux:select>

        {{-- Form Actions --}}
        <div class="flex items-center justify-end gap-3 pt-2">
            <flux:button href="{{ route('categories.index') }}" variant="ghost">
                Cancel
            </flux:button>
            <flux:button type="submit" variant="primary">
                Update Category
            </flux:button>
        </div>
    </form>
</x-layouts::app>