<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\PasswordUpdateRequest;
use App\Models\User;
use App\Services\Auth\AuthCredentialStore;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class SecurityController extends Controller
{
    public function __construct(
        private readonly AuthCredentialStore $credentialStore,
    ) {}

    /**
     * Show the user's security settings page.
     */
    public function edit(): Response
    {
        return Inertia::render(
            'settings/security',
            [
                'passwordRules' => Password::defaults()
                    ->toPasswordRulesString(),
            ],
        );
    }

    /**
     * Update the user's password.
     */
    public function update(
        PasswordUpdateRequest $request,
    ): RedirectResponse {
        $user = $request->user();

        abort_unless(
            $user instanceof User,
            403,
        );

        $this->credentialStore->updatePassword(
            $user,
            $request
                ->string('password')
                ->toString(),
        );

        Inertia::flash(
            'toast',
            [
                'type' => 'success',
                'message' => 'Kata sandi berhasil diperbarui.',
            ],
        );

        return back();
    }
}
