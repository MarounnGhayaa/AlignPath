<?php

namespace App\Docs;
        
        /**
        * @OA\Get(
        *     path="/api/v0.1/user/problems/path/{pathId}",
        *     summary="List problems by path",
        *     tags={"Problems"},
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
        *
        * @OA\Get(
        *     path="/api/v0.1/user/problems/{problemId}",
        *     summary="Get problem by ID",
        *     tags={"Problems"},
        *     security={{"bearerAuth":{}}},
        *     @OA\Parameter(name="problemId", in="path", required=true, description="Problem ID", @OA\Schema(type="integer")),
        *     @OA\Response(
        *         response=200,
        *         description="Successful response",
        *         @OA\JsonContent(type="object")
        *     ),
        *     @OA\Response(response=401, description="Unauthorized",
        *         @OA\JsonContent(@OA\Property(property="error", type="string", example="Unauthorized"))
        *     ),
        *     @OA\Response(response=404, description="Problem not found",
        *         @OA\JsonContent(@OA\Property(property="error", type="string", example="Problem not found"))
        *     )
        * )
        */

class ProblemDocs{}