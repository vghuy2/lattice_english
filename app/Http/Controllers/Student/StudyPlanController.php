<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\VocabularyLesson;
use App\Models\WritingPrompt;
use App\Services\StudentProgressService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudyPlanController extends Controller
{
    public function __construct(
        protected StudentProgressService $progressService
    ) {}

    /**
     * Display student personalized study roadmap & milestones.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $progress = $this->progressService->getDashboardData($user);

        // Next recommended lesson
        $nextLesson = $progress['in_progress']['lesson'] ?? VocabularyLesson::published()->ordered()->first();

        // Next recommended prompt
        $nextPrompt = WritingPrompt::availableForStudents()
            ->whereDoesntHave('submissions', fn ($q) => $q->where('user_id', $user->id)->submitted())
            ->ordered()
            ->first() ?? WritingPrompt::availableForStudents()->ordered()->first();

        return view('student.study_plan', [
            'user' => $user,
            'progress' => $progress,
            'nextLesson' => $nextLesson,
            'nextPrompt' => $nextPrompt,
        ]);
    }
}
