<?php

namespace App\Console\Commands;

use App\Models\AuthIdentity;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use MongoDB\Laravel\Connection as MongoConnection;
use Throwable;

class MigrateAuthCredentialsToMongo extends Command
{
    protected $signature = 'auth:migrate-to-mongodb
        {--purge-postgres-passwords : Ganti password PostgreSQL dengan hash acak setelah credential tersalin}
        {--force : Izinkan purge credential PostgreSQL di production}';

    protected $description = 'Salin credential login dari PostgreSQL ke MongoDB auth_identities';

    public function handle(): int
    {
        if (! extension_loaded('mongodb')) {
            $this->error(
                'Ekstensi PHP mongodb belum terpasang.',
            );

            return self::FAILURE;
        }

        $purgePostgresPasswords = (bool) $this->option(
            'purge-postgres-passwords',
        );

        if (
            $purgePostgresPasswords
            && app()->isProduction()
            && ! $this->option('force')
        ) {
            $this->error(
                'Gunakan --force jika ingin melakukan purge password PostgreSQL di production.',
            );

            return self::FAILURE;
        }

        try {
            $connection = DB::connection(
                'mongodb',
            );

            if (! $connection instanceof MongoConnection) {
                $this->error(
                    'Connection mongodb tidak menggunakan driver MongoDB Laravel.',
                );

                return self::FAILURE;
            }

            $collection = $connection->getCollection(
                'auth_identities',
            );

            $collection->createIndex(
                [
                    'user_id' => 1,
                ],
                [
                    'name' => 'auth_identities_user_id_unique',
                    'unique' => true,
                ],
            );

            $collection->createIndex(
                [
                    'email' => 1,
                ],
                [
                    'name' => 'auth_identities_email_unique',
                    'unique' => true,
                ],
            );
        } catch (Throwable $exception) {
            $this->error(
                'MongoDB tidak dapat diakses: '
                .$exception->getMessage(),
            );

            return self::FAILURE;
        }

        $created = 0;
        $existing = 0;
        $purged = 0;
        $failed = 0;

        User::query()
            ->select([
                'id',
                'email',
                'password',
            ])
            ->orderBy('id')
            ->chunkById(
                100,
                function ($users) use (
                    &$created,
                    &$existing,
                    &$purged,
                    &$failed,
                    $purgePostgresPasswords,
                ): void {
                    foreach ($users as $user) {
                        try {
                            $email = Str::lower(
                                trim(
                                    (string) $user->email,
                                ),
                            );

                            $passwordHash = $user
                                ->getRawOriginal(
                                    'password',
                                );

                            if (
                                $email === ''
                                || ! is_string(
                                    $passwordHash,
                                )
                                || $passwordHash === ''
                            ) {
                                $failed++;

                                $this->warn(
                                    "User ID {$user->id} dilewati karena email atau password hash tidak valid.",
                                );

                                continue;
                            }

                            $identity = AuthIdentity::query()
                                ->where(
                                    'user_id',
                                    (int) $user->id,
                                )
                                ->first();

                            if ($identity === null) {
                                AuthIdentity::query()
                                    ->create([
                                        'user_id' => (int) $user->id,
                                        'email' => $email,
                                        'password_hash' => $passwordHash,
                                    ]);

                                $created++;
                            } else {
                                if (
                                    (string) $identity->getAttribute(
                                        'email',
                                    ) !== $email
                                ) {
                                    $identity
                                        ->forceFill([
                                            'email' => $email,
                                        ])
                                        ->save();
                                }

                                $existing++;
                            }

                            if ($purgePostgresPasswords) {
                                $user
                                    ->forceFill([
                                        'password' => Str::random(
                                            64,
                                        ),
                                    ])
                                    ->saveQuietly();

                                $purged++;
                            }
                        } catch (Throwable $exception) {
                            $failed++;

                            $this->warn(
                                "User ID {$user->id} gagal: {$exception->getMessage()}",
                            );
                        }
                    }
                },
            );

        $this->newLine();

        $this->info(
            "Credential baru di MongoDB: {$created}",
        );

        $this->info(
            "Credential yang sudah ada: {$existing}",
        );

        $this->info(
            "Password PostgreSQL yang diganti hash acak: {$purged}",
        );

        if ($failed > 0) {
            $this->warn(
                "Credential gagal diproses: {$failed}",
            );

            return self::FAILURE;
        }

        $this->info(
            'Migrasi credential ke MongoDB selesai.',
        );

        return self::SUCCESS;
    }
}
