<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\User;
use App\Services\ModuleEnrollmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Public, self-service registration — one account for the whole ORA
 * platform. The visitor picks which modules they want on the form
 * (Journal, Ebook, Library, Researcher Network, Wiki, Repository),
 * and is enrolled into each with that module's entry-level role,
 * active immediately — no invitation or approval needed.
 *
 * This replaces the old split between the shared /register (which
 * only ever enrolled Journal + Researcher, hardcoded) and the
 * separate Researcher\RegisterController. Both signup paths now
 * flow through here.
 */
class RegisterController extends Controller
{
    public function __construct(protected ModuleEnrollmentService $enrollment)
    {
    }

    public function showRegister()
    {
        $modules = Module::selfRegisterable()->orderBy('name')->get();

        return view('auth.register', compact('modules'));
    }

    public function register(Request $request)
    {
        $selfRegisterableCodes = Module::selfRegisterable()->pluck('code')->all();

        $data = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:30', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'modules' => ['required', 'array', 'min:1'],
            'modules.*' => ['string', 'in:' . implode(',', $selfRegisterableCodes)],
        ], [
            'modules.required' => 'Pick at least one area to join.',
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

        $joined = $this->enrollment->enrollMany($user, $data['modules']);

        Auth::login($user);
        $request->session()->regenerate();

        $user->sendEmailVerificationNotification();

        $names = collect($joined)->pluck('name')->implode(', ');

        return redirect()
            ->route('verification.notice')
            ->with('success', "Welcome! You're enrolled in: {$names}. Verify your email to get started.");
    }
}
