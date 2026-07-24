<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\Controller;
use App\Models\LibraryMember;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Patron records for the physical circulation desk. Every ORA user
 * with the 'library-member' role can be enrolled here with a
 * membership number and a loan limit before they can actually
 * borrow anything — enrollment and record-keeping is the
 * Librarian's (manage-circulation) job at the front desk.
 */
class MemberController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizePermission('manage-circulation');

        $query = LibraryMember::with('user')->withCount(['loans as active_loans_count' => fn ($q) => $q->where('status', 'active')])->latest();

        if ($search = $request->get('q')) {
            $query->where('membership_no', 'like', "%{$search}%")
                ->orWhereHas('user', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
        }

        $members = $query->paginate(15)->withQueryString();

        return view('modules.library.members.index', compact('members'));
    }

    public function create()
    {
        $this->authorizePermission('manage-circulation');

        $users = User::whereDoesntHave('libraryMember')->orderBy('first_name')->orderBy('last_name')->get();

        return view('modules.library.members.create', compact('users'));
    }

    public function store(Request $request)
    {
        $this->authorizePermission('manage-circulation');

        $data = $request->validate([
            'user_id' => ['required', 'exists:users,id', 'unique:library_members,user_id'],
            'member_type' => ['required', 'in:student,staff,faculty,external'],
            'max_active_loans' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        $data['membership_no'] = 'LM-'.now()->format('y').'-'.strtoupper(Str::random(5));
        $data['status'] = 'active';
        $data['joined_at'] = now();
        $data['created_by'] = Auth::id();

        $member = LibraryMember::create($data);

        return redirect()
            ->route('library.members.show', $member)
            ->with('success', 'Member enrolled: '.$member->membership_no);
    }

    public function show(LibraryMember $member)
    {
        $this->authorizeSelfOrStaff($member);

        $member->load([
            'user',
            'loans.copy.book',
            'holds.book',
            'fines.loan.copy.book',
        ]);

        return view('modules.library.members.show', compact('member'));
    }

    public function edit(LibraryMember $member)
    {
        $this->authorizePermission('manage-circulation');

        return view('modules.library.members.edit', compact('member'));
    }

    public function update(Request $request, LibraryMember $member)
    {
        $this->authorizePermission('manage-circulation');

        $data = $request->validate([
            'member_type' => ['required', 'in:student,staff,faculty,external'],
            'status' => ['required', 'in:active,suspended,expired'],
            'max_active_loans' => ['required', 'integer', 'min:1', 'max:20'],
        ]);

        $data['updated_by'] = Auth::id();

        $member->update($data);

        return redirect()
            ->route('library.members.show', $member)
            ->with('success', 'Member record updated.');
    }

    /*
    |--------------------------------------------------------------------------
    | Authorization helpers
    |--------------------------------------------------------------------------
    */

    protected function authorizePermission(string $permission): void
    {
        abort_unless(
            Auth::user()->hasModulePermission('library', $permission),
            403,
            'You do not have permission to do this.'
        );
    }

    /**
     * A member can view their own record (loans, holds, fines);
     * front-desk staff can view anyone's.
     */
    protected function authorizeSelfOrStaff(LibraryMember $member): void
    {
        $user = Auth::user();

        if ($member->user_id === $user->id) {
            return;
        }

        abort_unless(
            $user->isSuperAdmin() || $user->hasModulePermission('library', 'manage-circulation'),
            403,
            'You do not have permission to view this member record.'
        );
    }
}
