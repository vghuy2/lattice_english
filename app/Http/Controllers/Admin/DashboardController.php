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

        $stats = WritingSubmission::selectRaw("
            COUNT(CASE WHEN status IN ('submitted', 'grading', 'graded') THEN 1 END) as total_submissions,
            COUNT(CASE WHEN status = 'graded' THEN 1 END) as graded_count,
            AVG(CASE WHEN status = 'graded' THEN overall_score END) as avg_writing_band,
            COUNT(CASE WHEN status = 'graded' AND overall_score < 5.0 THEN 1 END) as below_5,
            COUNT(CASE WHEN status = 'graded' AND overall_score >= 5.0 AND overall_score <= 5.5 THEN 1 END) as band_5_5_5,
            COUNT(CASE WHEN status = 'graded' AND overall_score >= 6.0 AND overall_score <= 6.5 THEN 1 END) as band_6_6_5,
            COUNT(CASE WHEN status = 'graded' AND overall_score >= 7.0 THEN 1 END) as band_7_plus
        ")->first();

        $totalSubmissions = (int) ($stats->total_submissions ?? 0);
        $gradedCount = (int) ($stats->graded_count ?? 0);
        $avgWritingBand = $gradedCount > 0 && $stats->avg_writing_band !== null ? round((float) $stats->avg_writing_band, 1) : null;

        // Band distribution
        $bandDistribution = [
            'below_5' => (int) ($stats->below_5 ?? 0),
            'band_5_5_5' => (int) ($stats->band_5_5_5 ?? 0),
            'band_6_6_5' => (int) ($stats->band_6_6_5 ?? 0),
            'band_7_plus' => (int) ($stats->band_7_plus ?? 0),
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
