<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\OnboardingController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard or login
Route::get('/', function () {
    if (Auth::check()) {
        $user = Auth::user();
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return $user->hasCompletedOnboarding()
            ? redirect()->route('student.dashboard')
            : redirect()->route('onboarding.index');
    }
    return redirect()->route('login');
})->name('home');

// Guest Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:auth-attempts')->name('login.store');

    Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->middleware('throttle:auth-attempts')->name('register.store');

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->middleware('throttle:auth-attempts')->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');
});

// Authenticated Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Shared Profile Routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/info', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/goals', [ProfileController::class, 'updateGoals'])->name('profile.goals');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::delete('/profile/avatar', [ProfileController::class, 'deleteAvatar'])->name('profile.avatar.delete');

    // Student Onboarding (Student role only)
    Route::middleware('role:student')->group(function () {
        Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding.index');
        Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');
    });

    // Student Protected Area (Must be onboarded)
    Route::middleware(['role:student', 'onboarded'])->prefix('student')->name('student.')->group(function () {
        Route::get('/dashboard', [StudentDashboardController::class, 'index'])->name('dashboard');

        // Student Vocabulary Library & Learning
        Route::prefix('vocabulary')->name('vocabulary.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Student\VocabularyLibraryController::class, 'index'])->name('index');
            Route::get('/lessons/{lesson}', [\App\Http\Controllers\Student\VocabularyLibraryController::class, 'showLesson'])->name('lessons.show');

            // Interactive Actions
            Route::post('/items/{item}/status', [\App\Http\Controllers\Student\VocabularyLibraryController::class, 'updateWordStatus'])->name('items.status');
            Route::post('/items/{item}/favorite', [\App\Http\Controllers\Student\VocabularyLibraryController::class, 'toggleFavorite'])->name('items.favorite');
            Route::post('/items/{item}/note', [\App\Http\Controllers\Student\VocabularyLibraryController::class, 'saveNote'])->name('items.note');

            // Practice Quizzes
            Route::post('/lessons/{lesson}/practice', [\App\Http\Controllers\Student\VocabularyPracticeController::class, 'start'])->name('practice.start');
            Route::get('/practice/{session}', [\App\Http\Controllers\Student\VocabularyPracticeController::class, 'show'])->name('practice.show');
            Route::post('/practice/{session}/submit', [\App\Http\Controllers\Student\VocabularyPracticeController::class, 'submit'])->middleware('throttle:vocabulary-practice')->name('practice.submit');
            Route::get('/practice/{session}/result', [\App\Http\Controllers\Student\VocabularyPracticeController::class, 'result'])->name('practice.result');

            // Review Queue & Saved Words
            Route::get('/review', [\App\Http\Controllers\Student\VocabularyReviewController::class, 'index'])->name('review.index');
            Route::post('/review/quiz', [\App\Http\Controllers\Student\VocabularyReviewController::class, 'startQuiz'])->name('review.quiz');
        });

        // Student Writing Practice & Submissions
        Route::prefix('writing')->name('writing.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Student\WritingPracticeController::class, 'index'])->name('index');
            Route::get('/submissions', [\App\Http\Controllers\Student\WritingPracticeController::class, 'submissions'])->name('submissions.index');
            Route::get('/submissions/{submission}', [\App\Http\Controllers\Student\WritingPracticeController::class, 'submissionDetail'])->name('submissions.show');
            Route::get('/{prompt}', [\App\Http\Controllers\Student\WritingPracticeController::class, 'show'])->name('show');
            Route::get('/{prompt}/practice', [\App\Http\Controllers\Student\WritingPracticeController::class, 'practice'])->name('practice');
            Route::post('/{prompt}/draft', [\App\Http\Controllers\Student\WritingPracticeController::class, 'saveDraft'])->middleware('throttle:writing-draft')->name('draft');
            Route::post('/{prompt}/submit', [\App\Http\Controllers\Student\WritingPracticeController::class, 'submit'])->middleware('throttle:writing-submit')->name('submit');
        });
    });

    // Admin Protected Area
    Route::middleware('role:admin')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Admin Vocabulary Management
        Route::prefix('vocabulary')->name('vocabulary.')->group(function () {
            // Topics CRUD
            Route::post('topics/{topic}/toggle-status', [\App\Http\Controllers\Admin\Vocabulary\TopicController::class, 'toggleStatus'])->name('topics.toggle-status');
            Route::resource('topics', \App\Http\Controllers\Admin\Vocabulary\TopicController::class);

            // Lessons CRUD & Preview
            Route::post('lessons/{lesson}/toggle-status', [\App\Http\Controllers\Admin\Vocabulary\LessonController::class, 'toggleStatus'])->name('lessons.toggle-status');
            Route::get('lessons/{lesson}/preview', [\App\Http\Controllers\Admin\Vocabulary\LessonController::class, 'preview'])->name('lessons.preview');
            Route::resource('lessons', \App\Http\Controllers\Admin\Vocabulary\LessonController::class);

            // Vocabulary Items CRUD & Reorder
            Route::post('lessons/{lesson}/items/reorder', [\App\Http\Controllers\Admin\Vocabulary\ItemController::class, 'reorder'])->name('lessons.items.reorder');
            Route::resource('lessons.items', \App\Http\Controllers\Admin\Vocabulary\ItemController::class);
        });

        // Admin Writing Management
        Route::prefix('writing')->name('writing.')->group(function () {
            // Scoring Configuration & Rubrics
            Route::get('scoring', [\App\Http\Controllers\Admin\Writing\ScoringConfigController::class, 'index'])->name('scoring.index');
            Route::put('scoring/rubrics/{rubric}', [\App\Http\Controllers\Admin\Writing\ScoringConfigController::class, 'updateRubric'])->name('scoring.rubrics.update');
            Route::post('scoring/rules/{rule}/toggle', [\App\Http\Controllers\Admin\Writing\ScoringConfigController::class, 'toggleRule'])->name('scoring.rules.toggle');
            Route::put('scoring/rules/{rule}', [\App\Http\Controllers\Admin\Writing\ScoringConfigController::class, 'updateRule'])->name('scoring.rules.update');

            // Prompts CRUD
            Route::post('prompts/{prompt}/toggle-status', [\App\Http\Controllers\Admin\Writing\PromptController::class, 'toggleStatus'])->name('prompts.toggle-status');
            Route::resource('prompts', \App\Http\Controllers\Admin\Writing\PromptController::class);

            // Sample Essays attached to prompts
            Route::resource('prompts.samples', \App\Http\Controllers\Admin\Writing\SampleEssayController::class)
                ->except(['index', 'show'])
                ->parameters(['samples' => 'essay']);

            // Student Submissions Review & Re-scoring
            Route::get('submissions', [\App\Http\Controllers\Admin\Writing\SubmissionController::class, 'index'])->name('submissions.index');
            Route::get('submissions/{submission}', [\App\Http\Controllers\Admin\Writing\SubmissionController::class, 'show'])->name('submissions.show');
            Route::post('submissions/{submission}/rescore', [\App\Http\Controllers\Admin\Writing\SubmissionController::class, 'rescore'])->name('submissions.rescore');
            Route::put('submissions/{submission}/feedback', [\App\Http\Controllers\Admin\Writing\SubmissionController::class, 'updateFeedback'])->name('submissions.feedback');
        });
    });
});
