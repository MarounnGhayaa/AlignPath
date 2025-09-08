<?php

namespace App\Http\Controllers;

use App\Traits\ResponseTrait;

/**
 * @OA\Info(
 *   title="AlignPath APIs",
 *   version="v0.1",
 *   description="RESTful APIs for AlignPath, offering endpoints for authentication, user management, and personalized career recommendations with their corresponding quests, problems, skills and resources."
 * )
 */

abstract class Controller {
    use ResponseTrait;
}
