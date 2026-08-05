<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Notifications\AppNotification;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

/**
 * "My Profile" — the current user's own details, avatar, and password.
 * Reachable from the top-right account menu on every page.
 */
class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();

        // "My Modules" card on the profile page: every module this
        // user actually holds an active role in — not just the
        // self-registerable ones (an admin-assigned module still
        // counts as "joined" here), matching what My Modules ->
        // Manage Modules shows as already joined.
        $joinedModuleIds = $user->moduleRoles()
            ->get()
            ->pluck('pivot.module_id')
            ->unique();

        $joinedModules = Module::whereIn('id', $joinedModuleIds)
            ->active()
            ->orderBy('name')
            ->get();

        // How many more self-registerable modules are still available
        // to join, for the "N more modules available" prompt below.
        $availableModulesCount = Module::selfRegisterable()
            ->whereNotIn('id', $joinedModuleIds)
            ->count();

        return view('account.profile', compact('user', 'joinedModules', 'availableModulesCount'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'phone' => ['nullable', 'string', 'max:30', Rule::unique('users', 'phone')->ignore($user->id)],
            'gender' => ['nullable', 'in:Male,Female'],
            'date_of_birth' => ['nullable', 'date'],
        ]);

        $user->update($validated);

        ActivityLogger::log('profile.updated', 'Updated profile details');

        $user->notify(new AppNotification(
            title: 'Profile updated',
            message: 'Your profile details were updated successfully.',
            url: route('account.profile.edit'),
            icon: 'bi-person-check',
            type: 'success',
        ));

        return back()->with('status', 'Profile updated successfully.');
    }

    public function updatePhoto(Request $request)
    {
        $request->validate([
            'profile_photo' => ['required', 'image', 'max:2048'],
        ]);

        $user = Auth::user();

        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $path = $request->file('profile_photo')->store('profile-photos', 'public');

        $user->update(['profile_photo' => $path]);

        ActivityLogger::log('profile.photo_updated', 'Updated profile photo');

        return back()->with('status', 'Profile photo updated.');
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        if (! Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'The current password is incorrect.']);
        }

        $user->update(['password' => Hash::make($request->password)]);

        ActivityLogger::log('password.changed', 'Changed account password');

        $user->notify(new AppNotification(
            title: 'Password changed',
            message: 'Your account password was changed. If this was not you, contact an administrator immediately.',
            icon: 'bi-shield-lock',
            type: 'warning',
        ));

        return back()->with('status', 'Password changed successfully.');
    }
}
