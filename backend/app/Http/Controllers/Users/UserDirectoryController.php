<?php

namespace App\Http\Controllers\Users;

use App\Http\Controllers\Controller;
use App\Services\Users\UserDirectoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDirectoryController extends Controller {
    protected UserDirectoryService $directory;

    public function __construct(UserDirectoryService $directory) {
        $this->directory = $directory;
    }

    public function index(Request $request) {
        $me = Auth::user();
        abort_unless($me !== null, 401);
        abort_unless(strtolower((string) $me->role) === 'mentor', 403);

        $query = trim((string) $request->query('search', ''));
        $limit = (int) $request->query('limit', 100);

        $users = $this->directory->searchStudents($query, $limit);

        return response()->json($users);
    }
}
