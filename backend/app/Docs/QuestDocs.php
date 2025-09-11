<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/user/quests/{pathId}",
 *     summary="List quests for a path",
 *     tags={"Quests"},
 *     description="Return all quests for the specified path.",
 *     security={{"bearerAuth"={}}},
 *     @OA\Parameter(name="pathId", in="path", required=true, @OA\Schema(type="integer", example=10)),
 *     @OA\Response(
 *         response=200,
 *         description="Array of quests",
 *         @OA\JsonContent(type="array", @OA\Items(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="path_id", type="integer", example=10),
 *             @OA\Property(property="title", type="string", example="Intro to Python"),
 *             @OA\Property(property="description", type="string", nullable=true)
 *         ))
 *     ),
 *     @OA\Response(response=401, description="Unauthorized")
 * )
 */
class QuestDocs {}
