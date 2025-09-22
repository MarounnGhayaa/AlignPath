<?php

namespace App\Services\Users;

use App\Models\UserPreference;
use Illuminate\Http\Request;


class User_PreferencesService {
    public static function storePreferences(Request $request, $userId) {
        $request->validate([
            'skills' => 'required|string|max:100',
            'interests' => 'required|string|max:100',
            'values' => 'required|string|max:100',
            'careers' => 'required|string|max:100',
        ]);

        $user_preference = new UserPreference;
        $user_preference->user_id = $userId;
        $user_preference->skills = $request->skills;
        $user_preference->interests = $request->interests;
        $user_preference->values = $request->values;
        $user_preference->careers = $request->careers;
        $user_preference->save();

        return $user_preference;
    }
}