<?php

namespace App\Http\Controllers\Admin\Vocabulary;

use App\Enums\ContentStatus;
use App\Enums\VocabularyLevel;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Vocabulary\StoreLessonRequest;
use App\Http\Requests\Admin\Vocabulary\UpdateLessonRequest;
use App\Models\VocabularyLesson;
use App\Models\VocabularyTopic;
use App\Services\VocabularyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LessonController extends Controller
{
    public function __construct(
        protected VocabularyService $vocabularyService
    ) {}

    /**
     * Display a listing of the lessons.
     */
    public function index(Request $request): View
    {
        $query = VocabularyLesson::with('topic')->withCount('items')->ordered();

        if ($request->filled('topic_id')) {
            $query->where('topic_id', $request->input('topic_id'));
        }

        if ($request->filled('level')) {
            $query->where('level', $request->input('level'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        $lessons = $query->paginate(12)->withQueryString();
        $topics = VocabularyTopic::ordered()->get();

        return view('admin.vocabulary.lessons.index', [
            'lessons' => $lessons,
            'topics' => $topics,
            'levels' => VocabularyLevel::cases(),
            'statuses' => ContentStatus::cases(),
            'selectedTopic' => $request->input('topic_id'),
            'selectedLevel' => $request->input('level'),
            'selectedStatus' => $request->input('status'),
            'search' => $request->input('search'),
        ]);
    }

    /**
     * Show the form for creating a new lesson.
     */
    public function create(Request $request): View
    {
        $topics = VocabularyTopic::ordered()->get();

        return view('admin.vocabulary.lessons.create', [
            'topics' => $topics,
            'levels' => VocabularyLevel::cases(),
            'statuses' => ContentStatus::cases(),
            'selectedTopicId' => $request->input('topic_id'),
        ]);
    }

    /**
     * Store a newly created lesson.
     */
    public function store(StoreLessonRequest $request): RedirectResponse
    {
        $lesson = $this->vocabularyService->saveLesson(
            $request->validated(),
            null,
            $request->file('thumbnail')
        );

        return redirect()->route('admin.vocabulary.lessons.index', ['topic_id' => $lesson->topic_id])
            ->with('success', "Đã tạo bài học \"{$lesson->title}\" thành công. Hãy thêm từ vựng cho bài học.");
    }

    /**
     * Show the form for editing the specified lesson.
     */
    public function edit(VocabularyLesson $lesson): View
    {
        $topics = VocabularyTopic::ordered()->get();

        return view('admin.vocabulary.lessons.edit', [
            'lesson' => $lesson,
            'topics' => $topics,
            'levels' => VocabularyLevel::cases(),
            'statuses' => ContentStatus::cases(),
        ]);
    }

    /**
     * Update the specified lesson.
     */
    public function update(UpdateLessonRequest $request, VocabularyLesson $lesson): RedirectResponse
    {
        $this->vocabularyService->saveLesson(
            $request->validated(),
            $lesson,
            $request->file('thumbnail')
        );

        return redirect()->route('admin.vocabulary.lessons.index', ['topic_id' => $lesson->topic_id])
            ->with('success', "Đã cập nhật bài học \"{$lesson->title}\" thành công.");
    }

    /**
     * Remove the specified lesson.
     */
    public function destroy(VocabularyLesson $lesson): RedirectResponse
    {
        $title = $lesson->title;
        $lesson->delete();

        return redirect()->route('admin.vocabulary.lessons.index')
            ->with('success', "Đã xóa bài học \"{$title}\" cùng toàn bộ từ vựng.");
    }

    /**
     * Preview lesson as student card learning view.
     */
    public function preview(VocabularyLesson $lesson): View
    {
        $lesson->load(['topic', 'items' => fn ($q) => $q->ordered()]);

        return view('admin.vocabulary.lessons.preview', [
            'lesson' => $lesson,
        ]);
    }

    /**
     * Quick toggle publish status.
     */
    public function toggleStatus(VocabularyLesson $lesson): RedirectResponse
    {
        $newStatus = $lesson->status === ContentStatus::PUBLISHED
            ? ContentStatus::DRAFT
            : ContentStatus::PUBLISHED;

        $updateData = ['status' => $newStatus];
        if ($newStatus === ContentStatus::PUBLISHED && empty($lesson->published_at)) {
            $updateData['published_at'] = now();
        }

        $lesson->update($updateData);

        return back()->with('success', "Đã chuyển trạng thái bài học sang \"{$newStatus->label()}\".");
    }
}
