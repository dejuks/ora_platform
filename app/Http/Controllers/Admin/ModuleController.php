<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Module\StoreModuleRequest;
use App\Http\Requests\Admin\Module\UpdateModuleRequest;
use App\Models\Module;
use Illuminate\Support\Str;

class ModuleController extends Controller
{
    /**
     * Display a listing of modules.
     */
    public function index()
    {
        $modules = Module::latest()->paginate(15);

        return view('admin.modules.index', compact('modules'));
    }

    /**
     * Show the form for creating a new module.
     */
    public function create()
    {
        return view('admin.modules.create');
    }

    /**
     * Store a newly created module.
     */
    public function store(StoreModuleRequest $request)
    {
        $data = $request->validated();

        $data['slug'] = Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['created_by'] = auth()->id();

        Module::create($data);

        return redirect()
            ->route('admin.modules.index')
            ->with('success', 'Module created successfully.');
    }

    /**
     * Display the specified module.
     */
    public function show(Module $module)
    {
        return view('admin.modules.show', compact('module'));
    }

    /**
     * Show the form for editing the specified module.
     */
    public function edit(Module $module)
    {
        return view('admin.modules.edit', compact('module'));
    }

    /**
     * Update the specified module.
     */
    public function update(UpdateModuleRequest $request, Module $module)
    {
        $data = $request->validated();

        $data['slug'] = Str::slug($data['name']);
        $data['is_active'] = $request->boolean('is_active');
        $data['updated_by'] = auth()->id();

        $module->update($data);

        return redirect()
            ->route('admin.modules.index')
            ->with('success', 'Module updated successfully.');
    }

    /**
     * Remove the specified module.
     */
    public function destroy(Module $module)
    {
        // Prevent deletion if users are assigned
        if ($module->users()->exists()) {
            return redirect()
                ->route('admin.modules.index')
                ->with('error', 'This module cannot be deleted because users are assigned to it.');
        }

        $module->delete();

        return redirect()
            ->route('admin.modules.index')
            ->with('success', 'Module deleted successfully.');
    }

    /**
     * Change module status.
     */
    public function toggleStatus(Module $module)
    {
        $module->update([
            'is_active' => ! $module->is_active,
            'updated_by' => auth()->id(),
        ]);

        return back()->with(
            'success',
            'Module status updated successfully.'
        );
    }
}