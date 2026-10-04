<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use App\Services\Auth\AuthCredentialStore;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Throwable;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    public function __construct(
        private readonly AuthCredentialStore $credentialStore,
    ) {}

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        $input['name'] = trim(
            (string) ($input['name'] ?? ''),
        );

        $input['email'] = Str::lower(
            trim(
                (string) ($input['email'] ?? ''),
            ),
        );

        Validator::make(
            $input,
            [
                ...$this->profileRules(),
                'password' => $this->passwordRules(),
            ],
            [
                'email.required' => 'Alamat email wajib diisi.',
                'email.email' => 'Format alamat email tidak valid.',
                'email.unique' => 'Email tersebut sudah terdaftar pada SkillPath AI.',
            ],
        )->validate();

        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $this->credentialStore->usesMongo()
                ? Str::random(64)
                : $input['password'],
            'role' => 'student',
        ]);

        try {
            $this->credentialStore->createForUser(
                $user,
                $input['password'],
            );
        } catch (Throwable $exception) {
            $user->delete();

            throw $exception;
        }

        return $user;
    }
}
