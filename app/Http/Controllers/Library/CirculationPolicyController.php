<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\LibraryCirculationPolicy;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * The Library Manager sets circulation-wide policy (loan period,
 * renewal limit, daily fine rate, hold expiry) — 'manage-circulation-policy'.
 * This does not gate day-to-day circulation actions themselves;
 * those stay with the Librarian's 'manage-circulation' permission.
 */
class CirculationPolicyController extends Controller
{
    public function edit()
    {
        abort_unless(
            Auth::user()->hasModulePermission('library', 'manage-circulation-policy'),
            403,
            'You do not have permission to do this.'
        );

        $policy = LibraryCirculationPolicy::current();

        return view('modules.library.policy.edit', compact('policy'));
    }

    public function update(Request $request)
    {
        abort_unless(
            Auth::user()->hasModulePermission('library', 'manage-circulation-policy'),
            403,
            'You do not have permission to do this.'
        );

        $data = $request->validate([
            'loan_period_days' => ['required', 'integer', 'min:1', 'max:90'],
            'max_renewals' => ['required', 'integer', 'min:0', 'max:10'],
            'fine_per_day' => ['required', 'numeric', 'min:0', 'max:100'],
            'hold_expiry_days' => ['required', 'integer', 'min:1', 'max:30'],
        ]);

        $data['updated_by'] = Auth::id();

        $policy = LibraryCirculationPolicy::current();
        $policy->update($data);

        return redirect()
            ->route('library.policy.edit')
            ->with('success', 'Circulation policy updated.');
    }
}
