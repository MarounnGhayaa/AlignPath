<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Services\Users\LearningResourceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LearningResourceController extends Controller {
    protected LearningResourceService $learningResources;

    public function __construct(LearningResourceService $learningResources) {
        $this->learningResources = $learningResources;
    }

    public function getResourcesByPath(Request $request, $pathId) {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $resources = $this->learningResources->listByPath((int) $pathId);

        return response()->json($resources);
    }
}
