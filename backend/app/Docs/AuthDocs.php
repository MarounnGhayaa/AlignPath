<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/guest/login",
 *     summary="Login",
 *     description="Authenticate user and return a JWT token.",
 *     tags={"Auth"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email","password"},
 *             @OA\Property(property="email", type="string", example="user@example.com"),
 *             @OA\Property(property="password", type="string", example="password123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful login",
 *         @OA\JsonContent(
 *             @OA\Property(property="access_token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Invalid credentials")
 * )
 * 
 * @OA\Post(
 *      path="/guest/register",
 *      summary="User registration",
 *      description="Register a new user and return a JWT token.",
 *      tags={"Auth"},
 *      @OA\RequestBody(
 *          required=true,
 *          @OA\JsonContent(
 *              required={"name", "email", "password", "password_confirmation"},
 *              @OA\Property(property="name", type="string", example="John Doe"),
 *              @OA\Property(property="email", type="string", format="email", example="john.doe@example.com"),
 *              @OA\Property(property="password", type="string", format="password", example="password"),
 *              @OA\Property(property="password_confirmation", type="string", format="password", example="password")
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Successful registration",
 *          @OA\JsonContent(
 *              @OA\Property(property="user", type="object"),
 *              @OA\Property(property="token", type="string", example="jwt_token_here")
 *          )
 *      ),
 *      @OA\Response(
 *          response=422,
 *          description="Validation error",
 *          @OA\JsonContent(
 *              @OA\Property(property="message", type="string", example="The given data was invalid.")
 *          )
 *      )
 * )
 */
class AuthDocs {}
