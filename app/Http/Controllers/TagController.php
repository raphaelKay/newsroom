<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Tag;
use App\Http\Requests\Tags\StoreTagRequest;
use App\Http\Requests\Tags\UpdateTagRequest;

class TagController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request) {
        $tags = Tag::latest()->paginate(12);
        return view('tags.index', ['tags' => $tags]);
    }

    public function show(Request $request, Tag $tag) {
        return view('tags.show', ['tag' => $tag]);
    }

    public function create(Request $request) {
        $this->authorize(auth()->user());

        return view('tags.create', []);
    }

    public function edit(Request $request, Tag $tag) {
        $this->authorize('update', $tag);

        return view('tags.edit', ['tag' => $tag]);
    }

    public function store(StoreTagRequest $request): RedirectResponse {
        $this->authorize(auth()->user());

        Tag::create($request->validated());
        return redirect()->route('tags.index')->with('status', 'tag created successfully.');
    }

    public function update(UpdateTagRequest $request, Tag $tag): RedirectResponse {
        $this->authorize('update', $tag);

        $tag->update($request->validated());
        return redirect()->route('tags.index')->with('status', 'tag updated successfully.');
    }

    public function delete(Request $request, Tag $tag): RedirectResponse {
        $this->authorize('delete', $tag);

        $tag->articles()->detach();
        $tag->delete();

        return redirect()->route('tags.index')->with('status', 'tag deleted successfully.');
    }

    public function dashboard(Request $request, Tag $tag) {
        $articles = $tag->articles()->latest()->paginate(12);

        return view('tags.dashboard', ['tag' => $tag, 'articles' => $articles]);
    }
}
