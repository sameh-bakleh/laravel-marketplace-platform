<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'Marketplace API',
    description: 'Versioned REST API for web, mobile, and integrations.'
)]
#[OA\SecurityScheme(
    securityScheme: 'bearerAuth',
    type: 'http',
    scheme: 'bearer',
    bearerFormat: 'JWT',
    description: 'Use access token from POST /api/v1/auth/login'
)]
#[OA\Server(url: '/api/v1', description: 'API v1')]
abstract class Controller
{
    //
}
