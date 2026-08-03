<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\LibraryPricingPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Library Manager (manage-settings): full CRUD over the fee rules a
 * Digital Librarian can attach to a paid resource (see
 * LibraryDigitalResource::pricing_plan_id). Mirrors
 * Ebook\CategoryController / Journal\CategoryController's shape —
 * a plain admin-managed list, gated by the module's own
 * 'manage-settings' permission (already held by library-manager)
 * rather than a new permission.
 */
class PricingPlanController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeSettings();

        $query = LibraryPricingPlan::withCount(['resources', 'purchases'])->latest();

        if ($request->filled('resource_type')) {
            $query->where('resource_type', $request->get('resource_type'));
        }

        $plans = $query->paginate(20)->withQueryString();

        return view('modules.library.pricing-plans.index', [
            'plans' => $plans,
            'resourceTypes' => LibraryPricingPlan::RESOURCE_TYPES,
        ]);
    }

    public function create()
    {
        $this->authorizeSettings();

        return view('modules.library.pricing-plans.create', [
            'resourceTypes' => LibraryPricingPlan::RESOURCE_TYPES,
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeSettings();

        $data = $this->validated($request);

        $data['slug'] = LibraryPricingPlan::uniqueSlug($data['name']);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['created_by'] = Auth::id();

        LibraryPricingPlan::create($data);

        return redirect()
            ->route('library.pricing-plans.index')
            ->with('success', 'Pricing plan created.');
    }

    public function edit(LibraryPricingPlan $pricingPlan)
    {
        $this->authorizeSettings();

        return view('modules.library.pricing-plans.edit', [
            'plan' => $pricingPlan,
            'resourceTypes' => LibraryPricingPlan::RESOURCE_TYPES,
        ]);
    }

    public function update(Request $request, LibraryPricingPlan $pricingPlan)
    {
        $this->authorizeSettings();

        $data = $this->validated($request, $pricingPlan->id);

        if ($data['name'] !== $pricingPlan->name) {
            $data['slug'] = LibraryPricingPlan::uniqueSlug($data['name'], $pricingPlan->id);
        }

        $data['is_active'] = $request->boolean('is_active', true);
        $data['updated_by'] = Auth::id();

        $pricingPlan->update($data);

        return redirect()
            ->route('library.pricing-plans.index')
            ->with('success', 'Pricing plan updated.');
    }

    /**
     * Non-destructive, same pattern as Ebook/Journal category
     * deletion — resources that used this plan simply fall back to
     * free (pricing_plan_id nulls out via the FK's nullOnDelete)
     * rather than the deletion being blocked or the resource breaking.
     */
    public function destroy(LibraryPricingPlan $pricingPlan)
    {
        $this->authorizeSettings();

        $pricingPlan->delete();

        return redirect()
            ->route('library.pricing-plans.index')
            ->with('success', 'Pricing plan deleted. Resources that used it are now free until reassigned.');
    }

    protected function validated(Request $request, ?int $ignoreId = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150', 'unique:library_pricing_plans,name'.($ignoreId ? ",{$ignoreId}" : '')],
            'description' => ['nullable', 'string', 'max:1000'],
            'resource_type' => ['nullable', 'in:ebook,journal_article,paper,other'],
            'amount' => ['required', 'numeric', 'min:0', 'max:100000'],
            'currency' => ['required', 'string', 'max:8'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['resource_type'] = $data['resource_type'] ?: null;

        return $data;
    }

    protected function authorizeSettings(): void
    {
        abort_unless(
            Auth::user()->hasModulePermission('library', 'manage-settings'),
            403,
            'You do not have permission to do this.'
        );
    }
}
