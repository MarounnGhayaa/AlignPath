<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Users\User_PreferencesService;

class UserPreferenceController extends Controller  {
    public function storeUserPreferences(Request $request) {
        $user_preference = User_PreferencesService:: storePreferences( $request, $request->user()->id);
        return $this->responseJSON($user_preference);
    }
}
