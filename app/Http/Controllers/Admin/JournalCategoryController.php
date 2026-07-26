<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JournalCategory;
use Illuminate\Http\Request;

class JournalCategoryController extends Controller
{
    public function index()
    {
        $categories = JournalCategory::withCount('articles')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->paginate(20);

        return view('admin.journal-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.journal-categories.create');
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        JournalCategory::create($data);

        return redirect()
            ->route('admin.journal-categories.index')
            ->with('success', 'Category created.');
    }

    public function edit(JournalCategory $journalCategory)
    {
        return view('admin.journal-categories.edit', ['category' => $journalCategory]);
    }

    public function update(Request $request, JournalCategory $journalCategory)
    {
        $data = $this->validated($request, $journalCategory->id);
        $journalCategory->update($data);

        return redirect()
            ->route('admin.journal-categories.index')
            ->with('success', 'Category updated.');
    }

    public function destroy(JournalCategory $journalCategory)
    {
        $journalCategory->delete();

        return redirect()
            ->route('admin.journal-categories.index')
            ->with('success', 'Category deleted.');
    }

    private function validated(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'name' => 'required|string|max:150',
            'slug' => 'nullable|string|max:150|unique:journal_categories,slug' . ($ignoreId ? ",{$ignoreId}" : ''),
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer',
        ]);
    }
}
