<?php

namespace App\Docs;

/**
 * @OA\Schema(
 *   schema="UserBasic",
 *   type="object",
 *   @OA\Property(property="id", type="integer", example=1),
 *   @OA\Property(property="name", type="string", example="Jane Doe"),
 *   @OA\Property(property="email", type="string", format="email", example="jane@example.com"),
 *   @OA\Property(property="avatar_url", type="string", nullable=true)
 * )
 */
class SharedSchemas {}

