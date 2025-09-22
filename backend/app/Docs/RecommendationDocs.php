<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/user/recommendations",
 *     summary="List user's saved recommendations",
 *     tags={"Recommendations"},
 *     security={{"bearerAuth"={}}},
 *     @OA\Response(
 *         response=200,
 *         description="Array of recommendations",
 *         @OA\JsonContent(type="array", @OA\Items(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="career_name", type="string", example="Data Analyst"),
 *             @OA\Property(property="description", type="string", nullable=true)
 *         ))
 *     ),
 *     @OA\Response(response=401, description="Unauthorized")
 * )
 */
class RecommendationDocs {}

