<x-layouts::app :title="__('Edit Article')">
    <div class="max-w-3xl mx-auto space-y-6">
        
        <div class="flex items-center justify-between border-b border-zinc-200 dark:border-zinc-800 pb-4">
            <div>
                <flux:heading size="xl">Edit Article</flux:heading>
                <flux:subheading>Update the details of your story.</flux:subheading>
            </div>
            <flux:button href="{{ route('articles.index') }}" variant="ghost" icon="arrow-left" size="sm">
                Back to Articles
            </flux:button>
        </div>

        {{-- Global Validation Summary Banner --}}
        @if ($errors->any())
            <div class="p-4 rounded-lg bg-red-50 dark:bg-red-950/50 border border-red-200 dark:border-red-800 text-red-800 dark:text-red-200 space-y-2">
                <div class="flex items-center gap-2 font-semibold">
                    <flux:icon name="exclamation-circle" class="size-5 text-red-500" />
                    <span>There were validation errors with your submission:</span>
                </div>
                <ul class="list-disc list-inside text-sm space-y-1 pl-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('articles.update', $article) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- 1. Article Title --}}
            <flux:field>
                <flux:input 
                    name="title" 
                    label="Article Title" 
                    :value="old('title', $article->title)"
                    placeholder="e.g., Breaking Discoveries in Tech" 
                    maxlength="150"
                    required 
                />
                <flux:error name="title" />
            </flux:field>

            {{-- 2. Status Segmented Control --}}
            @php
                $currentStatus = old('status', $article->status);
            @endphp
            <flux:field>
                <flux:radio.group 
                    name="status" 
                    label="Publishing Status" 
                    variant="segmented"
                    value="{{ $currentStatus }}"
                >
                    <flux:radio value="draft" label="Draft" :checked="$currentStatus === 'draft'" />
                    <flux:radio value="scheduled" label="Scheduled" :checked="$currentStatus === 'scheduled'" />
                    <flux:radio value="published" label="Published" :checked="$currentStatus === 'published'" />
                </flux:radio.group>
                <flux:error name="status" />
            </flux:field>

            {{-- 3. Category --}}
            @php
                $selectedCategory = old('category_id', $article->category_id);
            @endphp
            <flux:field>
                <flux:select 
                    name="category_id" 
                    label="Category" 
                    placeholder="Select a category"
                    required
                >
                    @foreach($categories as $category)
                        <flux:select.option value="{{ $category->id }}" :selected="$selectedCategory == $category->id">
                            {{ $category->name }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
                <flux:error name="category_id" />
            </flux:field>

            {{-- 4. Excerpt --}}
            <flux:field>
                <flux:textarea 
                    name="excerpt" 
                    label="Excerpt" 
                    placeholder="Short summary of the article..." 
                    rows="3"
                    description="Brief summary shown on news feeds and cards."
                    required 
                >{{ old('excerpt', $article->excerpt) }}</flux:textarea>
                <flux:error name="excerpt" />
            </flux:field>

            {{-- 5. Body / Content (Tiptap Container) --}}
            <flux:field>
                <flux:label required>Body</flux:label>
                <div 
                    x-data="tiptapEditor({ content: @js(old('body', $article->body)) })" 
                    class="border border-zinc-200 dark:border-zinc-700 rounded-lg overflow-hidden bg-white dark:bg-zinc-900 focus-within:ring-2 focus-within:ring-accent"
                >
                    {{-- Formatting Toolbar --}}
                    <div class="flex flex-wrap items-center gap-1 p-2 border-b border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-800/50">
                        <button type="button" @click="toggleBold()" :class="{ 'bg-zinc-200 dark:bg-zinc-700': isActive('bold') }" class="p-1.5 rounded hover:bg-zinc-200 dark:hover:bg-zinc-700 font-bold text-sm">B</button>
                        <button type="button" @click="toggleItalic()" :class="{ 'bg-zinc-200 dark:bg-zinc-700': isActive('italic') }" class="p-1.5 rounded hover:bg-zinc-200 dark:hover:bg-zinc-700 italic text-sm">I</button>
                        <button type="button" @click="toggleHeading(2)" :class="{ 'bg-zinc-200 dark:bg-zinc-700': isActive('heading', { level: 2 }) }" class="p-1.5 rounded hover:bg-zinc-200 dark:hover:bg-zinc-700 font-semibold text-xs">H2</button>
                        <button type="button" @click="toggleHeading(3)" :class="{ 'bg-zinc-200 dark:bg-zinc-700': isActive('heading', { level: 3 }) }" class="p-1.5 rounded hover:bg-zinc-200 dark:hover:bg-zinc-700 font-semibold text-xs">H3</button>
                        <button type="button" @click="toggleBulletList()" :class="{ 'bg-zinc-200 dark:bg-zinc-700': isActive('bulletList') }" class="p-1.5 rounded hover:bg-zinc-200 dark:hover:bg-zinc-700 text-xs">• List</button>
                        <button type="button" @click="toggleBlockquote()" :class="{ 'bg-zinc-200 dark:bg-zinc-700': isActive('blockquote') }" class="p-1.5 rounded hover:bg-zinc-200 dark:hover:bg-zinc-700 text-xs">“ Quote</button>
                    </div>

                    {{-- Editor Mount Point --}}
                    <div 
                        x-ref="element" 
                        @click="editor?.chain().focus().run()"
                        class="p-4 min-h-[250px] cursor-text prose dark:prose-invert max-w-none focus:outline-none"
                    ></div>

                    <input type="hidden" name="body" x-model="content">
                </div>
                <flux:error name="body" />
            </flux:field>

            {{-- 6. Schedule Date & Image Upload --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <flux:field>
                    <flux:input 
                        type="datetime-local" 
                        name="published_at" 
                        label="Publish Date / Time" 
                        :value="old('published_at', $article->published_at?->format('Y-m-d\TH:i'))"
                        description="Required if status is scheduled."
                    />
                    <flux:error name="published_at" />
                </flux:field>

                <flux:field>
                    <flux:input 
                        type="file" 
                        name="featured_image" 
                        label="Featured Image (Optional)" 
                        accept="image/*"
                        description="Upload a new image to replace the current one."
                    />
                    <flux:error name="featured_image" />
                </flux:field>
            </div>

            {{-- 7. Tags Checkbox Pill Matrix --}}
            @php
                $selectedTags = collect(old('tags', $article->tags->pluck('id')->toArray()))
                    ->map(fn($id) => (string) $id)
                    ->toArray();
            @endphp
            <flux:field>
                <flux:label>Tags</flux:label>
                
                <div class="flex flex-wrap gap-2 pt-1">
                    @forelse($tags as $tag)
                        @php
                            $isChecked = in_array((string) $tag->id, $selectedTags, true);
                        @endphp
                        <label class="cursor-pointer select-none">
                            <input 
                                type="checkbox" 
                                name="tags[]" 
                                value="{{ $tag->id }}" 
                                class="peer sr-only"
                                {{ $isChecked ? 'checked' : '' }}
                            >
                            <span class="inline-flex items-center gap-1 px-3 py-1.5 rounded-full text-xs font-medium bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 border border-zinc-200 dark:border-zinc-700/80 transition-all peer-checked:bg-zinc-900 peer-checked:text-white dark:peer-checked:bg-white dark:peer-checked:text-zinc-900 peer-checked:border-transparent hover:bg-zinc-200 dark:hover:bg-zinc-700">
                                <span class="text-zinc-400 dark:text-zinc-500 peer-checked:hidden">+</span>
                                {{ $tag->name }}
                            </span>
                        </label>
                    @empty
                        <p class="text-xs text-zinc-500 py-1">No tags available.</p>
                    @endforelse
                </div>

                <flux:error name="tags" />
                <flux:error name="tags.*" />
            </flux:field>

            {{-- 8. Breaking News Toggle --}}
            <div class="pt-2 w-fit">
                <flux:field>
                    <flux:checkbox 
                        name="is_breaking" 
                        label="Mark as Breaking News" 
                        :checked="(bool) old('is_breaking', $article->is_breaking)"
                    />
                    <flux:error name="is_breaking" />
                </flux:field>
            </div>

            {{-- 9. Form Submission --}}
            <div class="pt-4 flex items-center justify-end gap-3 border-t border-zinc-200 dark:border-zinc-800">
                <flux:button href="{{ route('articles.index') }}" variant="ghost">
                    Cancel
                </flux:button>
                <flux:button type="submit" variant="primary">
                    Update Article
                </flux:button>
            </div>
        </form>
    </div>
</x-layouts::app>