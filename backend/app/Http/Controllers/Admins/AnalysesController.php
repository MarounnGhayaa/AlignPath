<?php

namespace App\Http\Controllers\Admins;

use App\Http\Controllers\Controller;
use App\Services\Admins\AnalysesService;

class AnalysesController extends Controller {
    public function __construct(private AnalysesService $analyses) {}

    public function getAnalyses() {
        return $this->analyses->list();
    }
}
