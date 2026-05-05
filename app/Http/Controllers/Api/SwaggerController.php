<?php

namespace App\Http\Controllers\Api;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: "CaveVin20 API",
    version: "1.0.0",
    description: "API REST pour la gestion des stocks de vins, bières et spiritueux.",
    contact: new OA\Contact(email: "cavevin20@test.fr")
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    scheme: "bearer",
    bearerFormat: "JWT"
)]
#[OA\Server(
    url: "http://127.0.0.1:8000",
    description: "Serveur local"
)]
class SwaggerController {}