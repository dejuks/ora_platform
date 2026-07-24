<?php

namespace App\Http\Controllers\Journal;

use App\Http\Controllers\Controller;
use App\Models\JournalSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * The Journal Manager sets the Article Processing Charge — amount and
 * currency — that gets stamped onto every manuscript the moment an
 * Editor-in-Chief accepts it (see ManuscriptController::decide()) and
 * that's charged through Chapa (see PaymentController). This does not
 * gate the workflow decisions themselves; those stay with
 * 'make-final-decision'.
 */
class SettingsController extends Controller
{
    public function edit()
    {
        $this->authorizeSettings();

        $settings = JournalSetting::current();

        return view('modules.journal.settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $this->authorizeSettings();

        $data = $request->validate([
            'publication_fee' => ['required', 'numeric', 'min:0', 'max:100000'],
            'currency' => ['required', 'string', 'max:8'],
        ]);

        $data['updated_by'] = Auth::id();

        $settings = JournalSetting::current();
        $settings->update($data);

        return redirect()
            ->route('journal.settings.edit')
            ->with('success', 'Payment settings updated. This applies to every manuscript accepted from now on.');
    }

    protected function authorizeSettings(): void
    {
        abort_unless(
            Auth::user()->hasModulePermission('journal', 'manage-settings'),
            403,
            'You do not have permission to do this.'
        );
    }
}
