<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use App\Services\Users\SkillService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SkillController extends Controller {
    protected SkillService $skills;

    public function __construct(SkillService $skills) {
        $this->skills = $skills;
    }

    public function getSkillsByPath(Request $request, $pathId) {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $skills = $this->skills->listByPath((int) $pathId);

        return response()->json($skills);
    }

    public function update(Request $request, Skill $skill) {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        try {
            $validated = $request->validate([
                'value' => 'required|integer|min:0|max:100',
            ]);
        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        $updated = $this->skills->updateValue($skill, $validated['value'], $user->id);

        return response()->json($updated);
    }
}
