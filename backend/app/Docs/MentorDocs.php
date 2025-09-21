<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/user/mentors",
 *     summary="Search mentors",
 *     description="Return mentors that match the authenticated user's saved recommendations and optional search term.",
 *     tags={"Mentors"},
 *     security={{"bearerAuth"={}}},
 *     @OA\Parameter(name="search", in="query", required=false, description="Filter mentors by name, company, position, or expertise", @OA\Schema(type="string", example="design")),
 *     @OA\Response(
 *         response=200,
 *         description="Mentor list",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=91),
 *                 @OA\Property(property="name", type="string", example="Daniel Rivera"),
 *                 @OA\Property(property="position", type="string", example="Product Designer at Acme"),
 *                 @OA\Property(property="company", type="string", nullable=true, example="Acme"),
 *                 @OA\Property(property="expertise", type="array", @OA\Items(type="string", example="UX")),
 *                 @OA\Property(property="avatar", type="string", nullable=true, example="https://cdn.alignpath.io/avatars/91.png"),
 *                 @OA\Property(property="isOnline", type="boolean", example=true),
 *                 @OA\Property(property="lastSeen", type="string", format="date-time", nullable=true, example="2025-09-20T20:01:00Z")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated user")
 * )
 */
class MentorDocs {}

