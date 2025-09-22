<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Services\Users\MentorService;
use Illuminate\Http\Request;

class MentorController extends Controller {
    protected MentorService $mentors;

    public function __construct(MentorService $mentors) {
        $this->mentors = $mentors;
    }

    public function index(Request $request) {
        $query = trim((string) $request->query('search', ''));
        $user  = $request->user();

        $mentors = $this->mentors->search($user, $query);

        return response()->json($mentors);
    }
}
