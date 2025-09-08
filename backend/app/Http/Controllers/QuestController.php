<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Quest;

class QuestController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/quests/path/{pathId}",
     *     summary="List quests by path",
     *     tags={"Quests"},
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
    public function getQuestsByPath(Request $request, $pathId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $quests = Quest::where('path_id', $pathId)->get();

        return response()->json($quests);
    }
}
