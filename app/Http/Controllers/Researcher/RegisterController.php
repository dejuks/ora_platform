<?php

namespace App\Http\Controllers\Researcher;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Role;
use App\Models\User;
use App\Models\UserModuleRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Public, self-service registration for the Researchers' Network.
 * Anyone can create an account here — no invitation from a Super
 * Admin needed — and is enrolled straight into the researcher
 * module with the "Researcher / Member" role, with an empty profile
 * created automatically for them to fill in from /researcher/profile.
 */
class RegisterController extends Controller
{
    public function showRegister()
    {
        return view('modules.researcher.public.register');
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

        $this->enrollAsResearcherMember($user);

        Auth::login($user);
        $request->session()->regenerate();

        $user->sendEmailVerificationNotification();

        return redirect()
            ->route('verification.notice')
            ->with('success', 'Welcome to the Researchers\' Network! Verify your email to get started.');
    }

    /**
     * Grant the new user the "Researcher / Member" role inside the
     * Researcher Network module, and create an empty profile for
     * them to fill in — mirrors the migration's own intent: created
     * automatically the moment a user gets access to the module.
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
            UserModuleRole::firstOrCreate(
                ['user_id' => $user->id, 'module_id' => $module->id, 'role_id' => $memberRole->id],
                ['is_active' => true]
            );
        }

        $user->researcherProfile()->create([]);
    }
}
