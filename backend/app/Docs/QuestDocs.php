<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/user/quests/{pathId}",
 *     summary="List quests for a path",
 *     description="Return all quests that belong to the supplied learning path.",
 *     tags={"Quests"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="pathId", in="path", required=true, @OA\Schema(type="integer", example=10)),
 *     @OA\Response(
 *         response=200,
 *         description="Array of quests",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="path_id", type="integer", example=10),
 *                 @OA\Property(property="title", type="string", example="Intro to Python"),
 *                 @OA\Property(property="subtitle", type="string", nullable=true, example="Understand core syntax"),
 *                 @OA\Property(property="description", type="string", nullable=true),
 *                 @OA\Property(property="difficulty", type="string", nullable=true, example="beginner"),
 *                 @OA\Property(property="duration", type="string", nullable=true, example="1 week"),
 *                 @OA\Property(property="created_at", type="string", format="date-time", nullable=true),
 *                 @OA\Property(property="updated_at", type="string", format="date-time", nullable=true)
 *             )
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated user")
 * )
 */
class QuestDocs {}
