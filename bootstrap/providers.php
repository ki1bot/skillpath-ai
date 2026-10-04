<?php

use App\Providers\AppServiceProvider;
use App\Providers\FortifyServiceProvider;
use App\Providers\SocialAuthServiceProvider;
use MongoDB\Laravel\MongoDBServiceProvider;

return [
    MongoDBServiceProvider::class,
    AppServiceProvider::class,
    FortifyServiceProvider::class,
    SocialAuthServiceProvider::class,
];
