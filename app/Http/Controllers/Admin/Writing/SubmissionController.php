<?php

namespace App\Http\Controllers\Admin\Writing;

use App\Enums\SubmissionStatus;
use App\Enums\WritingTaskType;
use App\Http\Controllers\Controller;
use App\Models\WritingSubmission;
use App\Services\Scoring\WritingScoringService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SubmissionController extends Controller
{
    /**
     * Display a listing of student writing submissions.
     */
    public function index(Request $request): View
    {
        $query = WritingSubmission::with(['user', 'prompt.topic'])->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($uq) use ($search) {
                    $uq->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('prompt', function ($pq) use ($search) {
                    $pq->where('title', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('task_type')) {
            $query->whereHas('prompt', fn ($q) => $q->where('task_type', $request->input('task_type')));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('band_min')) {
            $query->where('overall_score', '>=', (float) $request->input('band_min'));
        }

        if ($request->filled('band_max')) {
            $query->where('overall_score', '<=', (float) $request->input('band_max'));
        }

        $submissions = $query->paginate(15)->withQueryString();

        // High-level aggregate stats
        $totalSubmissions = WritingSubmission::submitted()->count();
        $gradedCount = WritingSubmission::graded()->count();
        $avgScore = WritingSubmission::graded()->avg('overall_score');

        return view('admin.writing.submissions.index', [
            'submissions' => $submissions,
            'statuses' => SubmissionStatus::cases(),
            'taskTypes' => WritingTaskType::cases(),
            'totalSubmissions' => $totalSubmissions,
            'gradedCount' => $gradedCount,
            'avgScore' => $avgScore ? round($avgScore, 1) : null,
            'search' => $request->input('search'),
            'selectedTaskType' => $request->input('task_type'),
            'selectedStatus' => $request->input('status'),
            'bandMin' => $request->input('band_min'),
            'bandMax' => $request->input('band_max'),
        ]);
    }

    /**
     * Display the specified writing submission with detailed criteria scores and analysis.
     */
    public function show(WritingSubmission $submission): View
    {
        $submission->load(['user', 'prompt.topic', 'prompt.sampleEssays' => fn ($q) => $q->published()->ordered()]);

        return view('admin.writing.submissions.show', [
            'submission' => $submission,
            'prompt' => $submission->prompt,
        ]);
    }

    /**
     * Trigger re-scoring of a submission using the deterministic scoring engine.
     */
    public function rescore(WritingSubmission $submission, WritingScoringService $scoringService): RedirectResponse
    {
        if (empty(trim($submission->essay_content ?? ''))) {
            return back()->with('error', 'Bài nộp không có nội dung để chấm điểm.');
        }

        $scoringService->scoreSubmission($submission);

        return back()->with('success', "Đã chấm lại bài nộp #{$submission->id} thành công! Điểm mới: Band " . number_format($submission->overall_score, 1));
    }

    /**
     * Manually override scores or add examiner feedback.
     */
    public function updateFeedback(Request $request, WritingSubmission $submission): RedirectResponse
    {
        $validated = $request->validate([
            'overall_score' => ['required', 'numeric', 'min:3.5', 'max:9.0'],
            'ta_score' => ['required', 'numeric', 'min:3.5', 'max:9.0'],
            'cc_score' => ['required', 'numeric', 'min:3.5', 'max:9.0'],
            'lr_score' => ['required', 'numeric', 'min:3.5', 'max:9.0'],
            'gra_score' => ['required', 'numeric', 'min:3.5', 'max:9.0'],
            'feedback_notes' => ['nullable', 'string'],
        ]);

        $submission->update($validated);

        return back()->with('success', 'Đã cập nhật điểm số và nhận xét bài làm thành công.');
    }
}
