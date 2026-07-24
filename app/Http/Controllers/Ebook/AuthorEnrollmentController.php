<?php

namespace App\Http\Controllers\Ebook;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Role;
use App\Models\UserModuleRole;
use Illuminate\Support\Facades\Auth;

/**
 * Any logged-in ORA user (already registered via the platform's
 * shared /register page, or added by a Super Admin) can self-enroll
 * as an eBook Author with one click — no separate ebook-specific
 * registration form needed, and the shared RegisterController stays
 * untouched.
 */
class AuthorEnrollmentController extends Controller
{
    public function enroll()
    {
        $user = Auth::user();

        if ($user->hasModulePermission('ebook', 'submit-manuscript')) {
            return redirect()->route('ebook.books.index');
        }

        $module = Module::where('code', 'ebook')->firstOrFail();

        $authorRole = Role::where('module_id', $module->id)
            ->where('slug', 'ebook-author')
            ->firstOrFail();

        UserModuleRole::firstOrCreate(
            ['user_id' => $user->id, 'module_id' => $module->id, 'role_id' => $authorRole->id],
            ['is_active' => true]
        );

        return redirect()
            ->route('ebook.books.create')
            ->with('success', 'You are now an eBook Author. You can submit a manuscript right away.');
    }
}
