<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Article;
use App\Models\Category;
use App\Models\Tag;
use App\Http\Requests\Articles\StoreArticleRequest;
use App\Http\Requests\Articles\UpdateArticleRequest;
use Illuminate\Support\Arr;

class ArticleController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request) {
        $articles = Article::latest()->paginate(12);
        $categories = Category::orderBy('name', 'asc')->get();

        return view('articles.index', ['articles' => $articles, 'categories' => $categories]);
    }

    public function show(Request $request, Article $article) {
        $article->views()->firstOrCreate([
            'ip_address' => $request->ip(),
            'viewed_at'  => now()->startOfHour(),
        ], [
            'user_agent' => $request->userAgent(),
        ]);

        return view('articles.show', ['article' => $article]);
    }

    public function create(Request $request) {
        $this->authorize(auth()->user());

        $categories = Category::orderBy('name', 'asc')->get();
        $tags = Tag::orderBy('name', 'asc')->get();
        return view('articles.create', ['categories' => $categories, 'tags' => $tags]);
    }

    public function edit(Request $request, Article $article) {
        $this->authorize('update', $article);

        $categories = Category::orderBy('name', 'asc')->get();
        $tags = Tag::orderBy('name', 'asc')->get();
        return view('articles.edit', ['article' => $article, 'categories' => $categories, 'tags' => $tags]);
    }

    public function store(StoreArticleRequest $request): RedirectResponse
    {
        $this->authorize('create', Article::class);

        $validated = $request->validated();

        $article = Article::create(Arr::except($validated, ['tags']));

        if (!empty($validated['tags'])) {
            $article->tags()->attach($validated['tags']);
        }

        return redirect()->route('articles.index')->with('status', 'article created successfully.');
    }

    public function update(UpdateArticleRequest $request, Article $article): RedirectResponse 
    {
        $this->authorize('update', $article);

        $validated = $request->validated();

        $article->update(Arr::except($validated, ['tags']));

        $article->tags()->sync($request->input('tags', []));

        return redirect()->route('articles.index')->with('status', 'article updated successfully.');
    }

    public function delete(Request $request, Article $article): RedirectResponse {
        $this->authorize('delete', $article);

        $article->delete();
        return redirect()->route('articles.index')->with('status', 'article deleted successfully.');
    }

    public function dashboard(Request $request, Article $article) {
        $article->loadCount('views');
        $comments = $article->comments()->latest()->paginate(3);
        
        return view('articles.dashboard', ['article' => $article, 'comments' => $comments]);
    }
}
