<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/api/v0.1/user/recommendations",
 *     summary="Get user recommendations",
 *     tags={"Recommendations"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Successful response",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="career_name", type="string", example="Software Engineer"),
 *                 @OA\Property(property="description", type="string", example="Designs and builds software systems.")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated",
 *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated"))
 *     )
 * )
 */

class RecommendationDocs{}