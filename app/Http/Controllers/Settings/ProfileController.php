<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProfileDeleteRequest;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use App\Models\SocialAccount;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Show the user's profile settings page.
     */
    public function edit(Request $request): Response
    {
        $user = $request->user();

        abort_unless(
            $user instanceof User,
            403,
        );

        $connectedProviders = SocialAccount::query()
            ->where(
                'user_id',
                $user->id,
            )
            ->pluck(
                'provider',
            )
            ->all();

        return Inertia::render(
            'settings/profile',
            [
                'socialConnections' => [
                    'google' => in_array(
                        'google',
                        $connectedProviders,
                        true,
                    ),
                    'facebook' => in_array(
                        'facebook',
                        $connectedProviders,
                        true,
                    ),
                ],
            ],
        );
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->fill($request->validated());

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;

            Cache::forget(
                'email-verification:'.$user->id,
            );

            Cache::forget(
                'email-verification-resend:'.$user->id,
            );
        }

        $user->save();

        Inertia::flash('toast', [
            'type' => 'success',
            'message' => __('Profile updated.'),
        ]);

        return to_route('profile.edit');
    }

    /**
     * Delete the user's profile.
     */
    public function destroy(ProfileDeleteRequest $request): RedirectResponse
    {
        $user = $request->user();

        Auth::logout();

        Cache::forget(
            'email-verification:'.$user->id,
        );

        Cache::forget(
            'email-verification-resend:'.$user->id,
        );

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
