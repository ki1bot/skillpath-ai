<?php

namespace App\Actions\Fortify;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        $input['name'] = trim((string) ($input['name'] ?? ''));
        $input['email'] = Str::lower(
            trim((string) ($input['email'] ?? '')),
        );

        Validator::make(
            $input,
            [
                ...$this->profileRules(),
                'email' => [
                    'required',
                    'string',
                    'email:rfc,dns',
                    'max:255',
                    Rule::unique(User::class),
                ],
                'password' => $this->passwordRules(),
            ],
            [
                'email.required' => 'Alamat email wajib diisi.',
                'email.email' => 'Email tersebut tidak valid atau domain email tidak terdaftar.',
                'email.unique' => 'Email tersebut sudah terdaftar pada SkillPath AI.',
            ],
        )->validate();

        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => $input['password'],
            'role' => 'student',
        ]);
    }
}
