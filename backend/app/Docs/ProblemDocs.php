<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/user/problems/{pathId}",
 *     summary="List problems for a path",
 *     description="Return the multiple-choice problems linked to the given learning path.",
 *     tags={"Problems"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="pathId", in="path", required=true, @OA\Schema(type="integer", example=10)),
 *     @OA\Response(
 *         response=200,
 *         description="Array of problems",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=50),
 *                 @OA\Property(property="path_id", type="integer", example=10),
 *                 @OA\Property(property="title", type="string", example="Two Sum"),
 *                 @OA\Property(property="subtitle", type="string", nullable=true, example="Find indices adding up to target"),
 *                 @OA\Property(property="question", type="string", example="Given an array of integers..."),
 *                 @OA\Property(property="first_answer", type="string", example="Use a hash map"),
 *                 @OA\Property(property="second_answer", type="string", example="Sort the list"),
 *                 @OA\Property(property="third_answer", type="string", example="Brute force"),
 *                 @OA\Property(property="correct_answer", type="string", example="first_answer"),
 *                 @OA\Property(property="points", type="integer", example=100)
 *             )
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated user")
 * )
 *
 * @OA\Get(
 *     path="/user/problem/{problemId}",
 *     summary="Get a problem by id",
 *     description="Fetch the detailed statement for the specified problem.",
 *     tags={"Problems"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(name="problemId", in="path", required=true, @OA\Schema(type="integer", example=50)),
 *     @OA\Response(
 *         response=200,
 *         description="Problem",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=50),
 *             @OA\Property(property="path_id", type="integer", example=10),
 *             @OA\Property(property="title", type="string", example="Two Sum"),
 *             @OA\Property(property="subtitle", type="string", nullable=true),
 *             @OA\Property(property="question", type="string", example="Given an array of integers..."),
 *             @OA\Property(property="first_answer", type="string"),
 *             @OA\Property(property="second_answer", type="string"),
 *             @OA\Property(property="third_answer", type="string"),
 *             @OA\Property(property="correct_answer", type="string"),
 *             @OA\Property(property="points", type="integer", example=100)
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated user"),
 *     @OA\Response(response=404, description="Problem not found")
 * )
 */
class ProblemDocs {}
