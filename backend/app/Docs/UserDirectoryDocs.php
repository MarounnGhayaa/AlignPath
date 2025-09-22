<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/user/users",
 *     summary="Search students",
 *     description="Allow mentors to search the directory of students by name, company, position, or expertise.",
 *     tags={"UserDirectory"},
 *     security={{"bearerAuth"={}}},
 *     @OA\Parameter(name="search", in="query", required=false, description="Case-insensitive search term", @OA\Schema(type="string", example="frontend")),
 *     @OA\Parameter(name="limit", in="query", required=false, description="Maximum number of students to return (max 200)", @OA\Schema(type="integer", example=50)),
 *     @OA\Response(
 *         response=200,
 *         description="Students list",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=44),
 *                 @OA\Property(property="name", type="string", example="Sofia Patel"),
 *                 @OA\Property(property="position", type="string", nullable=true, example="Frontend Developer at Vanta"),
 *                 @OA\Property(property="company", type="string", nullable=true, example="Vanta"),
 *                 @OA\Property(property="expertise", type="array", @OA\Items(type="string", example="React")),
 *                 @OA\Property(property="avatar", type="string", nullable=true, example="https://cdn.alignpath.io/avatars/44.png"),
 *                 @OA\Property(property="isOnline", type="boolean", example=false),
 *                 @OA\Property(property="lastSeen", type="string", format="date-time", nullable=true, example="2025-09-19T09:15:00Z")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated user"),
 *     @OA\Response(response=403, description="User is not a mentor")
 * )
 */
class UserDirectoryDocs {}

