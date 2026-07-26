<?php

namespace App\Http\Controllers\Wiki;

use App\Http\Controllers\Controller;
use App\Models\WikiCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Administrator (Sysop) / Bureaucrat: configure the fixed list of
 * categories (History, Fiction, Education, ...) that Registered
 * Editors pick from when writing an article.
 */
class CategoryController extends Controller
{
    public function index()
    {
        $this->authorizePermission('manage-categories');

        $categories = WikiCategory::withCount('articles')->ordered()->paginate(20);

        return view('modules.wiki.categories.index', compact('categories'));
    }

    public function create()
    {
        $this->authorizePermission('manage-categories');

        return view('modules.wiki.categories.create');
    }

    public function store(Request $request)
    {
        $this->authorizePermission('manage-categories');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:wiki_categories,name'],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        WikiCategory::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('wiki.categories.index')
            ->with('success', 'Category created.');
    }

    public function edit(WikiCategory $category)
    {
        $this->authorizePermission('manage-categories');

        return view('modules.wiki.categories.edit', compact('category'));
    }

    public function update(Request $request, WikiCategory $category)
    {
        $this->authorizePermission('manage-categories');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:wiki_categories,name,'.$category->id],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $category->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()
            ->route('wiki.categories.index')
            ->with('success', 'Category updated.');
    }

    public function destroy(WikiCategory $category)
    {
        $this->authorizePermission('manage-categories');

        // Detach rather than block deletion — articles keep their
        // content, they just lose this one category tag.
        $category->articles()->detach();
        $category->delete();

        return redirect()
            ->route('wiki.categories.index')
            ->with('success', 'Category deleted.');
    }

    protected function authorizePermission(string $permission): void
    {
        abort_unless(
            Auth::user()->hasModulePermission('wiki', $permission),
            403,
            'You do not have permission to do this.'
        );
    }
}
