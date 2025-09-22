<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/user/preferences",
 *     summary="Save user preferences",
 *     description="Create or update the authenticated user's learning preferences. All fields are required and stored as plain strings.",
 *     tags={"UserPreference"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"skills","interests","values","careers"},
 *             @OA\Property(property="skills", type="string", example="Python, SQL, Statistics"),
 *             @OA\Property(property="interests", type="string", example="Data science, automation"),
 *             @OA\Property(property="values", type="string", example="Innovation, impact"),
 *             @OA\Property(property="careers", type="string", example="Data Analyst, ML Engineer")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Stored preferences",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(
 *                 property="payload",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=3),
 *                 @OA\Property(property="user_id", type="integer", example=1),
 *                 @OA\Property(property="skills", type="string"),
 *                 @OA\Property(property="interests", type="string"),
 *                 @OA\Property(property="values", type="string"),
 *                 @OA\Property(property="careers", type="string"),
 *                 @OA\Property(property="created_at", type="string", format="date-time", nullable=true),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", nullable=true)
 *             )
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated user"),
 *     @OA\Response(response=422, description="Validation error")
 * )
 */
class UserPreferenceDocs {}
