<?php

namespace App\Http\Controllers\Student;

use App\Enums\WordStudyStatus;
use App\Http\Controllers\Controller;
use App\Models\StudentVocabularyReview;
use App\Models\WritingSubmission;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ErrorNotebookController extends Controller
{
    /**
     * Display student error notebook with flagged words and writing feedback.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        // Weak / Review-needed vocabulary items
        $weakWords = StudentVocabularyReview::where('user_id', $user->id)
            ->where(function ($q) {
                $q->where('status', WordStudyStatus::REVIEW_NEEDED)
                  ->orWhere('incorrect_count', '>', 0);
            })
            ->with(['vocabularyItem.lesson.topic'])
            ->orderByDesc('incorrect_count')
            ->paginate(10, ['*'], 'words_page')
            ->withQueryString();

        // Writing submissions with feedback notes and scoring breakdown
        $writingErrors = WritingSubmission::where('user_id', $user->id)
            ->graded()
            ->with(['prompt.topic'])
            ->latest()
            ->take(5)
            ->get();

        return view('student.error_notebook', [
            'weakWords' => $weakWords,
            'writingErrors' => $writingErrors,
        ]);
    }
}
