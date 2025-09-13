<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SkillController extends Controller {
    public function getSkillsByPath(Request $request, $pathId) {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $skills = Skill::where('path_id', $pathId)->get();

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

        $skill->value = $validated['value'];
        $skill->save();

        return response()->json($skill);
    }
}
