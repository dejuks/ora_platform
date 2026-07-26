<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\Controller;
use App\Models\JournalCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Journal Manager: configure the fixed list of categories (Fiction,
 * Literature, Science, ...) that an Author picks from when
 * submitting a manuscript, and that visitors filter the public
 * article listing by.
 */
class CategoryController extends Controller
{
    public function index()
    {
        $this->authorizePermission('manage-categories');

        $categories = JournalCategory::withCount('manuscripts')->ordered()->paginate(20);

        return view('modules.journal.categories.index', compact('categories'));
    }

    public function create()
    {
        $this->authorizePermission('manage-categories');

        return view('modules.journal.categories.create');
    }

    public function store(Request $request)
    {
        $this->authorizePermission('manage-categories');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:journal_categories,name'],
            'description' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        JournalCategory::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $request->boolean('is_active', true),
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('journal.categories.index')
            ->with('success', 'Category created.');
    }

    public function edit(JournalCategory $category)
    {
        $this->authorizePermission('manage-categories');

        return view('modules.journal.categories.edit', compact('category'));
    }

    public function update(Request $request, JournalCategory $category)
    {
        $this->authorizePermission('manage-categories');

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:journal_categories,name,'.$category->id],
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
            ->route('journal.categories.index')
            ->with('success', 'Category updated.');
    }

    public function destroy(JournalCategory $category)
    {
        $this->authorizePermission('manage-categories');

        // Manuscripts keep their content, they just lose this one
        // category tag — same non-destructive pattern as Wiki.
        $category->manuscripts()->update(['category_id' => null]);
        $category->delete();

        return redirect()
            ->route('journal.categories.index')
            ->with('success', 'Category deleted.');
    }

    protected function authorizePermission(string $permission): void
    {
        abort_unless(
            Auth::user()->hasModulePermission('journal', $permission),
            403,
            'You do not have permission to do this.'
        );
    }
}
