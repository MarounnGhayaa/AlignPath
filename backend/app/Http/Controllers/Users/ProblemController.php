<?php

namespace App\Http\Controllers\Users;

use App\Exceptions\ServiceException;
use App\Http\Controllers\Controller;
use App\Services\Users\ProblemService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProblemController extends Controller {
    protected ProblemService $problems;

    public function __construct(ProblemService $problems) {
        $this->problems = $problems;
    }

    public function getProblemsByPath(Request $request, $pathId) {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $problems = $this->problems->listByPath((int) $pathId);

        return response()->json($problems);
    }

    public function getProblemById(Request $request, $problemId) {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $problem = $this->problems->findById((int) $problemId);
        } catch (ServiceException $exception) {
            return response()->json($exception->getPayload(), $exception->getStatus());
        }

        return response()->json($problem);
    }
}
