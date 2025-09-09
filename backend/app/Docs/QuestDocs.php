<?php

namespace App\Docs;

    /**
     * @OA\Get(
     *     path="/api/v0.1/user/quests/path/{pathId}",
     *     summary="List quests by path",
     *     tags={"Quests"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="pathId", in="path", required=true, description="Path ID", @OA\Schema(type="integer")),
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(type="array", @OA\Items(type="object"))
     *     ),
     *     @OA\Response(response=401, description="Unauthorized",
     *         @OA\JsonContent(@OA\Property(property="error", type="string", example="Unauthorized"))
     *     )
     * )
     */
class QuestDocs{}