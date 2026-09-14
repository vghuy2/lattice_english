<?php

namespace App\Http\Controllers\Student;

use App\Enums\ContentStatus;
use App\Enums\SubmissionStatus;
use App\Enums\VocabularyLevel;
use App\Enums\WritingPromptType;
use App\Enums\WritingTaskType;
use App\Http\Controllers\Controller;
use App\Models\VocabularyTopic;
use App\Models\WritingPrompt;
use App\Models\WritingSubmission;
use App\Services\Scoring\WritingScoringService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class WritingPracticeController extends Controller
{
    public function __construct(
        protected WritingScoringService $scoringService
    ) {}
    /**
     * Display student writing hub (Task 1 & Task 2 prompts).
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = WritingPrompt::availableForStudents()
            ->with(['topic', 'sampleEssays'])
            ->ordered();

        $taskType = $request->query('task_type'); // 'task_1', 'task_2', or null (all)
        if ($taskType && in_array($taskType, ['task_1', 'task_2'])) {
            $query->where('task_type', $taskType);
        }

        if ($request->filled('prompt_type')) {
            $query->where('prompt_type', $request->input('prompt_type'));
        }

        if ($request->filled('level')) {
            $query->where('level', $request->input('level'));
        }

        if ($request->filled('topic_id')) {
            $query->where('topic_id', $request->input('topic_id'));
        }

        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        $prompts = $query->paginate(9)->withQueryString();

        // Fetch user's latest submissions for prompts on page
        $userSubmissions = WritingSubmission::where('user_id', $user->id)
            ->whereIn('writing_prompt_id', $prompts->pluck('id'))
            ->latest()
            ->get()
            ->groupBy('writing_prompt_id');

        // Overall stats (consolidated into 1 SQL aggregate)
        $stats = WritingSubmission::where('writing_submissions.user_id', $user->id)
            ->whereIn('writing_submissions.status', [
                SubmissionStatus::SUBMITTED->value,
                SubmissionStatus::GRADING->value,
                SubmissionStatus::GRADED->value,
            ])
            ->join('writing_prompts', 'writing_submissions.writing_prompt_id', '=', 'writing_prompts.id')
            ->selectRaw("
                COUNT(writing_submissions.id) as total_submitted,
                COUNT(CASE WHEN writing_prompts.task_type = 'task_1' THEN 1 END) as task1_count,
                COUNT(CASE WHEN writing_prompts.task_type = 'task_2' THEN 1 END) as task2_count,
                AVG(CASE WHEN writing_submissions.status = ? THEN writing_submissions.overall_score END) as avg_score
            ", [SubmissionStatus::GRADED->value])
            ->first();

        $totalSubmitted = (int) ($stats->total_submitted ?? 0);
        $task1Count = (int) ($stats->task1_count ?? 0);
        $task2Count = (int) ($stats->task2_count ?? 0);
        $avgScore = ($stats && $stats->avg_score !== null) ? round((float) $stats->avg_score, 1) : null;

        return view('student.writing.index', [
            'prompts' => $prompts,
            'topics' => VocabularyTopic::published()->ordered()->get(),
            'taskTypes' => WritingTaskType::cases(),
            'promptTypes' => WritingPromptType::cases(),
            'levels' => VocabularyLevel::cases(),
            'selectedTaskType' => $taskType,
            'selectedPromptType' => $request->input('prompt_type'),
            'selectedLevel' => $request->input('level'),
            'selectedTopic' => $request->input('topic_id'),
            'search' => $request->input('search'),
            'userSubmissions' => $userSubmissions,
            'totalSubmitted' => $totalSubmitted,
            'task1Count' => $task1Count,
            'task2Count' => $task2Count,
            'avgScore' => $avgScore ? round($avgScore, 1) : null,
        ]);
    }

    /**
     * Display prompt detail, strategy guide & sample essays preview.
     */
    public function show(WritingPrompt $prompt): View
    {
        if (! $prompt->isAvailableForStudents()) {
            abort(404, 'Đề bài không tồn tại hoặc chưa được xuất bản.');
        }

        $prompt->load(['topic', 'sampleEssays' => fn ($q) => $q->published()->ordered()]);

        $latestSubmission = WritingSubmission::where('user_id', auth()->id())
            ->where('writing_prompt_id', $prompt->id)
            ->latest()
            ->first();

        return view('student.writing.show', [
            'prompt' => $prompt,
            'latestSubmission' => $latestSubmission,
        ]);
    }

    /**
     * Interactive Writing Exam Room (Split Screen).
     */
    public function practice(Request $request, WritingPrompt $prompt): View
    {
        if (! $prompt->isAvailableForStudents()) {
            abort(404, 'Đề bài không khả dụng.');
        }

        $user = $request->user();

        // Get latest draft if any
        $draft = WritingSubmission::where('user_id', $user->id)
            ->where('writing_prompt_id', $prompt->id)
            ->where('status', SubmissionStatus::DRAFT->value)
            ->latest()
            ->first();

        $prompt->load('topic');

        return view('student.writing.practice', [
            'prompt' => $prompt,
            'draft' => $draft,
        ]);
    }

    /**
     * Save draft endpoint (Auto-save / Manual save).
     */
    public function saveDraft(Request $request, WritingPrompt $prompt): JsonResponse
    {
        $request->validate([
            'essay_content' => ['nullable', 'string'],
            'time_spent_seconds' => ['nullable', 'integer', 'min:0'],
        ]);

        $user = $request->user();
        $content = $request->input('essay_content', '');
        $wordCount = !empty(trim($content)) ? count(preg_split('/\s+/', trim($content))) : 0;
        $timeSpent = (int) $request->input('time_spent_seconds', 0);

        $submission = WritingSubmission::updateOrCreate(
            [
                'user_id' => $user->id,
                'writing_prompt_id' => $prompt->id,
                'status' => SubmissionStatus::DRAFT->value,
            ],
            [
                'essay_content' => $content,
                'word_count' => $wordCount,
                'time_spent_seconds' => $timeSpent,
                'updated_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Đã lưu bản nháp tự động.',
            'word_count' => $wordCount,
            'updated_at' => $submission->updated_at->format('H:i:s'),
        ]);
    }

    /**
     * Final submission of writing essay.
     */
    public function submit(Request $request, WritingPrompt $prompt): RedirectResponse
    {
        $validated = $request->validate([
            'essay_content' => ['required', 'string', 'min:30'],
            'time_spent_seconds' => ['nullable', 'integer', 'min:0'],
        ]);

        $user = $request->user();
        $content = trim($validated['essay_content']);
        $wordCount = count(preg_split('/\s+/', $content));
        $timeSpent = (int) ($validated['time_spent_seconds'] ?? 0);

        // Find existing draft or create new submission
        $submission = WritingSubmission::where('user_id', $user->id)
            ->where('writing_prompt_id', $prompt->id)
            ->where('status', SubmissionStatus::DRAFT->value)
            ->first();

        if (! $submission) {
            $submission = new WritingSubmission();
            $submission->user_id = $user->id;
            $submission->writing_prompt_id = $prompt->id;
        }

        $submission->essay_content = $content;
        $submission->word_count = $wordCount;
        $submission->time_spent_seconds = $timeSpent;
        $submission->status = SubmissionStatus::SUBMITTED;
        $submission->submitted_at = now();
        $submission->save();

        // Perform instant deterministic rule-based evaluation (Zero AI)
        $this->scoringService->scoreSubmission($submission);

        return redirect()->route('student.writing.submissions.show', $submission)
            ->with('success', 'Nộp bài viết thành công! Hệ thống đã hoàn tất đánh giá theo 4 tiêu chí IELTS.');
    }

    /**
     * Display student's writing submissions history.
     */
    public function submissions(Request $request): View
    {
        $user = $request->user();

        $query = WritingSubmission::where('user_id', $user->id)
            ->with(['prompt.topic'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('task_type')) {
            $query->whereHas('prompt', fn ($q) => $q->where('task_type', $request->input('task_type')));
        }

        $submissions = $query->paginate(15)->withQueryString();

        return view('student.writing.submissions.index', [
            'submissions' => $submissions,
            'statuses' => SubmissionStatus::cases(),
            'selectedStatus' => $request->input('status'),
            'selectedTaskType' => $request->input('task_type'),
        ]);
    }

    /**
     * Display individual submission details.
     */
    public function submissionDetail(Request $request, WritingSubmission $submission): View
    {
        // Enforce ownership
        if ($submission->user_id !== $request->user()->id) {
            abort(403, 'Bạn không có quyền truy cập bài nộp này.');
        }

        $submission->load(['prompt.topic', 'prompt.sampleEssays' => fn ($q) => $q->published()->ordered()]);

        return view('student.writing.submissions.show', [
            'submission' => $submission,
            'prompt' => $submission->prompt,
        ]);
    }
}
