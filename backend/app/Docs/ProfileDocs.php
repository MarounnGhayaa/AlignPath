<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/user/getInfo/{id}",
 *     summary="Get user info",
 *     description="Return the public profile for a given user id.",
 *     tags={"Profile"},
 *     security={{"bearerAuth"={}}},
 *     @OA\Parameter(
 *         name="id", in="path", required=true,
 *         description="User ID", @OA\Schema(type="integer", example=1)
 *     ),
 *     @OA\Response(response=200, description="User info", @OA\JsonContent(ref="#/components/schemas/UserBasic")),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=404, description="User not found")
 * )
 * 
 * @OA\Get(
 *     path="/user/paths",
 *     summary="List user's learning paths",
 *     description="Return all learning paths saved by the authenticated user.",
 *     tags={"Profile"},
 *     security={{"bearerAuth"={}}},
 *     @OA\Response(
 *         response=200,
 *         description="Array of user paths",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=10),
 *                 @OA\Property(property="title", type="string", example="Data Science"),
 *                 @OA\Property(property="tag", type="string", example="data_science"),
 *                 @OA\Property(property="progress_percentage", type="number", format="float", example=42.5),
 *                 @OA\Property(property="date_saved", type="string", format="date-time", example="2025-09-01T09:30:00Z")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthorized")
 * )
 * 
 * @OA\Put(
 *     path="/user/updateInfo/{id}",
 *     summary="Update user profile",
 *     description="Update fields on the user's profile.",
 *     tags={"Profile"},
 *     security={{"bearerAuth"={}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=1)),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="name", type="string", example="Jane Doe"),
 *             @OA\Property(property="email", type="string", format="email", example="jane@example.com"),
 *             @OA\Property(property="bio", type="string", nullable=true, example="Curious lifelong learner."),
 *             @OA\Property(property="avatar_url", type="string", nullable=true, example="https://cdn.example.com/avatars/1.png")
 *         )
 *     ),
 *     @OA\Response(response=200, description="Updated user", @OA\JsonContent(ref="#/components/schemas/UserBasic")),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=422, description="Validation error")
 * )
 */
class ProfileDocs {}
