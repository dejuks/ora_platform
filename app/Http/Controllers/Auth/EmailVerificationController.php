<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Every user must verify their email before the rest of the platform
 * opens up — the 'verified' middleware (see routes/web.php) sends
 * anyone who isn't verified yet here instead of wherever they were
 * trying to go. A Super Admin can turn this requirement off entirely
 * from Settings (see SystemSetting), in which case this page is
 * unreachable through normal navigation.
 */
class EmailVerificationController extends Controller
{
    /**
     * The "check your inbox" holding page.
     */
    public function notice()
    {
        if (! SystemSetting::current()->require_email_verification) {
            return redirect()->route('dashboard');
        }

        return Auth::user()->hasVerifiedEmail()
            ? redirect()->route('dashboard')
            : view('auth.verify-email');
    }

    /**
     * The signed link from inside the verification email itself.
     */
    public function verify(EmailVerificationRequest $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard')->with('success', 'Your email is already verified.');
        }

        $request->fulfill();

        return redirect()->route('dashboard')->with('success', 'Email verified — welcome aboard!');
    }

    /**
     * "Resend verification email" button on the holding page.
     */
    public function resend(Request $request)
    {
        if ($request->user()->hasVerifiedEmail()) {
            return redirect()->route('dashboard');
        }

        $request->user()->sendEmailVerificationNotification();

        return back()->with('status', 'verification-link-sent');
    }
}
