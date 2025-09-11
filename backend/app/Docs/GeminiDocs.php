<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/user/chat",
 *     summary="Send a chat message to the AI assistant",
 *     description="Creates/uses a chat thread and returns the assistant's reply.",
 *     tags={"Gemini"},
 *     security={{"bearerAuth"={}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"message"},
 *             @OA\Property(property="message", type="string", example="How do I start learning Python?"),
 *             @OA\Property(property="thread_id", type="integer", nullable=true, example=12, description="Existing thread id to continue in; omitted to create a new one"),
 *             @OA\Property(property="context", type="object", nullable=true, description="Optional context to steer the assistant")
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Assistant reply",
 *         @OA\JsonContent(
 *             @OA\Property(property="reply", type="string", example="Here's a roadmap to get you started..."),
 *             @OA\Property(property="blocked", type="boolean", example=false),
 *             @OA\Property(property="thread_id", type="integer", example=12),
 *             @OA\Property(property="user_message_id", type="integer", example=101),
 *             @OA\Property(property="assistant_message_id", type="integer", example=102),
 *             @OA\Property(property="usage", type="object", example={"input_tokens": 123, "output_tokens": 456}),
 *             @OA\Property(property="raw", type="object", description="Raw provider response (debug)")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=422, description="Validation error")
 * )
 * 
 * @OA\Get(
 *     path="/user/chat/threads",
 *     summary="List chat threads",
 *     description="Paginated list of the user's chat threads, newest first.",
 *     tags={"Gemini"},
 *     security={{"bearerAuth"={}}},
 *     @OA\Parameter(name="limit", in="query", required=false, @OA\Schema(type="integer", minimum=1, maximum=100, default=20)),
 *     @OA\Response(
 *         response=200,
 *         description="Threads",
 *         @OA\JsonContent(type="array", @OA\Items(
 *             @OA\Property(property="id", type="integer", example=12),
 *             @OA\Property(property="title", type="string", example="Learning Python"),
 *             @OA\Property(property="last_message_at", type="string", format="date-time", example="2025-09-10T20:12:00Z")
 *         ))
 *     ),
 *     @OA\Response(response=401, description="Unauthorized")
 * )
 * 
 * @OA\Get(
 *     path="/user/chat/threads/{thread}",
 *     summary="Get a chat thread",
 *     description="Fetch a single thread and its messages.",
 *     tags={"Gemini"},
 *     security={{"bearerAuth"={}}},
 *     @OA\Parameter(name="thread", in="path", required=true, @OA\Schema(type="integer", example=12)),
 *     @OA\Response(
 *         response=200,
 *         description="Thread detail",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=12),
 *             @OA\Property(property="title", type="string", example="Learning Python"),
 *             @OA\Property(property="messages", type="array", @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=102),
 *                 @OA\Property(property="role", type="string", example="assistant"),
 *                 @OA\Property(property="content", type="string", example="Start with basics like variables..."),
 *                 @OA\Property(property="created_at", type="string", format="date-time")
 *             ))
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=403, description="Forbidden"),
 *     @OA\Response(response=404, description="Not found")
 * )
 * 
 * @OA\Patch(
 *     path="/user/chat/threads/{thread}",
 *     summary="Rename a chat thread",
 *     description="Update only the title of a chat thread.",
 *     tags={"Gemini"},
 *     security={{"bearerAuth"={}}},
 *     @OA\Parameter(name="thread", in="path", required=true, @OA\Schema(type="integer", example=12)),
 *     @OA\RequestBody(required=true, @OA\JsonContent(@OA\Property(property="title", type="string", example="My Python Journey"))),
 *     @OA\Response(response=200, description="Renamed", @OA\JsonContent(@OA\Property(property="ok", type="boolean", example=true))),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=403, description="Forbidden"),
 *     @OA\Response(response=404, description="Not found")
 * )
 * 
 * @OA\Delete(
 *     path="/user/chat/threads/{thread}",
 *     summary="Delete a chat thread",
 *     description="Permanently delete a thread belonging to the authenticated user.",
 *     tags={"Gemini"},
 *     security={{"bearerAuth"={}}},
 *     @OA\Parameter(name="thread", in="path", required=true, @OA\Schema(type="integer", example=12)),
 *     @OA\Response(response=200, description="Deleted", @OA\JsonContent(@OA\Property(property="ok", type="boolean", example=true))),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=403, description="Forbidden"),
 *     @OA\Response(response=404, description="Not found")
 * )
 */
class GeminiDocs {}
