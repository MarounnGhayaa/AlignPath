<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/admin/analyses",
 *     summary="List conversation analyses",
 *     description="Returns the latest daily conversation analyses including related user information. Requires admin role.",
 *     tags={"Admin"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Analyses",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=12),
 *                 @OA\Property(property="user_id", type="integer", example=44),
 *                 @OA\Property(property="thread_id", type="integer", nullable=true, example=18),
 *                 @OA\Property(property="day", type="string", format="date", example="2025-09-20"),
 *                 @OA\Property(property="summary", type="string", example="Strong engagement with mentor support."),
 *                 @OA\Property(property="attributes", type="object", nullable=true, example={"sentiment":"positive"}),
 *                 @OA\Property(
 *                     property="user",
 *                     type="object",
 *                     @OA\Property(property="id", type="integer", example=44),
 *                     @OA\Property(property="username", type="string", example="student_sara"),
 *                     @OA\Property(property="email", type="string", format="email", example="sara@example.com")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated user"),
 *     @OA\Response(response=403, description="Forbidden")
 * )
 *
 * @OA\Delete(
 *     path="/admin/users/{user}",
 *     summary="Delete a user",
 *     description="Deletes the specified user. Only admins can delete accounts and cannot delete themselves.",
 *     tags={"Admin"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="user", in="path", required=true, description="User id", @OA\Schema(type="integer", example=77)),
 *     @OA\Response(
 *         response=200,
 *         description="Deletion succeeded",
 *         @OA\JsonContent(@OA\Property(property="status", type="string", example="ok"))
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated user"),
 *     @OA\Response(response=403, description="Forbidden"),
 *     @OA\Response(response=422, description="Cannot delete yourself")
 * )
 */
class AdminDocs {}
