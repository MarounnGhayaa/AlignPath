<?php

namespace App\Http\Controllers;

use App\Models\Problem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProblemController extends Controller
{
        public function getProblemsByPath(Request $request, $pathId)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $problems = Problem::where('path_id', $pathId)->get();

        return response()->json($problems);
    }

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
