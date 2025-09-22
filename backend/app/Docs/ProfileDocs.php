<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/user/getInfo/{id}",
 *     summary="Get user info",
 *     description="Return the public profile data for the given user id.",
 *     tags={"Profile"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="User identifier",
 *         @OA\Schema(type="integer", example=3)
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="User info",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(
 *                 property="payload",
 *                 type="object",
 *                 nullable=true,
 *                 @OA\Property(property="id", type="integer", example=3),
 *                 @OA\Property(property="username", type="string", example="learner_jane"),
 *                 @OA\Property(property="email", type="string", format="email", example="jane@example.com"),
 *                 @OA\Property(property="role", type="string", example="student"),
 *                 @OA\Property(property="location", type="string", nullable=true, example="Berlin, DE"),
 *                 @OA\Property(property="avatar_url", type="string", nullable=true, example="https://cdn.alignpath.io/avatars/3.png"),
 *                 @OA\Property(property="created_at", type="string", format="date-time"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=404, description="User not found")
 * )
 *
 * @OA\Get(
 *     path="/user/paths",
 *     summary="List user's learning paths",
 *     description="Return all learning paths saved by the authenticated user.",
 *     tags={"Profile"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Array of user paths",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=10),
 *                 @OA\Property(property="title", type="string", example="Data Science"),
 *                 @OA\Property(property="tag", type="string", example="data_science"),
 *                 @OA\Property(property="progress_percentage", type="integer", example=45),
 *                 @OA\Property(property="date_saved", type="string", format="date-time", example="2025-09-01T09:30:00Z")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated user")
 * )
 *
 * @OA\Patch(
 *     path="/user/updateInfo/{id}",
 *     summary="Update user profile",
 *     description="Updates selected fields on the user's profile. Fields are optional and will only be updated when provided.",
 *     tags={"Profile"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer", example=3)),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             @OA\Property(property="username", type="string", nullable=true, example="learner_jane"),
 *             @OA\Property(property="email", type="string", format="email", nullable=true, example="jane@example.com"),
 *             @OA\Property(property="password", type="string", format="password", nullable=true, example="newPassword123"),
 *             @OA\Property(property="password_confirmation", type="string", format="password", nullable=true, example="newPassword123"),
 *             @OA\Property(property="location", type="string", nullable=true, example="Berlin, DE")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Updated user",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(
 *                 property="payload",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=3),
 *                 @OA\Property(property="username", type="string", example="learner_jane"),
 *                 @OA\Property(property="email", type="string", format="email", example="jane@example.com"),
 *                 @OA\Property(property="location", type="string", nullable=true)
 *             )
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated user"),
 *     @OA\Response(response=422, description="Validation error")
 * )
 */
class ProfileDocs {}
