<?php

namespace Tests\Concerns;

use Illuminate\Support\Facades\DB;
use MongoDB\Laravel\Connection as MongoConnection;
use RuntimeException;

trait UsesMongoCredentials
{
    protected function setUpUsesMongoCredentials(): void
    {
        $database = trim(
            (string) env(
                'MONGODB_TEST_DATABASE',
                'skillpathai_auth_test',
            ),
        );

        if (
            $database === ''
            || ! str_contains(
                $database,
                '_test',
            )
        ) {
            throw new RuntimeException(
                'MONGODB_TEST_DATABASE harus menggunakan database khusus test.',
            );
        }

        $testToken = getenv(
            'TEST_TOKEN',
        );

        if (
            is_string($testToken)
            && $testToken !== ''
        ) {
            $database .= '_'.$testToken;
        }

        config()->set(
            'auth_credentials.driver',
            'mongodb',
        );

        config()->set(
            'database.connections.mongodb.database',
            $database,
        );

        DB::purge(
            'mongodb',
        );

        $connection = DB::connection(
            'mongodb',
        );

        if (! $connection instanceof MongoConnection) {
            throw new RuntimeException(
                'Connection mongodb tidak menggunakan driver MongoDB Laravel.',
            );
        }

        $collection = $connection->getCollection(
            'auth_identities',
        );

        $collection->deleteMany([]);

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
    }

    protected function tearDownUsesMongoCredentials(): void
    {
        $connection = DB::connection(
            'mongodb',
        );

        if ($connection instanceof MongoConnection) {
            $connection
                ->getCollection(
                    'auth_identities',
                )
                ->deleteMany([]);
        }

        DB::purge(
            'mongodb',
        );
    }
}
