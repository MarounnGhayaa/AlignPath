<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/user/skills/{pathId}",
 *     summary="List skills for a path",
 *     tags={"Skills"},
 *     security={{"bearerAuth"={}}},
 *     @OA\Parameter(name="pathId", in="path", required=true, @OA\Schema(type="integer", example=10)),
 *     @OA\Response(
 *         response=200,
 *         description="Array of skills",
 *         @OA\JsonContent(type="array", @OA\Items(
 *             @OA\Property(property="id", type="integer", example=7),
 *             @OA\Property(property="path_id", type="integer", example=10),
 *             @OA\Property(property="name", type="string", example="Control Flow")
 *         ))
 *     ),
 *     @OA\Response(response=401, description="Unauthorized")
 * )
 */
class SkillDocs {}
