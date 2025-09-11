<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/user/accept-path",
 *     summary="Accept/Save a learning path for the user",
 *     description="Associate a path to the authenticated user (creates a UserPath).",
 *     tags={"AiAgent"},
 *     security={{"bearerAuth"={}}},
 *     @OA\RequestBody(required=true, @OA\JsonContent(required={"path_id"}, @OA\Property(property="path_id", type="integer", example=10))),
 *     @OA\Response(response=200, description="Path accepted", @OA\JsonContent(
 *         @OA\Property(property="user_path", type="object",
 *             @OA\Property(property="id", type="integer", example=33),
 *             @OA\Property(property="user_id", type="integer", example=1),
 *             @OA\Property(property="path_id", type="integer", example=10),
 *             @OA\Property(property="progress_percentage", type="number", format="float", example=0)
 *         )
 *     )),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=404, description="Path not found"),
 *     @OA\Response(response=422, description="Validation error")
 * )
 * 
 * @OA\Post(
 *     path="/user/recommend-careers",
 *     summary="Recommend careers based on interests",
 *     description="Use the AI agent to recommend career paths and persist them as recommendations.",
 *     tags={"AiAgent"},
 *     security={{"bearerAuth"={}}},
 *     @OA\RequestBody(required=true, @OA\JsonContent(required={"interests"}, @OA\Property(property="interests", type="array", @OA\Items(type="string"), example={"AI","Cloud","Frontend"}))),
 *     @OA\Response(response=200, description="Career paths", @OA\JsonContent(
 *         @OA\Property(property="career_paths", type="array", @OA\Items(
 *             @OA\Property(property="title", type="string", example="Machine Learning Engineer"),
 *             @OA\Property(property="description", type="string", nullable=true, example="Build and deploy ML models...")
 *         ))
 *     )),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=502, description="AI agent failed")
 * )
 * 
 * @OA\Post(
 *     path="/user/generate-quests-and-problems",
 *     summary="Generate quests, problems, skills and resources",
 *     description="Invoke the AI agent to generate learning content, then persist and return it.",
 *     tags={"AiAgent"},
 *     security={{"bearerAuth"={}}},
 *     @OA\RequestBody(required=false, @OA\JsonContent(@OA\Property(property="path_id", type="integer", nullable=true, example=10))),
 *     @OA\Response(response=200, description="Generated content", @OA\JsonContent(
 *         @OA\Property(property="quests", type="array", @OA\Items(
 *             @OA\Property(property="id", type="integer", example=1),
 *             @OA\Property(property="title", type="string", example="Intro to Python"),
 *             @OA\Property(property="description", type="string", nullable=true)
 *         )),
 *         @OA\Property(property="problems", type="array", @OA\Items(
 *             @OA\Property(property="id", type="integer", example=5),
 *             @OA\Property(property="title", type="string", example="FizzBuzz"),
 *             @OA\Property(property="description", type="string", nullable=true)
 *         )),
 *         @OA\Property(property="skills", type="array", @OA\Items(
 *             @OA\Property(property="id", type="integer", example=7),
 *             @OA\Property(property="name", type="string", example="Control Flow")
 *         )),
 *         @OA\Property(property="learningResources", type="array", @OA\Items(
 *             @OA\Property(property="id", type="integer", example=9),
 *             @OA\Property(property="title", type="string", example="Official Python Tutorial"),
 *             @OA\Property(property="type", type="string", example="article"),
 *             @OA\Property(property="url", type="string", example="https://docs.python.org/3/tutorial/"),
 *             @OA\Property(property="description", type="string", nullable=true)
 *         ))
 *     )),
 *     @OA\Response(response=401, description="Unauthorized"),
 *     @OA\Response(response=502, description="AI agent failed")
 * )
 */
class AiAgentDocs {}
