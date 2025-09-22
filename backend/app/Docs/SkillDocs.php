<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/user/skills/{pathId}",
 *     summary="List skills for a path",
 *     description="Return the current skill ratings attached to a learning path.",
 *     tags={"Skills"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="pathId", in="path", required=true, @OA\Schema(type="integer", example=10)),
 *     @OA\Response(
 *         response=200,
 *         description="Array of skills",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=7),
 *                 @OA\Property(property="path_id", type="integer", example=10),
 *                 @OA\Property(property="name", type="string", example="Control Flow"),
 *                 @OA\Property(property="value", type="integer", example=60)
 *             )
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated user")
 * )
 *
 * @OA\Put(
 *     path="/user/skills/{skill}",
 *     summary="Update a skill progress value",
 *     description="Update the numeric progress value for a specific skill and propagate the user's path progress.",
 *     tags={"Skills"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="skill", in="path", required=true, @OA\Schema(type="integer", example=7)),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"value"},
 *             @OA\Property(property="value", type="integer", minimum=0, maximum=100, example=75)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Updated skill",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=7),
 *             @OA\Property(property="path_id", type="integer", example=10),
 *             @OA\Property(property="name", type="string", example="Control Flow"),
 *             @OA\Property(property="value", type="integer", example=75)
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated user"),
 *     @OA\Response(response=422, description="Validation error")
 * )
 */
class SkillDocs {}
