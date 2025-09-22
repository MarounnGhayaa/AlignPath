<?php

namespace App\Docs;

/**
 * @OA\Info(
 *     title="AlignPath APIs",
 *     version="v0.1",
 *     description="RESTful APIs for AlignPath, offering endpoints for authentication, user management, and personalized career recommendations with their corresponding quests, problems, skills, and resources."
 * )
 *
 * @OA\Server(
 *     url="/api/v0.1",
 *     description="Base path for v0.1 API"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */
class OpenApi {}