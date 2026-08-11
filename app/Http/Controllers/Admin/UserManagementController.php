<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserManagementController extends Controller
{
    public function index(): Response
    {
        $users = User::query()
            ->select([
                'id',
                'name',
                'email',
                'role',
            ])
            ->orderBy('id')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render(
            'admin/users',
            [
                'users' => $users,
            ],
        );
    }

    public function updateRole(
        Request $request,
        User $user,
    ): RedirectResponse {
        $request->validate([
            'role' => [
                'required',
                'string',
                'in:admin,student',
            ],
        ]);

        $role = $request
            ->string('role')
            ->toString();

        $user->update([
            'role' => $role,
        ]);

        Inertia::flash(
            'toast',
            [
                'type' => 'success',
                'message' => "Role {$user->name} berhasil diubah menjadi {$role}.",
            ],
        );

        return back();
    }
}
