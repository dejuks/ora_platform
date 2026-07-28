<?php

namespace App\Http\Controllers\Ebook;

use App\Http\Controllers\Controller;
use App\Models\BookCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Book Editor: configure the fixed list of categories (Fiction,
 * Literature, Science, ...) that an Author picks from when
 * submitting a book, and that visitors filter the public eBook
 * listing by. Mirrors Journal\CategoryController exactly.
 */
class CategoryController extends Controller
{
    public function index()
    {
        $this->authorizePermission('manage-categories');

        $categories = BookCategory::withCount('books')->ordered()->paginate(20);

        return view('modules.ebook.categories.index', compact('categories'));
    }

    public function create()
    {
        $this->authorizePermission('manage-categories');

        return view('modules.ebook.categories.create');
    }

    public function store(Request $request)
    {
        $this->authorizePermission('manage-categories');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:ebook_categories,name'],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        BookCategory::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('ebook.categories.index')
            ->with('success', 'Category created.');
    }

    public function edit(BookCategory $category)
    {
        $this->authorizePermission('manage-categories');

        return view('modules.ebook.categories.edit', compact('category'));
    }

    public function update(Request $request, BookCategory $category)
    {
        $this->authorizePermission('manage-categories');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:ebook_categories,name,'.$category->id],
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
            ->route('ebook.categories.index')
            ->with('success', 'Category updated.');
    }

    public function destroy(BookCategory $category)
    {
        $this->authorizePermission('manage-categories');

        // Books keep their content, they just lose this one category
        // tag — same non-destructive pattern as Journal/Wiki.
        $category->books()->update(['category_id' => null]);
        $category->delete();

        return redirect()
            ->route('ebook.categories.index')
            ->with('success', 'Category deleted.');
    }

    protected function authorizePermission(string $permission): void
    {
        abort_unless(
            Auth::user()->hasModulePermission('ebook', $permission),
            403,
            'You do not have permission to do this.'
        );
    }
}
