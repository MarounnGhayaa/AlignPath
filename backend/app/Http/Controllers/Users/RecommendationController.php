<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Recommendation;

/**
 * @OA\Get(
 *     path="/api/user/recommendations",
 *     summary="Get user recommendations",
 *     tags={"Recommendations"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Successful response",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=1),
 *                 @OA\Property(property="career_name", type="string", example="Software Engineer"),
 *                 @OA\Property(property="description", type="string", example="Designs and builds software systems.")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated",
 *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated"))
 *     )
 * )
 */
class RecommendationController extends Controller
{
    public function getUserRecommendations(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $recommendations = Recommendation::where('user_id', $user->id)
                                    ->select('id', 'career_name', 'description')
                                    ->get();

        return response()->json($recommendations);
    }
}
