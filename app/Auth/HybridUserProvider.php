<?php

namespace App\Auth;

use App\Models\User;
use App\Services\Auth\AuthCredentialStore;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Str;

class HybridUserProvider implements UserProvider
{
    public function __construct(
        private readonly Hasher $hasher,
        private readonly AuthCredentialStore $credentialStore,
    ) {}

    public function retrieveById($identifier): ?Authenticatable
    {
        if (! is_int($identifier) && ! is_string($identifier)) {
            return null;
        }

        return User::query()
            ->whereKey($identifier)
            ->first();
    }

    public function retrieveByToken(
        $identifier,
        $token,
    ): ?Authenticatable {
        $user = $this->retrieveById($identifier);

        if (! $user instanceof User || $token === '') {
            return null;
        }

        $rememberToken = (string) $user->getRememberToken();

        if ($rememberToken === '') {
            return null;
        }

        return hash_equals(
            $rememberToken,
            $token,
        ) ? $user : null;
    }

    public function updateRememberToken(
        Authenticatable $user,
        $token,
    ): void {
        if (! $user instanceof User) {
            return;
        }

        $user->setRememberToken($token);

        $timestamps = $user->timestamps;

        $user->timestamps = false;
        $user->save();
        $user->timestamps = $timestamps;
    }

    /**
     * @param  array<string, mixed>  $credentials
     */
    public function retrieveByCredentials(
        array $credentials,
    ): ?Authenticatable {
        $email = Str::lower(
            trim(
                (string) ($credentials['email'] ?? ''),
            ),
        );

        if ($email === '') {
            return null;
        }

        if ($this->credentialStore->usesMongo()) {
            $identity = $this->credentialStore
                ->findIdentityByEmail($email);

            $userId = $identity?->getAttribute(
                'user_id',
            );

            if (
                ! is_int($userId)
                && ! is_numeric($userId)
            ) {
                return null;
            }

            return User::query()
                ->whereKey((int) $userId)
                ->first();
        }

        return User::query()
            ->whereRaw(
                'LOWER(email) = ?',
                [$email],
            )
            ->first();
    }

    /**
     * @param  array<string, mixed>  $credentials
     */
    public function validateCredentials(
        Authenticatable $user,
        array $credentials,
    ): bool {
        if (! $user instanceof User) {
            return false;
        }

        $plainPassword = $credentials['password'] ?? null;

        if (
            ! is_string($plainPassword)
            || $plainPassword === ''
        ) {
            return false;
        }

        $passwordHash = $this->credentialStore
            ->passwordHashFor($user);

        return $passwordHash !== null
            && $this->hasher->check(
                $plainPassword,
                $passwordHash,
            );
    }

    /**
     * @param  array<string, mixed>  $credentials
     */
    public function rehashPasswordIfRequired(
        Authenticatable $user,
        array $credentials,
        bool $force = false,
    ): void {
        if (! $user instanceof User) {
            return;
        }

        $plainPassword = $credentials['password'] ?? null;

        if (
            ! is_string($plainPassword)
            || $plainPassword === ''
        ) {
            return;
        }

        $this->credentialStore
            ->rehashPasswordIfRequired(
                $user,
                $plainPassword,
                $force,
            );
    }
}
