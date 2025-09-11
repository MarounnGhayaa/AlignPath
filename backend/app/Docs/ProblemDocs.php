<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/user/problems/{pathId}",
 *     summary="List problems for a path",
 *     tags={"Problems"},
 *     security={{"bearerAuth"={}}},
 *     @OA\Parameter(name="pathId", in="path", required=true, @OA\Schema(type="integer", example=10)),
 *     @OA\Response(
 *         response=200,
 *         description="Array of problems",
 *         @OA\JsonContent(type="array", @OA\Items(
 *             @OA\Property(property="id", type="integer", example=50),
 *             @OA\Property(property="path_id", type="integer", example=10),
 *             @OA\Property(property="title", type="string", example="Two Sum"),
 *             @OA\Property(property="description", type="string", nullable=true)
 *         ))
 *     ),
 *     @OA\Response(response=401, description="Unauthorized")
 * )
 * 
 * @OA\Get(
 *     path="/user/problem/{problemId}",
 *     summary="Get a problem by id",
 *     tags={"Problems"},
 *     security={{"bearerAuth"={}}},
 *     @OA\Parameter(name="problemId", in="path", required=true, @OA\Schema(type="integer", example=50)),
 *     @OA\Response(
 *         response=200,
 *         description="Problem",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=50),
 *             @OA\Property(property="path_id", type="integer", example=10),
 *             @OA\Property(property="title", type="string", example="Two Sum"),
 *             @OA\Property(property="description", type="string", example="Given an array of integers...", nullable=true)
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=404, description="Problem not found")
 * )
 */
class ProblemDocs {}
