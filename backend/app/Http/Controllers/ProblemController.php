<?php

namespace App\Http\Controllers;

use App\Models\Problem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProblemController extends Controller
{
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
         */
        public function getProblemsByPath(Request $request, $pathId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $problems = Problem::where('path_id', $pathId)->get();

        return response()->json($problems);
    }

    /**
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
    public function getProblemById(Request $request, $problemId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $problem = Problem::find($problemId);

        if (!$problem) {
            return response()->json(['error' => 'Problem not found'], 404);
        }

        return response()->json($problem);
    }
}
