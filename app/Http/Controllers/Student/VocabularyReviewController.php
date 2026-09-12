<?php

namespace App\Http\Controllers\Student;

use App\Enums\WordStudyStatus;
use App\Http\Controllers\Controller;
use App\Models\StudentVocabularyReview;
use App\Services\VocabularyLearningService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VocabularyReviewController extends Controller
{
    public function __construct(
        protected VocabularyLearningService $learningService
    ) {}

    /**
     * Display student's saved words and review queue.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = StudentVocabularyReview::where('user_id', $user->id)
            ->with(['vocabularyItem.lesson.topic']);

        $tab = $request->query('tab', 'due'); // 'due', 'favorites', 'all'

        if ($tab === 'due') {
            $query->dueForReview();
        } elseif ($tab === 'favorites') {
            $query->favorites();
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $term = $request->input('search');
            $query->whereHas('vocabularyItem', function ($q) use ($term) {
                $q->search($term);
            });
        }

        $reviews = $query->orderBy('next_review_at', 'asc')->paginate(15)->withQueryString();

        // Stats
        $dueCount = StudentVocabularyReview::where('user_id', $user->id)->dueForReview()->count();
        $favoritesCount = StudentVocabularyReview::where('user_id', $user->id)->favorites()->count();
        $totalStudiedCount = StudentVocabularyReview::where('user_id', $user->id)->count();

        return view('student.vocabulary.review.index', [
            'reviews' => $reviews,
            'tab' => $tab,
            'dueCount' => $dueCount,
            'favoritesCount' => $favoritesCount,
            'totalStudiedCount' => $totalStudiedCount,
            'statuses' => WordStudyStatus::cases(),
            'selectedStatus' => $request->input('status'),
            'search' => $request->input('search'),
        ]);
    }

    /**
     * Start quick review quiz from due words.
     */
    public function startQuiz(Request $request): RedirectResponse
    {
        $user = $request->user();

        try {
            $session = $this->learningService->generateReviewSession($user, 10);
            return redirect()->route('student.vocabulary.practice.show', $session);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
