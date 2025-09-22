<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Services\Users\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RecommendationController extends Controller {
    protected RecommendationService $recommendations;

    public function __construct(RecommendationService $recommendations) {
        $this->recommendations = $recommendations;
    }

    public function getUserRecommendations(Request $request) {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $recommendations = $this->recommendations->listForUser($user->id);

        return response()->json($recommendations);
    }
}
