<?php

namespace App\Docs;

    /**
     * @OA\Post(
     *     path="/api/v0.1/user/preferences",
     *     summary="Store or update authenticated user's preferences",
     *     tags={"User Preferences"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(response=200, description="Successful response", @OA\JsonContent(type="object"))
     * )
     */
class UserPreferenceDocs{}