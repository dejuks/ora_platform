<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Role;
use App\Models\User;
use App\Models\UserModuleRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Public, self-service registration for Journal authors. Anyone can
 * create an account here — no invitation from a Super Admin needed —
 * and is enrolled straight into the journal module with the "Author"
 * role, so they can submit a manuscript immediately after signing in.
 */
class RegisterController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'status' => 'Active',
        ]);

        $this->enrollAsJournalAuthor($user);
        $this->enrollAsResearcherMember($user);

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('dashboard')
            ->with('success', 'Welcome! Your account is ready — you can submit a manuscript and start networking with other researchers now.');
    }

    /**
     * Grant every new self-registered user the "Researcher / Member"
     * role inside the Researcher Network module, and create an empty
     * profile for them to fill in — anyone can register and start
     * networking immediately, no invitation needed.
     */
    protected function enrollAsResearcherMember(User $user): void
    {
        $module = Module::where('code', 'researcher')->first();

        if (! $module) {
            return;
        }

        $memberRole = Role::where('module_id', $module->id)
            ->where('slug', 'network-member')
            ->first();

        if ($memberRole) {
            UserModuleRole::create([
                'user_id' => $user->id,
                'module_id' => $module->id,
                'role_id' => $memberRole->id,
                'is_active' => true,
            ]);
        }

        $user->researcherProfile()->create([]);
    }

    /**
     * Grant every new self-registered user the "Author" role inside
     * the Journal module, so /journal/manuscripts opens up to them
     * without a Super Admin having to assign anything manually.
     */
    protected function enrollAsJournalAuthor(User $user): void
    {
        $module = Module::where('code', 'journal')->first();

        if (! $module) {
            return;
        }

        $authorRole = Role::where('module_id', $module->id)
            ->where('slug', 'author')
            ->first();

        if (! $authorRole) {
            return;
        }

        UserModuleRole::create([
            'user_id' => $user->id,
            'module_id' => $module->id,
            'role_id' => $authorRole->id,
            'is_active' => true,
        ]);
    }
}
