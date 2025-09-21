<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/guest/login",
 *     summary="Login",
 *     description="Authenticate a user and receive a JWT token plus user details.",
 *     tags={"Auth"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"email","password"},
 *             @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="password123")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful login",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="success"),
 *             @OA\Property(
 *                 property="payload",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=12),
 *                 @OA\Property(property="username", type="string", example="mentor_mike"),
 *                 @OA\Property(property="email", type="string", format="email", example="user@example.com"),
 *                 @OA\Property(property="role", type="string", example="mentor"),
 *                 @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOi..."),
 *                 @OA\Property(property="created_at", type="string", format="date-time"),
 *                 @OA\Property(property="updated_at", type="string", format="date-time")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Invalid credentials",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="string", example="error"),
 *             @OA\Property(property="payload", type="null")
 *         )
 *     )
 * )
 *
 * @OA\Post(
 *      path="/guest/register",
 *      summary="User registration",
 *      description="Register a new user and receive a JWT token plus stored profile details.",
 *      tags={"Auth"},
 *      @OA\RequestBody(
 *          required=true,
 *          @OA\JsonContent(
 *              required={"username", "email", "password", "role"},
 *              @OA\Property(property="username", type="string", example="student_sara"),
 *              @OA\Property(property="email", type="string", format="email", example="sara@example.com"),
 *              @OA\Property(property="password", type="string", format="password", example="password123"),
 *              @OA\Property(property="role", type="string", enum={"student","mentor"}, example="student")
 *          )
 *      ),
 *      @OA\Response(
 *          response=200,
 *          description="Successful registration",
 *          @OA\JsonContent(
 *              @OA\Property(property="status", type="string", example="success"),
 *              @OA\Property(
 *                  property="payload",
 *                  type="object",
 *                  @OA\Property(property="id", type="integer", example=25),
 *                  @OA\Property(property="username", type="string", example="student_sara"),
 *                  @OA\Property(property="email", type="string", format="email", example="sara@example.com"),
 *                  @OA\Property(property="role", type="string", example="student"),
 *                  @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOi..."),
 *                  @OA\Property(property="created_at", type="string", format="date-time"),
 *                  @OA\Property(property="updated_at", type="string", format="date-time")
 *              )
 *          )
 *      ),
 *      @OA\Response(response=422, description="Validation error")
 * )
 */
class AuthDocs {}

