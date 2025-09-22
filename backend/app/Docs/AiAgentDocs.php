<?php

namespace App\Docs;

/**
 * @OA\Post(
 *     path="/user/accept-path",
 *     summary="Accept a recommended path",
 *     description="Creates a learning path from an existing recommendation and links it to the authenticated user.",
 *     tags={"AiAgent"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"recommendation_id"},
 *             @OA\Property(
 *                 property="recommendation_id",
 *                 type="integer",
 *                 example=42,
 *                 description="Identifier of the recommendation being accepted"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Path accepted",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Path accepted and saved successfully"),
 *             @OA\Property(property="path_id", type="integer", example=15),
 *             @OA\Property(property="career_name", type="string", example="AI Research Scientist")
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated user"),
 *     @OA\Response(response=404, description="Recommendation not found"),
 *     @OA\Response(response=422, description="Validation error")
 * )
 *
 * @OA\Post(
 *     path="/user/recommend-careers",
 *     summary="Generate fresh career recommendations",
 *     description="Uses the user's saved preferences to request career recommendations from the AI agent and store them.",
 *     tags={"AiAgent"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Career recommendations",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="career_paths",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="title", type="string", example="Machine Learning Engineer"),
 *                     @OA\Property(property="description", type="string", nullable=true, example="Design and deploy predictive systems.")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(response=401, description="Unauthenticated user"),
 *     @OA\Response(response=404, description="User preferences missing"),
 *     @OA\Response(response=502, description="AI provider failure")
 * )
 *
 * @OA\Post(
 *     path="/user/generate-quests-and-problems",
 *     summary="Generate learning content for a path",
 *     description="Asks the AI agent to generate quests, problems, skills, and learning resources for the given career and path.",
 *     tags={"AiAgent"},
 *     security={{"bearerAuth":{}}},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"career", "path_id"},
 *             @OA\Property(property="career", type="string", example="Product Design"),
 *             @OA\Property(property="path_id", type="integer", example=7)
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Generated learning entities",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="quests",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="title", type="string", example="Design discovery"),
 *                     @OA\Property(property="subtitle", type="string", nullable=true, example="Understand user needs"),
 *                     @OA\Property(property="difficulty", type="string", nullable=true, example="intermediate"),
 *                     @OA\Property(property="duration", type="string", nullable=true, example="2 weeks"),
 *                     @OA\Property(property="path_id", type="integer", example=7)
 *                 )
 *             ),
 *             @OA\Property(
 *                 property="problems",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="title", type="string", example="User persona mismatch"),
 *                     @OA\Property(property="question", type="string", example="Which change best satisfies the new research findings?"),
 *                     @OA\Property(property="first_answer", type="string", example="Adjust personas to match new segments"),
 *                     @OA\Property(property="second_answer", type="string", example="Ignore the research"),
 *                     @OA\Property(property="third_answer", type="string", example="Delay the launch"),
 *                     @OA\Property(property="correct_answer", type="string", example="first_answer"),
 *                     @OA\Property(property="points", type="integer", example=10),
 *                     @OA\Property(property="path_id", type="integer", example=7)
 *                 )
 *             ),
 *             @OA\Property(
 *                 property="skills",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="name", type="string", example="Rapid prototyping"),
 *                     @OA\Property(property="value", type="integer", example=20),
 *                     @OA\Property(property="path_id", type="integer", example=7)
 *                 )
 *             ),
 *             @OA\Property(
 *                 property="learningResources",
 *                 type="array",
 *                 @OA\Items(
 *                     @OA\Property(property="name", type="string", example="Design sprint playbook"),
 *                     @OA\Property(property="type", type="string", example="article"),
 *                     @OA\Property(property="url", type="string", format="uri", example="https://example.com/design-sprint"),
 *                     @OA\Property(property="description", type="string", nullable=true)
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(response=422, description="Invalid payload"),
 *     @OA\Response(response=502, description="AI provider failure")
 * )
 */
class AiAgentDocs {}
