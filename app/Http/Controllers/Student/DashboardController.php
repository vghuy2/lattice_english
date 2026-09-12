<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Services\StudentProgressService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        protected StudentProgressService $progressService
    ) {}

    /**
     * Display student dashboard with complete progress metrics, streak, and review queue.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $dashboardData = $this->progressService->getDashboardData($user);

        return view('student.dashboard', $dashboardData);
    }
}
