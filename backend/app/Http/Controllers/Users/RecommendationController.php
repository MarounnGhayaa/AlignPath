<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Recommendation;

class RecommendationController extends Controller
{
    public function getUserRecommendations(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $recommendations = Recommendation::where('user_id', $user->id)
                                    ->select('career_name', 'description')
                                    ->get();

        return response()->json($recommendations);
    }
}
