<?php

namespace App\Docs;

    /**
     * @OA\Post(
     *      path="/api/v0.1/user/chat",
     *      summary="Send messages to the Gemini AI model for chat completion",
     *      tags={"Gemini"},
     *      security={{"bearerAuth":{}}},
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\JsonContent(
     *              required={"messages"},
     *              @OA\Property(
     *                  property="messages",
     *                  type="array",
     *                  description="An array of messages in the chat history",
     *                  @OA\Items(
     *                      @OA\Property(property="role", type="string", enum={"user", "model"}, example="user"),
     *                      @OA\Property(property="content", type="string", example="Hello, how are you?")
     *                  )
     *              ),
     *              @OA\Property(property="system", type="string", nullable=true, example="You are a helpful assistant.", description="Optional system instruction for the AI model"),
     *              @OA\Property(property="temperature", type="number", format="float", nullable=true, example=0.7, description="Optional temperature for controlling randomness"),
     *              @OA\Property(property="maxOutputTokens", type="integer", nullable=true, example=1024, description="Optional maximum number of output tokens")
     *          )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="Successful response from Gemini API",
     *          @OA\JsonContent(
     *              @OA\Property(property="reply", type="string", example="I am doing well, thank you!"),
     *              @OA\Property(property="blocked", type="string", nullable=true, example="SAFETY"),
     *              @OA\Property(property="raw", type="object", description="Raw response from the Gemini API")
     *          )
     *      ),
     *      @OA\Response(
     *          response=500,
     *          description="Error interacting with Gemini API",
     *          @OA\JsonContent(
     *              @OA\Property(property="error", type="boolean", example=true),
     *              @OA\Property(property="status", type="integer", example=500),
     *              @OA\Property(property="message", type="string", example="Gemini API error")
     *          )
     *      )
     * )
     */

class GeminiDocs{}