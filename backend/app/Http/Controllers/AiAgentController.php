<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\UserPath;
use App\Models\Recommendation;
use App\Models\Path;
use App\Models\Quest;
use App\Models\Problem;

class AiAgentController extends Controller
{
    protected $fastApiBase;
    protected $fastApiToken;

    public function __construct() {
        $this->fastApiBase = env("FASTAPI_AGENT_URL");
        $this->fastApiToken = env("FASTAPI_AGENT_SHARED_SECRET");
    }

    /**
     * @OA\Post(
     *      path="/api/v0.1/user/ai/accept-path",
     *      summary="Accept a recommended path",
     *      tags={"AI Agent"},
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"recommendation_id"},
     *              @OA\Property(property="recommendation_id", type="integer", example=1, description="ID of the recommendation to accept")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Path accepted and saved successfully",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Path accepted and saved successfully"),
     *              @OA\Property(property="path_id", type="integer", example=1),
     *              @OA\Property(property="career_name", type="string", example="Software Engineer")
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(@OA\Property(property="error", type="string", example="Unauthorized"))
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(@OA\Property(property="message", type="string", example="The given data was invalid."))
     *      )
     * )
     */
    public function acceptPath(Request $request) {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'recommendation_id' => 'required|exists:recommendations,id',
        ]);

        $recommendationId = $request->input('recommendation_id');
        $recommendation = Recommendation::findOrFail($recommendationId);

        $path = Path::create([
            'name' => $recommendation->career_name,
            'tag' => $recommendation->description,
        ]);

        $exists = UserPath::where('user_id', $user->id)
            ->where('path_id', $path->id)
            ->exists();

        if (!$exists) {
            UserPath::create([
                'user_id' => $user->id,
                'path_id' => $path->id,
                'progress_percentage' => 0,
                'date_saved' => now(),
            ]);
        }

        $recommendation->update(['status' => 'accepted']);

        return response()->json([
            'message' => 'Path accepted and saved successfully',
            'path_id' => $path->id,
            'career_name' => $recommendation->career_name
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/v0.1/user/ai/dismiss-path",
     *      summary="Dismiss a recommended path",
     *      tags={"AI Agent"},
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"recommendation_id"},
     *              @OA\Property(property="recommendation_id", type="integer", example=1, description="ID of the recommendation to dismiss")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Recommendation dismissed successfully",
     *          @OA\JsonContent(@OA\Property(property="message", type="string", example="Recommendation dismissed successfully"))
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(@OA\Property(property="error", type="string", example="Unauthorized"))
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(@OA\Property(property="message", type="string", example="The given data was invalid."))
     *      )
     * )
     */
    public function dismissPath(Request $request)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'recommendation_id' => 'required|exists:recommendations,id',
        ]);

        $recommendationId = $request->input('recommendation_id');
        $recommendation = Recommendation::findOrFail($recommendationId);

        $recommendation->update(['status' => 'dismissed']);

        return response()->json([
            'message' => 'Recommendation dismissed successfully'
        ]);
    }

    /**
     * @OA\Post(
     *      path="/api/v0.1/user/ai/recommend-careers",
     *      summary="Get career recommendations from AI agent",
     *      tags={"AI Agent"},
     *      security={{"bearerAuth":{}}},
     *      @OA\Response(
     *          response=200,
     *          description="Successful response with recommended careers",
     *          @OA\JsonContent(
     *              @OA\Property(property="career_paths", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="title", type="string", example="Software Engineer"),
     *                      @OA\Property(property="description", type="string", example="Designs and builds software systems.")
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=401,
     *          description="Unauthorized",
     *          @OA\JsonContent(@OA\Property(property="error", type="string", example="Unauthorized"))
     *      ),
     *      @OA\Response(
     *          response=404,
     *          description="No preferences found for the user",
     *          @OA\JsonContent(@OA\Property(property="error", type="string", example="No preferences found"))
     *      ),
     *      @OA\Response(
     *          response=502,
     *          description="AI agent failed to return recommendations",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="AI agent failed"),
     *              @OA\Property(property="details", type="object", example={})
     *          )
     *      )
     * )
     */
    public function recommendCareers(Request $request) {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $prefs = $user->preference;
        if (!$prefs) {
            return response()->json(['error' => 'No preferences found'], 404);
        }

        $interests = implode(", ", [
            $prefs->skills,
            $prefs->interests,
            $prefs->values,
            $prefs->careers
        ]);

        $response = Http::withHeaders([
            "Authorization" => "Bearer {$this->fastApiToken}"
        ])->post("{$this->fastApiBase}/recommend-careers", [
            "interests" => $interests
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'AI agent failed', 'details' => $response->json()], 502);
        }

        $recommendedCareers = $response->json();

        foreach ($recommendedCareers['career_paths'] as $career) {
            Recommendation::create([
                'user_id' => $user->id,
                'career_name' => $career['title'],
                'description' => $career['description'] ?? null,
            ]);
        }

        return response()->json($recommendedCareers);
    }

    /**
     * @OA\Post(
     *      path="/api/v0.1/user/ai/generate-quests",
     *      summary="Generate quests for a given career path",
     *      tags={"AI Agent"},
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"career", "path_id"},
     *              @OA\Property(property="career", type="string", example="Software Engineer", description="The career path for which to generate quests"),
     *              @OA\Property(property="path_id", type="integer", example=1, description="The ID of the path to associate the quests with")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful response with generated quests",
     *          @OA\JsonContent(
     *              @OA\Property(property="quests", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="title", type="string", example="Learn basic algorithms"),
     *                      @OA\Property(property="subtitle", type="string", example="Understand data structures and algorithms"),
     *                      @OA\Property(property="difficulty", type="string", example="easy"),
     *                      @OA\Property(property="duration", type="string", example="2 weeks")
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(@OA\Property(property="message", type="string", example="The given data was invalid."))
     *      ),
     *      @OA\Response(
     *          response=502,
     *          description="AI agent failed to generate quests",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="AI agent failed"),
     *              @OA\Property(property="details", type="object", example={})
     *          )
     *      )
     * )
     */
    public function generateQuests(Request $request)
    {
        $request->validate([
            'career' => 'required|string',
            'path_id' => 'required|exists:paths,id',
        ]);

        $response = Http::withHeaders([
            "Authorization" => "Bearer {$this->fastApiToken}"
        ])->post("{$this->fastApiBase}/generate-quests", [
            "career" => $request->input('career')
        ]);

        if ($response->failed()) {
            return response()->json(['error' => 'AI agent failed', 'details' => $response->json()], 502);
        }

        $generatedQuests = $response->json();

        foreach ($generatedQuests['quests'] as $questData) {
            Quest::create([
                'title' => $questData['title'],
                'subtitle' => $questData['subtitle'] ?? '',
                'path_id' => $request->input('path_id'),
                'difficulty' => $questData['difficulty'] ?? null,
                'duration' => $questData['duration'] ?? null,
            ]);
        }

        return response()->json($generatedQuests);
    }

    /**
     * @OA\Post(
     *      path="/api/v0.1/user/ai/generate-quests-and-problems",
     *      summary="Generate quests and problems for a given career path",
     *      tags={"AI Agent"},
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"career", "path_id"},
     *              @OA\Property(property="career", type="string", example="Software Engineer", description="The career path for which to generate quests and problems"),
     *              @OA\Property(property="path_id", type="integer", example=1, description="The ID of the path to associate the quests and problems with")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful response with generated quests and problems",
     *          @OA\JsonContent(
     *              @OA\Property(property="quests", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="title", type="string", example="Learn basic algorithms"),
     *                      @OA\Property(property="subtitle", type="string", example="Understand data structures and algorithms"),
     *                      @OA\Property(property="difficulty", type="string", example="easy"),
     *                      @OA\Property(property="duration", type="string", example="2 weeks")
     *                  )
     *              ),
     *              @OA\Property(property="problems", type="array",
     *                  @OA\Items(
     *                      @OA\Property(property="title", type="string", example="Implement a sorting algorithm"),
     *                      @OA\Property(property="subtitle", type="string", example="Sort an array of integers"),
     *                      @OA\Property(property="question", type="string", example="Given an array of integers, sort the array in ascending order."),
     *                      @OA\Property(property="first_answer", type="string", example="Bubble sort"),
     *                      @OA\Property(property="second_answer", type="string", example="Merge sort"),
     *                      @OA\Property(property="third_answer", type="string", example="Quick sort"),
     *                      @OA\Property(property="correct_answer", type="string", example="Merge sort"),
     *                      @OA\Property(property="points", type="integer", example=10)
     *                  )
     *              )
     *          )
     *      ),
     *      @OA\Response(
     *          response=422,
     *          description="Validation error",
     *          @OA\JsonContent(@OA\Property(property="message", type="string", example="The given data was invalid."))
     *      ),
     *      @OA\Response(
     *          response=502,
     *          description="AI agent failed to generate quests and problems",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="string", example="AI agent failed"),
     *              @OA\Property(property="details", type="object", example={})
     *          )
     *      )
     * )
     */
    public function generateQuestsAndProblems(Request $request)
{
    $request->validate([
        'career' => 'required|string',
        'path_id' => 'required|exists:paths,id',
    ]);

    $response = Http::withHeaders([
        "Authorization" => "Bearer {$this->fastApiToken}"
    ])->post("{$this->fastApiBase}/generate-quests-and-problems", [
        "career" => $request->input('career')
    ]);

    if ($response->failed()) {
        return response()->json(['error' => 'AI agent failed', 'details' => $response->json()], 502);
    }

    $generatedData = $response->json();

    // Save quests
    foreach ($generatedData['quests'] as $questData) {
        Quest::create([
            'title' => $questData['title'],
            'subtitle' => $questData['subtitle'] ?? '',
            'path_id' => $request->input('path_id'),
            'difficulty' => $questData['difficulty'] ?? null,
            'duration' => $questData['duration'] ?? null,
        ]);
    }

    // Save problems
    foreach ($generatedData['problems'] as $problemData) {
        Problem::create([
            'title'          => $problemData['title'],
            'subtitle'       => $problemData['subtitle'] ?? '',
            'path_id'        => $request->input('path_id'),
            'question'       => $problemData['question'],
            'first_answer'   => $problemData['first_answer'],
            'second_answer'  => $problemData['second_answer'],
            'third_answer'   => $problemData['third_answer'],
            'correct_answer' => $problemData['correct_answer'],
            'points'         => $problemData['points'] ?? 0,
        ]);
    }

    return response()->json($generatedData);
}


}
