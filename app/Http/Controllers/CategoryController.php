<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Models\Category;
use App\Http\Requests\Categories\StoreCategoryRequest;
use App\Http\Requests\Categories\UpdateCategoryRequest;

class CategoryController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request) {
        $categories = Category::latest()->paginate(12);
        return view('categories.index', ['categories' => $categories]);
    }

    public function show(Request $request, Category $category) {
        return view('categories.show', ['category' => $category]);
    }

    public function create(Request $request) {
        $this->authorize(auth()->user());

        $categories = Category::orderBy('name', 'asc')->get();
        return view('categories.create', ['categories' => $categories]);
    }

    public function edit(Request $request, Category $category) {
        $this->authorize('update', $category);

        $categories = Category::orderBy('name', 'asc')->get();
        return view('categories.edit', ['category' => $category, 'categories' => $categories]);
    }

    public function store(StoreCategoryRequest $request): RedirectResponse {
        $this->authorize(auth()->user());

        Category::create($request->validated());
        return redirect()->route('categories.index')->with('status', 'category created successfully.');
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse {
        $this->authorize('update', $category);

        $category->update($request->validated());
        return redirect()->route('categories.index')->with('status', 'category updated successfully.');
    }

    public function delete(Request $request, Category $category): RedirectResponse {
        $this->authorize('delete', $category);

        $category->delete();

        return redirect()->route('categories.index')->with('status', 'category deleted successfully.');
    }

    public function dashboard(Request $request, Category $category) {
        $articles = $category->articles()->latest()->paginate(12);

        return view('categories.dashboard', ['category' => $category, 'articles' => $articles]);
    }
}
