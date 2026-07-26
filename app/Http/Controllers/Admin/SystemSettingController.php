<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SystemSettingController extends Controller
{
    public function edit()
    {
        $settings = SystemSetting::current();

        return view('admin.settings.edit', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'require_email_verification' => ['nullable', 'boolean'],
        ]);

        SystemSetting::current()->update([
            'require_email_verification' => $request->boolean('require_email_verification'),
            'updated_by' => Auth::id(),
        ]);

        return back()->with('success', 'Settings updated.');
    }
}
