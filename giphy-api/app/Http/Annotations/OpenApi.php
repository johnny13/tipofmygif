<?php

namespace App\Http\Annotations;

/**
 * @OA\Info(
 *     title="Giphy API",
 *     version="1.0.0",
 *     description="API for searching Giphy GIFs, ratings, and comments",
 *     @OA\Contact(
 *         email="support@example.com"
 *     )
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class OpenApi
{
    // This class is used to hold OpenAPI annotations
}