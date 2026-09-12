<?php

namespace App\Http\Controllers\Admin\Writing;

use App\Enums\ScoringCriterion;
use App\Enums\WritingTaskType;
use App\Http\Controllers\Controller;
use App\Models\ScoringRubric;
use App\Models\ScoringRule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ScoringConfigController extends Controller
{
    /**
     * Display rubrics and deterministic scoring rules dashboard.
     */
    public function index(Request $request): View
    {
        $taskType = $request->query('task_type', WritingTaskType::TASK_1->value);
        $criterion = $request->query('criterion', ScoringCriterion::TASK_ACHIEVEMENT_RESPONSE->value);

        $rubrics = ScoringRubric::forTask($taskType)
            ->forCriterion($criterion)
            ->orderBy('band_score', 'asc')
            ->get();

        $rules = ScoringRule::orderBy('task_type', 'asc')->get();

        return view('admin.writing.scoring.index', [
            'rubrics' => $rubrics,
            'rules' => $rules,
            'taskTypes' => WritingTaskType::cases(),
            'criteria' => ScoringCriterion::cases(),
            'currentTaskType' => $taskType,
            'currentCriterion' => $criterion,
        ]);
    }

    /**
     * Update a specific rubric band descriptor.
     */
    public function updateRubric(Request $request, ScoringRubric $rubric): RedirectResponse
    {
        $validated = $request->validate([
            'description' => ['required', 'string'],
        ]);

        $rubric->update([
            'description' => $validated['description'],
        ]);

        return back()->with('success', "Đã cập nhật tiêu chí chấm cho Band {$rubric->band_score} ({$rubric->criterion->shortCode()})!");
    }

    /**
     * Toggle active status of a scoring rule.
     */
    public function toggleRule(ScoringRule $rule): RedirectResponse
    {
        $rule->update([
            'is_active' => ! $rule->is_active,
        ]);

        $statusText = $rule->is_active ? 'Kích hoạt' : 'Tạm tắt';
        return back()->with('success', "Đã {$statusText} quy tắc: {$rule->name}");
    }

    /**
     * Update scoring rule parameters.
     */
    public function updateRule(Request $request, ScoringRule $rule): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'parameters' => ['nullable', 'json'],
        ]);

        $params = $rule->parameters;
        if ($request->filled('parameters')) {
            $params = json_decode($validated['parameters'], true);
        }

        $rule->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'parameters' => $params,
        ]);

        return back()->with('success', "Đã cập nhật cấu hình quy tắc: {$rule->name}");
    }
}
