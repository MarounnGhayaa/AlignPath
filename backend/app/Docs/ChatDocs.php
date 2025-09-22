<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/user/mentors/{person}/chats",
 *     summary="Fetch chat history with a mentor",
 *     description="Return chronological messages exchanged with the specified mentor.",
 *     tags={"Chats"},
 *     security={{"bearerAuth"={}}},
 *     @OA\Parameter(name="person", in="path", required=true, description="Mentor user id", @OA\Schema(type="integer", example=91)),
 *     @OA\Response(
 *         response=200,
 *         description="Messages",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=1201),
 *                 @OA\Property(property="message", type="string", example="Great job on completing the SQL quest!"),
 *                 @OA\Property(property="sender_id", type="integer", example=33),
 *                 @OA\Property(property="isFromMentor", type="boolean", example=true),
 *                 @OA\Property(property="timestamp", type="string", format="date-time", example="2025-09-21T18:12:43Z")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=400, description="Attempting to open a chat with yourself"),
 *     @OA\Response(response=401, description="Unauthenticated user")
 * )
 *
 * @OA\Post(
 *     path="/user/mentors/{person}/messages",
 *     summary="Send a message to a mentor",
 *     description="Persist a new message in the conversation with the given mentor.",
 *     tags={"Chats"},
 *     security={{"bearerAuth"={}}},
 *     @OA\Parameter(name="person", in="path", required=true, description="Mentor user id", @OA\Schema(type="integer", example=91)),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"message"},
 *             @OA\Property(property="message", type="string", example="Could you review my portfolio outline?")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Created message",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=1450),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="sender_id", type="integer", example=12),
 *             @OA\Property(property="isFromMentor", type="boolean", example=false),
 *             @OA\Property(property="timestamp", type="string", format="date-time", example="2025-09-21T18:19:00Z")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Attempting to message yourself"),
 *     @OA\Response(response=401, description="Unauthenticated user"),
 *     @OA\Response(response=422, description="Validation error")
 * )
 *
 * @OA\Get(
 *     path="/user/users/{person}/chats",
 *     summary="Fetch chat history with a student",
 *     description="Return chronological messages exchanged with the specified student.",
 *     tags={"Chats"},
 *     security={{"bearerAuth"={}}},
 *     @OA\Parameter(name="person", in="path", required=true, description="Student user id", @OA\Schema(type="integer", example=44)),
 *     @OA\Response(
 *         response=200,
 *         description="Messages",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 @OA\Property(property="id", type="integer", example=2201),
 *                 @OA\Property(property="message", type="string", example="Let's schedule our next mentoring session."),
 *                 @OA\Property(property="sender_id", type="integer", example=44),
 *                 @OA\Property(property="isFromMentor", type="boolean", example=false),
 *                 @OA\Property(property="timestamp", type="string", format="date-time")
 *             )
 *         )
 *     ),
 *     @OA\Response(response=400, description="Attempting to open a chat with yourself"),
 *     @OA\Response(response=401, description="Unauthenticated user")
 * )
 *
 * @OA\Post(
 *     path="/user/users/{person}/messages",
 *     summary="Send a message to a student",
 *     description="Persist a new message in the conversation with the given student.",
 *     tags={"Chats"},
 *     security={{"bearerAuth"={}}},
 *     @OA\Parameter(name="person", in="path", required=true, description="Student user id", @OA\Schema(type="integer", example=44)),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"message"},
 *             @OA\Property(property="message", type="string", example="Great progress, keep going!")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Created message",
 *         @OA\JsonContent(
 *             @OA\Property(property="id", type="integer", example=2288),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(property="sender_id", type="integer", example=91),
 *             @OA\Property(property="isFromMentor", type="boolean", example=true),
 *             @OA\Property(property="timestamp", type="string", format="date-time")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Attempting to message yourself"),
 *     @OA\Response(response=401, description="Unauthenticated user"),
 *     @OA\Response(response=422, description="Validation error")
 * )
 *
 * @OA\Post(
 *     path="/user/transcribe",
 *     summary="Transcribe an audio message",
 *     description="Uploads an audio clip for speech-to-text transcription using the configured fallback service.",
 *     tags={"Chats"},
 *     security={{"bearerAuth"={}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\MediaType(
 *             mediaType="multipart/form-data",
 *             @OA\Schema(
 *                 required={"audio"},
 *                 @OA\Property(
 *                     property="audio",
 *                     type="string",
 *                     format="binary",
 *                     description="Audio file to transcribe (mp3, wav, webm, mp4, ogg)"
 *                 ),
 *                 @OA\Property(property="language", type="string", nullable=true, example="en")
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Transcription result",
 *         @OA\JsonContent(@OA\Property(property="text", type="string", example="Hi mentor, I finished the assignment."))
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated user"),
 *     @OA\Response(response=422, description="Invalid file upload"),
 *     @OA\Response(response=500, description="Transcription service error")
 * )
 */
class ChatDocs {}

