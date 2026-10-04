<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model;

class AuthIdentity extends Model
{
    protected $connection = 'mongodb';

    protected $table = 'auth_identities';

    protected $fillable = [
        'user_id',
        'email',
        'password_hash',
    ];

    protected $hidden = [
        'password_hash',
    ];

    protected function casts(): array
    {
        return [
            'user_id' => 'integer',
        ];
    }
}
