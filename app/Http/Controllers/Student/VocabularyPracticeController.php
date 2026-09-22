<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\VocabularyLesson;
use App\Models\VocabularyPracticeSession;
use App\Services\Cache\RedisCacheKeys;
use App\Services\VocabularyLearningService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VocabularyPracticeController extends Controller
{
    public function __construct(
        protected VocabularyLearningService $learningService
    ) {}

    /**
     * Start a new practice session for a lesson.
     */
    public function start(Request $request, VocabularyLesson $lesson): RedirectResponse
    {
        if (! $lesson->isAvailableForStudents()) {
            abort(404, 'Bài học không khả dụng.');
        }

        $user = $request->user();

        // Check if there is an in-progress session (with Redis cache optimization)
        $activeKey = RedisCacheKeys::practiceActiveKey($user->id, $lesson->id);
        $existingSessionId = RedisCacheKeys::rememberOrFallback($activeKey, RedisCacheKeys::TTL_PRACTICE_ACTIVE, function () use ($user, $lesson) {
            $existing = VocabularyPracticeSession::where('user_id', $user->id)
                ->where('lesson_id', $lesson->id)
                ->where('status', 'in_progress')
                ->latest()
                ->first();

            return $existing?->id;
        });

        if ($existingSessionId) {
            $existingSession = VocabularyPracticeSession::find($existingSessionId);
            if ($existingSession && $existingSession->status === 'in_progress') {
                return redirect()->route('student.vocabulary.practice.show', $existingSession);
            }
            RedisCacheKeys::invalidateActivePractice($user->id, $lesson->id);
        }

        try {
            $session = $this->learningService->generatePracticeSession($user, $lesson);
            RedisCacheKeys::rememberOrFallback($activeKey, RedisCacheKeys::TTL_PRACTICE_ACTIVE, fn () => $session->id);
            return redirect()->route('student.vocabulary.practice.show', $session);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Display practice quiz interface.
     */
    public function show(Request $request, VocabularyPracticeSession $session): View|RedirectResponse
    {
        // Enforce ownership
        if ($session->user_id !== $request->user()->id) {
            abort(403, 'Bạn không có quyền truy cập bài luyện tập này.');
        }

        if ($session->isCompleted()) {
            return redirect()->route('student.vocabulary.practice.result', $session);
        }

        $session->load(['lesson.topic', 'answers.vocabularyItem']);

        return view('student.vocabulary.practice.show', [
            'session' => $session,
        ]);
    }

    /**
     * Submit and evaluate practice quiz.
     */
    public function submit(Request $request, VocabularyPracticeSession $session): RedirectResponse
    {
        // Enforce ownership
        if ($session->user_id !== $request->user()->id) {
            abort(403, 'Bạn không có quyền nộp bài luyện tập này.');
        }

        if ($session->isCompleted()) {
            return redirect()->route('student.vocabulary.practice.result', $session)
                ->with('warning', 'Bài tập này đã được nộp trước đó.');
        }

        $userAnswers = $request->input('answers', []);

        $completedSession = $this->learningService->submitPracticeSession($session, $userAnswers);

        // Invalidate active session and dashboard cache
        if ($session->lesson_id) {
            RedisCacheKeys::invalidateActivePractice($session->user_id, $session->lesson_id);
        }
        RedisCacheKeys::invalidateStudentDashboard($session->user_id);

        return redirect()->route('student.vocabulary.practice.result', $completedSession)
            ->with('success', "Chúc mừng bạn đã hoàn thành bài luyện tập! Độ chính xác: {$completedSession->accuracy_rate}%.");
    }

    /**
     * Display practice result and explanations.
     */
    public function result(Request $request, VocabularyPracticeSession $session): View
    {
        // Enforce ownership
        if ($session->user_id !== $request->user()->id) {
            abort(403, 'Bạn không có quyền xem kết quả bài luyện tập này.');
        }

        $session->load(['lesson.topic', 'answers.vocabularyItem']);

        return view('student.vocabulary.practice.result', [
            'session' => $session,
        ]);
    }
}
