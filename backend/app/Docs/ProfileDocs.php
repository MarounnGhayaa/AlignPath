<?php

namespace App\Docs;

    /**
     * @OA\Get(
     *     path="/api/v0.1/user/{id}",
     *     summary="Get user info by ID",
     *     tags={"Profile"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="User ID", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Successful response", @OA\JsonContent(type="object"))
     * )
     * 
     * @OA\Put(
     *     path="/api/v0.1/user/{id}",
     *     summary="Update user info",
     *     tags={"Profile"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="User ID", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(response=200, description="Successful response", @OA\JsonContent(type="object"))
     * )
     * 
     * @OA\Get(
     *     path="/api/v0.1/user/paths",
     *     summary="Get authenticated user's saved paths",
     *     tags={"Profile"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(type="array", @OA\Items(
     *              @OA\Property(property="id", type="integer", example=1),
     *              @OA\Property(property="title", type="string", example="Frontend Developer"),
     *              @OA\Property(property="tag", type="string", example="Web"),
     *              @OA\Property(property="progress_percentage", type="number", example=40),
     *              @OA\Property(property="date_saved", type="string", format="date-time")
     *         ))
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated"))
     *     )
     * )
     */
class ProfileDocs{}