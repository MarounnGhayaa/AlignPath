<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/user/resources/{pathId}",
 *     summary="List learning resources for a path",
 *     description="Return curated learning resources for the given learning path.",
 *     tags={"LearningResources"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="pathId", in="path", required=true, @OA\Schema(type="integer", example=10)),
 *     @OA\Response(
 *         response=200,
 *         description="Array of learning resources",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=9),
 *                 @OA\Property(property="path_id", type="integer", example=10),
 *                 @OA\Property(property="name", type="string", example="Official Python Tutorial"),
 *                 @OA\Property(property="type", type="string", example="article"),
 *                 @OA\Property(property="url", type="string", format="uri", example="https://docs.python.org/3/tutorial/"),
 *                 @OA\Property(property="description", type="string", nullable=true)
 *             )
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated user")
 * )
 */
class LearningResourceDocs {}
