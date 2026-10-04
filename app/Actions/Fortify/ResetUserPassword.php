<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Models\User;
use App\Services\Auth\AuthCredentialStore;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\ResetsUserPasswords;

class ResetUserPassword implements ResetsUserPasswords
{
    use PasswordValidationRules;

    public function __construct(
        private readonly AuthCredentialStore $credentialStore,
    ) {}

    /**
     * Validate and reset the user's forgotten password.
     *
     * @param  array<string, string>  $input
     */
    public function reset(User $user, array $input): void
    {
        Validator::make(
            $input,
            [
                'password' => $this->passwordRules(),
            ],
        )->validate();

        $this->credentialStore->updatePassword(
            $user,
            $input['password'],
        );
    }
}
