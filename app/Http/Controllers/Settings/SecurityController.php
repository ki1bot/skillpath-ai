<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\PasswordUpdateRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;

class SecurityController extends Controller
{
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

        $user->forceFill([
            'password' => $request
                ->string('password')
                ->toString(),
        ])->save();

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
