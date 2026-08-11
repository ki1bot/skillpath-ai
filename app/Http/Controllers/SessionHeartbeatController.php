<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class SessionHeartbeatController extends Controller
{
    public function __invoke(): Response
    {
        return response()->noContent();
    }
}
