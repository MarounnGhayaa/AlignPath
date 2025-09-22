<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/user/chat",
 *     summary="Send messages to the Gemini-powered assistant",
 *     description="Creates or continues a chat thread with the Gemini assistant and returns the assistant response.",
 *     tags={"Gemini"},
 *     security={{"bearerAuth"={}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"messages"},
 *             @OA\Property(property="thread_id", type="integer", nullable=true, example=18, description="Existing thread identifier to continue"),
 *             @OA\Property(
 *                 property="messages",
 *                 type="array",
 *                 @OA\Items(
 *                     required={"role","content"},
 *                     @OA\Property(property="role", type="string", enum={"user","model"}, example="user"),
 *                     @OA\Property(property="content", type="string", example="How can I improve my data analysis skills?")
 *                 )
 *             ),
 *             @OA\Property(property="system", type="string", nullable=true, example="Focus on project-based advice"),
 *             @OA\Property(property="temperature", type="number", format="float", nullable=true, example=0.6),
 *             @OA\Property(property="maxOutputTokens", type="integer", nullable=true, example=1024)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Assistant reply",
 *         @OA\JsonContent(
 *             @OA\Property(property="reply", type="string", nullable=true, example="Start with exploratory data analysis on open datasets."),
 *             @OA\Property(property="blocked", type="string", nullable=true, example=null),
 *             @OA\Property(property="thread_id", type="integer", example=18),
 *             @OA\Property(property="user_message_id", type="integer", nullable=true, example=3101),
 *             @OA\Property(property="assistant_message_id", type="integer", example=3102),
 *             @OA\Property(property="usage", type="object", nullable=true, example={"promptTokenCount":230,"candidatesTokenCount":180}),
 *             @OA\Property(property="raw", type="object", description="Raw payload returned by Gemini for debugging")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated user"),
 *     @OA\Response(response=422, description="Validation error"),
 *     @OA\Response(response=500, description="Gemini API error")
 * )
 *
 * @OA\Get(
 *     path="/user/chat/threads",
 *     summary="List chat threads",
 *     description="Returns the authenticated user's chat threads ordered by last activity.",
 *     tags={"Gemini"},
 *     security={{"bearerAuth"={}}},
 *     @OA\Parameter(name="limit", in="query", required=false, @OA\Schema(type="integer", minimum=1, maximum=100, example=20)),
 *     @OA\Response(
 *         response=200,
 *         description="Threads list",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="items",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=18),
 *                     @OA\Property(property="title", type="string", example="Learning SQL"),
 *                     @OA\Property(property="last_message_at", type="string", format="date-time", nullable=true, example="2025-09-21T17:45:00Z"),
 *                     @OA\Property(property="messages_count", type="integer", example=12),
 *                     @OA\Property(property="preview", type="string", nullable=true, example="Focus on real datasets to..." )
 *                 )
 *             ),
 *             @OA\Property(
 *                 property="meta",
 *                 type="object",
 *                 @OA\Property(property="current_page", type="integer", example=1),
 *                 @OA\Property(property="per_page", type="integer", example=20),
 *                 @OA\Property(property="total", type="integer", example=37),
 *                 @OA\Property(property="last_page", type="integer", example=2)
 *             )
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated user")
 * )
 *
 * @OA\Get(
 *     path="/user/chat/threads/{thread}",
 *     summary="Retrieve a chat thread",
 *     description="Fetches a single chat thread with its messages.",
 *     tags={"Gemini"},
 *     security={{"bearerAuth"={}}},
 *     @OA\Parameter(name="thread", in="path", required=true, @OA\Schema(type="integer", example=18)),
 *     @OA\Response(
 *         response=200,
 *         description="Thread detail",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="thread",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=18),
 *                 @OA\Property(property="title", type="string", example="Learning SQL"),
 *                 @OA\Property(property="last_message_at", type="string", format="date-time", nullable=true)
 *             ),
 *             @OA\Property(
 *                 property="messages",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=4101),
 *                     @OA\Property(property="role", type="string", enum={"user","model"}, example="model"),
 *                     @OA\Property(property="content", type="string", example="Start with SELECT queries..."),
 *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-21T17:45:00Z")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated user"),
 *     @OA\Response(response=403, description="Thread does not belong to the user"),
 *     @OA\Response(response=404, description="Thread not found")
 * )
 */
class GeminiDocs {}

