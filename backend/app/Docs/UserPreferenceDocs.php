<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/user/preferences",
 *     summary="Save user preferences",
 *     description="Create or update learning preferences for the authenticated user.",
 *     tags={"UserPreference"},
 *     security={{"bearerAuth"={}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"interests"},
 *             @OA\Property(property="interests", type="array", @OA\Items(type="string"), example={"AI","Web Development"}),
 *             @OA\Property(property="goals", type="array", @OA\Items(type="string"), example={"Switch careers","Build a portfolio"}),
 *             @OA\Property(property="learning_style", type="string", example="hands_on"),
 *             @OA\Property(property="time_availability", type="string", example="10h/week"),
 *             @OA\Property(property="languages", type="array", @OA\Items(type="string"), example={"en","fr"})
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Stored preferences",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=3),
 *             @OA\Property(property="user_id", type="integer", example=1),
 *             @OA\Property(property="interests", type="array", @OA\Items(type="string")),
 *             @OA\Property(property="goals", type="array", @OA\Items(type="string")),
 *             @OA\Property(property="learning_style", type="string"),
 *             @OA\Property(property="time_availability", type="string"),
 *             @OA\Property(property="languages", type="array", @OA\Items(type="string"))
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=422, description="Validation error")
 * )
 */
class UserPreferenceDocs {}
