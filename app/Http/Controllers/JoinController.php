<?php

namespace App\Http\Controllers;

use App\Models\JoinRequest;
use Illuminate\Http\Request;

/**
 * The "Join ORA" form on the public portal is a lightweight interest
 * form, not an account signup — it lands in a review queue that a
 * Super Admin works through, since joining the Association is a
 * separate step from creating a login. Anyone who already knows
 * which module they want and is ready for an account can use
 * /register directly instead (linked in the topbar).
 */
class JoinController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'module_id' => ['nullable', 'exists:modules,id'],
            'message' => ['nullable', 'string', 'max:2000'],
        ]);

        JoinRequest::create($data + ['status' => 'pending']);

        return redirect()
            ->route('portal')
            ->with('join_success', "Thanks for reaching out — we'll be in touch soon.");
    }
}
