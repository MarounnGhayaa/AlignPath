<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\Users\User_PreferencesService;

class UserPreferenceController extends Controller  {
    /**
     * @OA\Post(
     *     path="/api/user/preferences",
     *     summary="Store or update authenticated user's preferences",
     *     tags={"User Preferences"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(response=200, description="Successful response", @OA\JsonContent(type="object"))
     * )
     */
    public function storeUserPreferences(Request $request) {
        $user_preference = User_PreferencesService:: storePreferences( $request, $request->user()->id);
        return $this->responseJSON($user_preference);
    }
}
