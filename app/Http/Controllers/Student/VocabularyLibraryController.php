<?php

namespace App\Http\Controllers\Student;

use App\Enums\LearningStatus;
use App\Enums\VocabularyLevel;
use App\Http\Controllers\Controller;
use App\Models\StudentLessonProgress;
use App\Models\StudentVocabularyReview;
use App\Models\VocabularyItem;
use App\Models\VocabularyLesson;
use App\Models\VocabularyTopic;
use App\Services\Cache\RedisCacheKeys;
use App\Services\VocabularyLearningService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VocabularyLibraryController extends Controller
{
    public function __construct(
        protected VocabularyLearningService $learningService
    ) {}

    /**
     * Display student vocabulary library.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // 1. Fetch available topics (cached with Redis)
        $topics = RedisCacheKeys::rememberOrFallback(
            RedisCacheKeys::TOPICS,
            RedisCacheKeys::TTL_TOPICS,
            fn () => VocabularyTopic::published()->ordered()->get()
        );

        // 2. Fetch available lessons (published and published_at <= now)
        $query = VocabularyLesson::availableForStudents()
            ->with('topic')
            ->withCount('items')
            ->ordered();

        if ($request->filled('topic_id')) {
            $query->where('topic_id', $request->input('topic_id'));
        }

        if ($request->filled('level')) {
            $query->where('level', $request->input('level'));
        }

        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        $lessons = $query->paginate(12)->withQueryString();

        // 3. Attach student progress to each lesson
        $progressMap = StudentLessonProgress::where('user_id', $user->id)
            ->whereIn('lesson_id', $lessons->pluck('id'))
            ->get()
            ->keyBy('lesson_id');

        // Total words learned by user
        $totalWordsLearned = StudentVocabularyReview::where('user_id', $user->id)
            ->whereIn('status', ['known', 'learning'])
            ->count();

        // Words needing review today
        $dueReviewCount = StudentVocabularyReview::where('user_id', $user->id)
            ->dueForReview()
            ->count();

        return view('student.vocabulary.library.index', [
            'topics' => $topics,
            'lessons' => $lessons,
            'progressMap' => $progressMap,
            'levels' => VocabularyLevel::cases(),
            'selectedTopic' => $request->input('topic_id'),
            'selectedLevel' => $request->input('level'),
            'search' => $request->input('search'),
            'totalWordsLearned' => $totalWordsLearned,
            'dueReviewCount' => $dueReviewCount,
        ]);
    }

    /**
     * Display lesson learning view (Flashcard & List).
     */
    public function showLesson(Request $request, VocabularyLesson $lesson): View
    {
        // Enforce student visibility
        if (! $lesson->isAvailableForStudents()) {
            abort(404, 'Bài học không tồn tại hoặc chưa được xuất bản.');
        }

        $user = $request->user();

        RedisCacheKeys::rememberOrFallback(
            RedisCacheKeys::lessonContentKey($lesson->id),
            RedisCacheKeys::TTL_LESSON_CONTENT,
            fn () => $lesson->load(['topic', 'items' => fn ($q) => $q->ordered()])
        );

        // Get user reviews for items in this lesson
        $reviewsMap = StudentVocabularyReview::where('user_id', $user->id)
            ->whereIn('vocabulary_item_id', $lesson->items->pluck('id'))
            ->get()
            ->keyBy('vocabulary_item_id');

        // Get or init progress
        $progress = StudentLessonProgress::firstOrCreate(
            ['user_id' => $user->id, 'lesson_id' => $lesson->id],
            ['total_items_count' => $lesson->items->count(), 'status' => LearningStatus::NOT_STARTED]
        );

        return view('student.vocabulary.library.show', [
            'lesson' => $lesson,
            'reviewsMap' => $reviewsMap,
            'progress' => $progress,
            'mode' => $request->query('mode', 'cards'), // 'cards' or 'list'
        ]);
    }

    /**
     * Mark word status (known, learning, review_needed).
     */
    public function updateWordStatus(Request $request, VocabularyItem $item): JsonResponse|RedirectResponse
    {
        $request->validate([
            'status' => ['required', 'in:known,learning,review_needed'],
        ]);

        $user = $request->user();
        $review = $this->learningService->markWordStatus($user, $item, $request->input('status'));

        // Invalidate student dashboard cache
        RedisCacheKeys::invalidateStudentDashboard($user->id);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'status' => $review->status->value,
                'mastery_level' => $review->mastery_level,
                'message' => "Đã đánh dấu từ: {$review->status->label()}",
            ]);
        }

        return back()->with('success', "Đã đánh dấu từ \"{$item->word}\": {$review->status->label()}");
    }

    /**
     * Toggle favorite bookmark for a word.
     */
    public function toggleFavorite(Request $request, VocabularyItem $item): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        $isFavorite = $this->learningService->toggleFavorite($user, $item);

        // Invalidate student dashboard cache
        RedisCacheKeys::invalidateStudentDashboard($user->id);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_favorite' => $isFavorite,
                'message' => $isFavorite ? 'Đã lưu vào danh sách yêu thích' : 'Đã bỏ yêu thích',
            ]);
        }

        return back()->with('success', $isFavorite ? 'Đã lưu vào danh sách yêu thích' : 'Đã bỏ yêu thích');
    }

    /**
     * Save personal note for a word.
     */
    public function saveNote(Request $request, VocabularyItem $item): JsonResponse|RedirectResponse
    {
        $request->validate([
            'note' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = $request->user();
        $this->learningService->savePersonalNote($user, $item, $request->input('note'));

        // Invalidate student dashboard cache
        RedisCacheKeys::invalidateStudentDashboard($user->id);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã lưu ghi chú cá nhân thành công.',
            ]);
        }

        return back()->with('success', 'Đã lưu ghi chú cá nhân.');
    }
}
