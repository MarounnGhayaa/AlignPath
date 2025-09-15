<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use App\Models\UserPath;
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

        // Also update the saved path progress based on average of all skills for this path
        try {
            $avg = Skill::where('path_id', $skill->path_id)->avg('value');
            if ($avg !== null) {
                UserPath::where('user_id', $user->id)
                    ->where('path_id', $skill->path_id)
                    ->update(['progress_percentage' => (int) round($avg)]);
            }
        } catch (\Throwable $e) {
            // Swallow errors here to not block the skill update; logging could be added
        }

        return response()->json($skill);
    }
}
