<?php

namespace App\Docs;

/**
 * @OA\Get(
 *     path="/guest/chat-messages",
 *     summary="Export chat messages for automation",
 *     description="Provide N8N (or other automation tooling) access to chat messages for a given day, optionally filtered by thread.",
 *     tags={"Automation"},
 *     @OA\Parameter(name="start", in="query", required=true, description="Day to export (ISO 8601 date)", @OA\Schema(type="string", format="date", example="2025-09-20")),
 *     @OA\Parameter(name="tz", in="query", required=false, description="IANA timezone for the provided day", @OA\Schema(type="string", example="Europe/Berlin")),
 *     @OA\Parameter(name="thread_id", in="query", required=false, description="Filter messages to a thread", @OA\Schema(type="integer", example=18)),
 *     @OA\Parameter(name="limit", in="query", required=false, description="Maximum number of messages to return (max 1000)", @OA\Schema(type="integer", example=200)),
 *     @OA\Parameter(name="cursor", in="query", required=false, description="Pagination cursor returned from a previous call", @OA\Schema(type="string", example="MjAyNS0wOS0yMFQxMjowMDowMFo=|1201")),
 *     @OA\Response(
 *         response=200,
 *         description="Message export",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="meta",
 *                 type="object",
 *                 @OA\Property(property="start_local", type="string", format="date-time", example="2025-09-20T00:00:00+02:00"),
 *                 @OA\Property(property="end_local", type="string", format="date-time", example="2025-09-21T00:00:00+02:00"),
 *                 @OA\Property(property="tz", type="string", example="Europe/Berlin"),
 *                 @OA\Property(property="count", type="integer", example=120),
 *                 @OA\Property(property="next_cursor", type="string", nullable=true, example="MjAyNS0wOS0yMFQyMTo0NTowMFo=|980")
 *             ),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=3201),
 *                     @OA\Property(property="thread_id", type="integer", example=18),
 *                     @OA\Property(property="user_id", type="integer", nullable=true, example=12),
 *                     @OA\Property(property="role", type="string", example="model"),
 *                     @OA\Property(property="content", type="string", example="Here is a reflection of today's progress..."),
 *                     @OA\Property(property="meta", type="object", nullable=true, description="Provider metadata"),
 *                     @OA\Property(property="created_at", type="string", format="date-time", example="2025-09-20T11:15:42Z"),
 *                     @OA\Property(property="updated_at", type="string", format="date-time", nullable=true)
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(response=422, description="Validation error")
 * )
 *
 * @OA\Post(
 *     path="/guest/daily-conversation-analyses",
 *     summary="Store conversation analyses",
 *     description="Creates or updates one or more daily conversation analyses, typically from an automation workflow.",
 *     tags={"Automation"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="array",
 *             minItems=1,
 *             @OA\Items(
 *                 required={"user_id","day"},
 *                 @OA\Property(property="user_id", type="integer", example=44),
 *                 @OA\Property(property="thread_id", type="integer", nullable=true, example=18),
 *                 @OA\Property(property="day", type="string", format="date", example="2025-09-20"),
 *                 @OA\Property(property="summary", type="string", nullable=true, example="Student maintained positive engagement."),
 *                 @OA\Property(property="attributes", type="object", nullable=true, example={"sentiment":"positive","topics":"portfolio"}),
 *                 @OA\Property(property="raw", type="object", nullable=true)
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Stored analyses",
 *         @OA\JsonContent(
 *             @OA\Property(property="count", type="integer", example=2),
 *             @OA\Property(
 *                 property="data",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer", example=17),
 *                     @OA\Property(property="user_id", type="integer", example=44),
 *                     @OA\Property(property="thread_id", type="integer", nullable=true, example=18),
 *                     @OA\Property(property="day", type="string", format="date", example="2025-09-20"),
 *                     @OA\Property(property="summary", type="string", nullable=true),
 *                     @OA\Property(property="attributes", type="object", nullable=true),
 *                     @OA\Property(property="raw", type="object", nullable=true),
 *                     @OA\Property(property="created_at", type="string", format="date-time", nullable=true),
 *                     @OA\Property(property="updated_at", type="string", format="date-time", nullable=true)
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(response=422, description="Validation error")
 * )
 */
class N8nDocs {}
