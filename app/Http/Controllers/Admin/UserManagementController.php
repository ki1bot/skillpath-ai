<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AccountDeletedMail;
use App\Models\User;
use App\Services\Auth\AuthCredentialStore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class UserManagementController extends Controller
{
    public function __construct(
        private readonly AuthCredentialStore $credentialStore,
    ) {}

    public function index(Request $request): Response
    {
        $manager = $request->user();

        abort_unless(
            $manager instanceof User
            && $manager->canManageUsers(),
            403,
        );

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
                'managerId' => (int) $manager->getKey(),
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

        $manager = $request->user();

        if (
            ! $manager instanceof User
            || ! $manager->canManageUsers()
        ) {
            abort(403);
        }

        if ($manager->is($user)) {
            throw ValidationException::withMessages([
                'role' => 'Role akun pengelola pengguna tidak dapat diubah dari halaman ini.',
            ]);
        }

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

    public function destroy(
        Request $request,
        User $user,
    ): RedirectResponse {
        $manager = $request->user();

        if (
            ! $manager instanceof User
            || ! $manager->canManageUsers()
        ) {
            abort(403);
        }

        if ($manager->is($user)) {
            throw ValidationException::withMessages([
                'delete' => 'Akun pengelola pengguna tidak dapat dihapus dari halaman ini.',
            ]);
        }

        $deletedUserId = (int) $user->getKey();
        $deletedUserName = (string) $user->name;
        $deletedUserEmail = (string) $user->email;

        Cache::forget(
            'email-verification:'.$deletedUserId,
        );

        Cache::forget(
            'email-verification-resend:'.$deletedUserId,
        );

        $user->delete();

        $credentialDeleted = true;

        try {
            $this->credentialStore->deleteForUser(
                $user,
            );
        } catch (Throwable $exception) {
            report($exception);

            $credentialDeleted = false;
        }

        $notificationSent = true;

        try {
            Mail::to(
                $deletedUserEmail,
            )->send(
                new AccountDeletedMail(
                    $deletedUserName,
                    $deletedUserEmail,
                ),
            );
        } catch (Throwable $exception) {
            report($exception);

            $notificationSent = false;
        }

        if (
            ! $credentialDeleted
            || ! $notificationSent
        ) {
            $issues = [];

            if (! $credentialDeleted) {
                $issues[] = 'credential MongoDB gagal dibersihkan';
            }

            if (! $notificationSent) {
                $issues[] = 'email pemberitahuan gagal dikirim';
            }

            Inertia::flash(
                'toast',
                [
                    'type' => 'warning',
                    'message' => "Akun {$deletedUserName} telah dihapus, tetapi ".implode(
                        ' dan ',
                        $issues,
                    ).'.',
                ],
            );

            return back();
        }

        Inertia::flash(
            'toast',
            [
                'type' => 'success',
                'message' => "Akun {$deletedUserName} berhasil dihapus dan email pemberitahuan telah dikirim.",
            ],
        );

        return back();
    }
}
