<?php

namespace App\Docs;

/**
 * @OA\Info(
 *     version="0.1",
 *     title="Users API (v0.1)"
 * )
 */
/**
 * @OA\Server(
 *     url="/api/v0.1",
 *     description="Base path for v0.1 API"
 * )
 */
/**
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class OpenApi {}
