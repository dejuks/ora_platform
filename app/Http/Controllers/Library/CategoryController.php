<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\LibraryCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Library Manager: configure the fixed list of categories (Fiction,
 * Literature, Science, ...) a catalog title is tagged with, and that
 * visitors filter the public library catalog by. Mirrors
 * Journal\CategoryController exactly.
 */
class CategoryController extends Controller
{
    public function index()
    {
        $this->authorizePermission('manage-categories');

        $categories = LibraryCategory::withCount('books')->ordered()->paginate(20);

        return view('modules.library.categories.index', compact('categories'));
    }

    public function create()
    {
        $this->authorizePermission('manage-categories');

        return view('modules.library.categories.create');
    }

    public function store(Request $request)
    {
        $this->authorizePermission('manage-categories');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:library_categories,name'],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        LibraryCategory::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('library.categories.index')
            ->with('success', 'Category created.');
    }

    public function edit(LibraryCategory $category)
    {
        $this->authorizePermission('manage-categories');

        return view('modules.library.categories.edit', compact('category'));
    }

    public function update(Request $request, LibraryCategory $category)
    {
        $this->authorizePermission('manage-categories');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:library_categories,name,'.$category->id],
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
            ->route('library.categories.index')
            ->with('success', 'Category updated.');
    }

    public function destroy(LibraryCategory $category)
    {
        $this->authorizePermission('manage-categories');

        // Titles keep their catalog record, they just lose this one
        // category tag — same non-destructive pattern as Journal/Wiki.
        $category->books()->update(['category_id' => null]);
        $category->delete();

        return redirect()
            ->route('library.categories.index')
            ->with('success', 'Category deleted.');
    }

    protected function authorizePermission(string $permission): void
    {
        abort_unless(
            Auth::user()->hasModulePermission('library', $permission),
            403,
            'You do not have permission to do this.'
        );
    }
}
