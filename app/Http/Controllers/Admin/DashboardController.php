<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SubmissionStatus;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VocabularyItem;
use App\Models\VocabularyLesson;
use App\Models\VocabularyTopic;
use App\Models\WritingPrompt;
use App\Models\WritingSubmission;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display comprehensive admin analytics and dashboard.
     */
    public function index(Request $request): View
    {
        $totalStudents = User::where('role', UserRole::STUDENT->value)->count();
        $totalTopics = VocabularyTopic::count();
        $totalLessons = VocabularyLesson::count();
        $totalItems = VocabularyItem::count();
        $totalPrompts = WritingPrompt::count();

        $totalSubmissions = WritingSubmission::submitted()->count();
        $gradedCount = WritingSubmission::graded()->count();
        $avgWritingBand = WritingSubmission::graded()->avg('overall_score');

        // Band distribution
        $bandDistribution = [
            'below_5' => WritingSubmission::graded()->where('overall_score', '<', 5.0)->count(),
            'band_5_5_5' => WritingSubmission::graded()->whereBetween('overall_score', [5.0, 5.5])->count(),
            'band_6_6_5' => WritingSubmission::graded()->whereBetween('overall_score', [6.0, 6.5])->count(),
            'band_7_plus' => WritingSubmission::graded()->where('overall_score', '>=', 7.0)->count(),
        ];

        // Recent Submissions
        $recentSubmissions = WritingSubmission::with(['user', 'prompt.topic'])
            ->latest()
            ->take(5)
            ->get();

        // Recent Students
        $recentStudents = User::where('role', UserRole::STUDENT->value)
            ->latest()
            ->take(5)
            ->get();

        // Top attempted prompts
        $topPrompts = WritingPrompt::withCount('sampleEssays')
            ->ordered()
            ->take(4)
            ->get();

        return view('admin.dashboard', [
            'totalStudents' => $totalStudents,
            'totalTopics' => $totalTopics,
            'totalLessons' => $totalLessons,
            'totalItems' => $totalItems,
            'totalPrompts' => $totalPrompts,
            'totalSubmissions' => $totalSubmissions,
            'gradedCount' => $gradedCount,
            'avgWritingBand' => $avgWritingBand ? round($avgWritingBand, 1) : null,
            'bandDistribution' => $bandDistribution,
            'recentSubmissions' => $recentSubmissions,
            'recentStudents' => $recentStudents,
            'topPrompts' => $topPrompts,
        ]);
    }
}
