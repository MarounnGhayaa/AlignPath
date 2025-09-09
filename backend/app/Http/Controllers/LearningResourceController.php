<?php

namespace App\Http\Controllers;

use App\Models\LearningResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LearningResourceController extends Controller {
    public function getResourcesByPath(Request $request, $pathId) {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $resources = LearningResource::where('path_id', $pathId)->get();

        return response()->json($resources);
    }
}