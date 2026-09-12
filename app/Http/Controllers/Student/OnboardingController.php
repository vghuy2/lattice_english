<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Http\Requests\Student\OnboardingRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OnboardingController extends Controller
{
    /**
     * Show onboarding form.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $user = $request->user();

        if ($user->hasCompletedOnboarding()) {
            return redirect()->route('student.dashboard');
        }

        return view('onboarding.index', [
            'user' => $user,
        ]);
    }

    /**
     * Save onboarding information.
     */
    public function store(OnboardingRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->update([
            'current_band' => $request->input('current_band'),
            'target_band' => $request->input('target_band'),
            'test_type' => $request->input('test_type'),
            'target_date' => $request->input('target_date'),
            'study_days_per_week' => $request->input('study_days_per_week'),
            'study_goal' => $request->input('study_goal'),
            'onboarding_completed_at' => now(),
        ]);

        return redirect()->route('student.dashboard')->with('success', 'Thiết lập lộ trình học tập thành công! Chúc bạn đạt kết quả mục tiêu.');
    }
}
