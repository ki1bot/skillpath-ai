<?php

namespace App\Services\Auth;

use App\Models\AuthIdentity;
use App\Models\User;
use Illuminate\Contracts\Hashing\Hasher;
use Illuminate\Support\Str;
use RuntimeException;

class AuthCredentialStore
{
    public function __construct(
        private readonly Hasher $hasher,
    ) {}

    public function usesMongo(): bool
    {
        return $this->driver() === 'mongodb';
    }

    public function findIdentityByEmail(string $email): ?AuthIdentity
    {
        if (! $this->usesMongo()) {
            return null;
        }

        return AuthIdentity::query()
            ->where('email', $this->normalizeEmail($email))
            ->first();
    }

    public function findIdentityByUserId(int $userId): ?AuthIdentity
    {
        if (! $this->usesMongo()) {
            return null;
        }

        return AuthIdentity::query()
            ->where('user_id', $userId)
            ->first();
    }

    public function passwordHashFor(User $user): ?string
    {
        if (! $this->usesMongo()) {
            $password = $user->getRawOriginal('password');

            return is_string($password) && $password !== ''
                ? $password
                : null;
        }

        $identity = $this->findIdentityByUserId(
            (int) $user->getKey(),
        );

        $passwordHash = $identity?->getAttribute(
            'password_hash',
        );

        return is_string($passwordHash) && $passwordHash !== ''
            ? $passwordHash
            : null;
    }

    public function createForUser(
        User $user,
        string $plainPassword,
    ): void {
        if (! $this->usesMongo()) {
            return;
        }

        AuthIdentity::query()->create([
            'user_id' => (int) $user->getKey(),
            'email' => $this->normalizeEmail(
                (string) $user->email,
            ),
            'password_hash' => $this->hasher->make(
                $plainPassword,
            ),
        ]);
    }

    public function ensureForUser(User $user): void
    {
        if (! $this->usesMongo()) {
            return;
        }

        $userId = (int) $user->getKey();

        $email = $this->normalizeEmail(
            (string) $user->email,
        );

        $identity = $this->findIdentityByUserId(
            $userId,
        );

        if ($identity !== null) {
            if (
                (string) $identity->getAttribute(
                    'email',
                ) !== $email
            ) {
                $identity->forceFill([
                    'email' => $email,
                ])->save();
            }

            return;
        }

        $passwordHash = $user->getRawOriginal(
            'password',
        );

        if (
            ! is_string($passwordHash)
            || $passwordHash === ''
        ) {
            $passwordHash = $this->hasher->make(
                Str::random(64),
            );
        }

        AuthIdentity::query()->create([
            'user_id' => $userId,
            'email' => $email,
            'password_hash' => $passwordHash,
        ]);
    }

    public function updatePassword(
        User $user,
        string $plainPassword,
    ): void {
        if (! $this->usesMongo()) {
            $user->forceFill([
                'password' => $plainPassword,
            ])->save();

            return;
        }

        $this->ensureForUser($user);

        $identity = $this->findIdentityByUserId(
            (int) $user->getKey(),
        );

        if ($identity === null) {
            throw new RuntimeException(
                'Identitas autentikasi MongoDB tidak ditemukan.',
            );
        }

        $identity->forceFill([
            'email' => $this->normalizeEmail(
                (string) $user->email,
            ),
            'password_hash' => $this->hasher->make(
                $plainPassword,
            ),
        ])->save();
    }

    public function updateEmail(
        User $user,
        string $email,
    ): void {
        if (! $this->usesMongo()) {
            return;
        }

        $this->ensureForUser($user);

        $identity = $this->findIdentityByUserId(
            (int) $user->getKey(),
        );

        if ($identity === null) {
            throw new RuntimeException(
                'Identitas autentikasi MongoDB tidak ditemukan.',
            );
        }

        $identity->forceFill([
            'email' => $this->normalizeEmail($email),
        ])->save();
    }

    public function deleteForUser(User $user): void
    {
        if (! $this->usesMongo()) {
            return;
        }

        AuthIdentity::query()
            ->where(
                'user_id',
                (int) $user->getKey(),
            )
            ->delete();
    }

    public function rehashPasswordIfRequired(
        User $user,
        string $plainPassword,
        bool $force = false,
    ): void {
        $passwordHash = $this->passwordHashFor(
            $user,
        );

        if (
            $passwordHash === null
            || (
                ! $force
                && ! $this->hasher->needsRehash(
                    $passwordHash,
                )
            )
        ) {
            return;
        }

        $this->updatePassword(
            $user,
            $plainPassword,
        );
    }

    private function driver(): string
    {
        $driver = Str::lower(
            trim(
                (string) config(
                    'auth_credentials.driver',
                    'postgres',
                ),
            ),
        );

        if (
            ! in_array(
                $driver,
                ['postgres', 'mongodb'],
                true,
            )
        ) {
            throw new RuntimeException(
                "AUTH_CREDENTIAL_STORE tidak valid: {$driver}",
            );
        }

        return $driver;
    }

    private function normalizeEmail(string $email): string
    {
        return Str::lower(
            trim($email),
        );
    }
}
