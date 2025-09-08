<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\Users\UserService;
use App\Models\User;
use App\Models\UserPath;
use App\Models\Path;

class ProfileController extends Controller {
    /**
     * @OA\Get(
     *     path="/api/v0.1/user/{id}",
     *     summary="Get user info by ID",
     *     tags={"Profile"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="User ID", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Successful response", @OA\JsonContent(type="object"))
     * )
     */
    public function getUserInfo($id) {
        $user = User::find($id);

        return $this->responseJSON($user);  
    }

    /**
     * @OA\Put(
     *     path="/api/v0.1/user/{id}",
     *     summary="Update user info",
     *     tags={"Profile"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, description="User ID", @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(type="object")
     *     ),
     *     @OA\Response(response=200, description="Successful response", @OA\JsonContent(type="object"))
     * )
     */
    public function updateUserInfo(Request $request, $id) {
        $data = $request->all();
        $updatedUser = UserService::update($data, $id);    

        return $this->responseJSON($updatedUser);  
    }

    /**
     * @OA\Get(
     *     path="/api/v0.1/user/paths",
     *     summary="Get authenticated user's saved paths",
     *     tags={"Profile"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful response",
     *         @OA\JsonContent(type="array", @OA\Items(
     *              @OA\Property(property="id", type="integer", example=1),
     *              @OA\Property(property="title", type="string", example="Frontend Developer"),
     *              @OA\Property(property="tag", type="string", example="Web"),
     *              @OA\Property(property="progress_percentage", type="number", example=40),
     *              @OA\Property(property="date_saved", type="string", format="date-time")
     *         ))
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated",
     *         @OA\JsonContent(@OA\Property(property="message", type="string", example="Unauthenticated"))
     *     )
     * )
     */
    public function getUserPaths(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated'], 401);
        }

        $userPaths = UserPath::where('user_id', $user->id)
            ->join('paths', 'users_paths.path_id', '=', 'paths.id')
            ->select(
                'paths.id as id',
                'paths.name as title',
                'paths.tag as tag',
                'users_paths.progress_percentage',
                'users_paths.date_saved'
            )
            ->get();

        return response()->json($userPaths);
    }
}
