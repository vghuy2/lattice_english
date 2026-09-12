<?php

namespace App\Http\Controllers\Admin\Vocabulary;

use App\Enums\ContentStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Vocabulary\StoreTopicRequest;
use App\Http\Requests\Admin\Vocabulary\UpdateTopicRequest;
use App\Models\VocabularyTopic;
use App\Services\VocabularyService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TopicController extends Controller
{
    public function __construct(
        protected VocabularyService $vocabularyService
    ) {}

    /**
     * Display a listing of the topics.
     */
    public function index(Request $request): View
    {
        $query = VocabularyTopic::withCount(['lessons', 'items'])->ordered();

        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $topics = $query->paginate(10)->withQueryString();

        return view('admin.vocabulary.topics.index', [
            'topics' => $topics,
            'search' => $request->input('search'),
            'status' => $request->input('status'),
            'statuses' => ContentStatus::cases(),
        ]);
    }

    /**
     * Show the form for creating a new topic.
     */
    public function create(): View
    {
        return view('admin.vocabulary.topics.create', [
            'statuses' => ContentStatus::cases(),
        ]);
    }

    /**
     * Store a newly created topic.
     */
    public function store(StoreTopicRequest $request): RedirectResponse
    {
        $topic = $this->vocabularyService->saveTopic(
            $request->validated(),
            null,
            $request->file('image')
        );

        return redirect()->route('admin.vocabulary.topics.index')
            ->with('success', "Đã tạo chủ đề \"{$topic->title}\" thành công.");
    }

    /**
     * Show the form for editing the specified topic.
     */
    public function edit(VocabularyTopic $topic): View
    {
        return view('admin.vocabulary.topics.edit', [
            'topic' => $topic,
            'statuses' => ContentStatus::cases(),
        ]);
    }

    /**
     * Update the specified topic.
     */
    public function update(UpdateTopicRequest $request, VocabularyTopic $topic): RedirectResponse
    {
        $this->vocabularyService->saveTopic(
            $request->validated(),
            $topic,
            $request->file('image')
        );

        return redirect()->route('admin.vocabulary.topics.index')
            ->with('success', "Đã cập nhật chủ đề \"{$topic->title}\" thành công.");
    }

    /**
     * Remove the specified topic.
     */
    public function destroy(VocabularyTopic $topic): RedirectResponse
    {
        $title = $topic->title;
        $topic->delete();

        return redirect()->route('admin.vocabulary.topics.index')
            ->with('success', "Đã xóa chủ đề \"{$title}\" cùng tất cả bài học liên quan.");
    }

    /**
     * Quick toggle topic status.
     */
    public function toggleStatus(VocabularyTopic $topic): RedirectResponse
    {
        $newStatus = $topic->status === ContentStatus::PUBLISHED
            ? ContentStatus::DRAFT
            : ContentStatus::PUBLISHED;

        $topic->update(['status' => $newStatus]);

        return back()->with('success', "Đã chuyển trạng thái chủ đề sang \"{$newStatus->label()}\".");
    }
}
