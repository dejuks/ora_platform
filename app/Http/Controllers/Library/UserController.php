<?php

namespace App\Http\Controllers\Library;

use App\Http\Controllers\ModuleAdmin\BaseModuleUserController;
use App\Models\LibraryBranch;
use App\Models\User;

class UserController extends BaseModuleUserController
{
    protected string $moduleCode = 'library';

    protected function extraValidationRules(): array
    {
        return [
            'branch_id' => ['nullable', 'integer', 'exists:library_branches,id'],
        ];
    }

    protected function extraIndexRelations(): array
    {
        return ['libraryBranches'];
    }

    protected function extraFormData(?User $user = null): array
    {
        return [
            'branches' => LibraryBranch::active()->orderBy('name')->get(),
            'assignedBranchId' => $user?->libraryBranch()?->id,
        ];
    }

    protected function extraShowData(User $user): array
    {
        return [
            'branch' => $user->libraryBranch(),
        ];
    }

    /**
     * One branch per user (see library/admin/users): a branch_id
     * present in the request replaces whatever was assigned before;
     * an empty one clears it, leaving the user unscoped (every
     * branch) per User::accessibleLibraryBranchIds().
     */
    protected function afterUserSaved(User $user, array $data): void
    {
        $branchId = $data['branch_id'] ?? null;

        $user->libraryBranches()->sync(
            $branchId ? [$branchId => ['assigned_by' => auth()->id()]] : []
        );
    }
}