<?php

namespace App\Http\Controllers\Ebook;

use App\Http\Controllers\Controller;
use App\Models\EbookSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * The Book Editor sets the Book Processing Charge — amount and
 * currency — that gets stamped onto every book the moment it's
 * accepted (see BookController::decide()) and that's charged through
 * Chapa (see PaymentController). This does not gate the editorial
 * decision itself; that stays with 'make-editorial-decision'.
 */
class SettingsController extends Controller
{
    public function edit()
    {
        $this->authorizeSettings();

        $settings = EbookSetting::current();

        return view('modules.ebook.settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $this->authorizeSettings();

        $data = $request->validate([
            'processing_fee' => ['required', 'numeric', 'min:0', 'max:100000'],
            'currency' => ['required', 'string', 'max:8'],
        ]);

        $data['updated_by'] = Auth::id();

        $settings = EbookSetting::current();
        $settings->update($data);

        return redirect()
            ->route('ebook.settings.edit')
            ->with('success', 'Payment settings updated. This applies to every book accepted from now on.');
    }

    protected function authorizeSettings(): void
    {
        abort_unless(
            Auth::user()->hasModulePermission('ebook', 'manage-settings'),
            403,
            'You do not have permission to do this.'
        );
    }
}
