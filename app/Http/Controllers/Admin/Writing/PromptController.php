<?php

namespace App\Http\Controllers\Admin\Writing;

use App\Enums\ContentStatus;
use App\Enums\VocabularyLevel;
use App\Enums\WritingPromptType;
use App\Enums\WritingTaskType;
use App\Http\Controllers\Controller;
use App\Models\VocabularyTopic;
use App\Models\WritingPrompt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PromptController extends Controller
{
    /**
     * Display writing prompts list.
     */
    public function index(Request $request): View
    {
        $query = WritingPrompt::with(['topic', 'sampleEssays'])->ordered();

        if ($request->filled('task_type')) {
            $query->where('task_type', $request->input('task_type'));
        }

        if ($request->filled('prompt_type')) {
            $query->where('prompt_type', $request->input('prompt_type'));
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

        $prompts = $query->paginate(15)->withQueryString();

        return view('admin.writing.prompts.index', [
            'prompts' => $prompts,
            'taskTypes' => WritingTaskType::cases(),
            'promptTypes' => WritingPromptType::cases(),
            'levels' => VocabularyLevel::cases(),
            'statuses' => ContentStatus::cases(),
            'selectedTaskType' => $request->input('task_type'),
            'selectedPromptType' => $request->input('prompt_type'),
            'selectedLevel' => $request->input('level'),
            'selectedStatus' => $request->input('status'),
            'search' => $request->input('search'),
        ]);
    }

    /**
     * Show form for creating a new writing prompt.
     */
    public function create(): View
    {
        return view('admin.writing.prompts.create', [
            'topics' => VocabularyTopic::ordered()->get(),
            'taskTypes' => WritingTaskType::cases(),
            'promptTypesGrouped' => WritingPromptType::groupedByTask(),
            'levels' => VocabularyLevel::cases(),
            'statuses' => ContentStatus::cases(),
        ]);
    }

    /**
     * Store newly created writing prompt.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'task_type' => ['required', Rule::enum(WritingTaskType::class)],
            'prompt_type' => ['required', Rule::enum(WritingPromptType::class)],
            'topic_id' => ['nullable', 'exists:vocabulary_topics,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:writing_prompts,slug'],
            'prompt_text' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:3072'],
            'level' => ['required', Rule::enum(VocabularyLevel::class)],
            'min_words' => ['required', 'integer', 'min:50', 'max:500'],
            'time_limit_minutes' => ['required', 'integer', 'min:5', 'max:120'],
            'guidance' => ['nullable', 'string'],
            'outline_sections' => ['nullable', 'array'],
            'outline_sections.*.section' => ['required_with:outline_sections', 'string'],
            'outline_sections.*.hint' => ['nullable', 'string'],
            'status' => ['required', Rule::enum(ContentStatus::class)],
            'published_at' => ['nullable', 'date'],
            'order_index' => ['nullable', 'integer', 'min:0'],
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('writing/charts', 'public');
        }

        // Format outline
        $suggestedOutline = [];
        if (!empty($validated['outline_sections'])) {
            foreach ($validated['outline_sections'] as $sec) {
                if (!empty(trim($sec['section'] ?? ''))) {
                    $suggestedOutline[] = [
                        'section' => trim($sec['section']),
                        'hint' => trim($sec['hint'] ?? ''),
                    ];
                }
            }
        }

        $slug = !empty($validated['slug']) ? Str::slug($validated['slug']) : Str::slug($validated['title']);

        $prompt = WritingPrompt::create([
            'task_type' => $validated['task_type'],
            'prompt_type' => $validated['prompt_type'],
            'topic_id' => $validated['topic_id'] ?? null,
            'title' => $validated['title'],
            'slug' => $slug,
            'prompt_text' => $validated['prompt_text'],
            'image_path' => $imagePath,
            'level' => $validated['level'],
            'min_words' => $validated['min_words'],
            'time_limit_minutes' => $validated['time_limit_minutes'],
            'guidance' => $validated['guidance'] ?? null,
            'suggested_outline' => $suggestedOutline,
            'status' => $validated['status'],
            'published_at' => $validated['published_at'] ?? ($validated['status'] === ContentStatus::PUBLISHED->value ? now() : null),
            'order_index' => $validated['order_index'] ?? 0,
        ]);

        return redirect()->route('admin.writing.prompts.show', $prompt)
            ->with('success', 'Đã tạo đề bài Writing thành công!');
    }

    /**
     * Show prompt details, outline and attached sample essays.
     */
    public function show(WritingPrompt $prompt): View
    {
        $prompt->load(['topic', 'sampleEssays' => fn ($q) => $q->ordered()]);

        return view('admin.writing.prompts.show', [
            'prompt' => $prompt,
        ]);
    }

    /**
     * Show form for editing a writing prompt.
     */
    public function edit(WritingPrompt $prompt): View
    {
        return view('admin.writing.prompts.edit', [
            'prompt' => $prompt,
            'topics' => VocabularyTopic::ordered()->get(),
            'taskTypes' => WritingTaskType::cases(),
            'promptTypesGrouped' => WritingPromptType::groupedByTask(),
            'levels' => VocabularyLevel::cases(),
            'statuses' => ContentStatus::cases(),
        ]);
    }

    /**
     * Update writing prompt.
     */
    public function update(Request $request, WritingPrompt $prompt): RedirectResponse
    {
        $validated = $request->validate([
            'task_type' => ['required', Rule::enum(WritingTaskType::class)],
            'prompt_type' => ['required', Rule::enum(WritingPromptType::class)],
            'topic_id' => ['nullable', 'exists:vocabulary_topics,id'],
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('writing_prompts', 'slug')->ignore($prompt->id)],
            'prompt_text' => ['required', 'string'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:3072'],
            'remove_image' => ['nullable', 'boolean'],
            'level' => ['required', Rule::enum(VocabularyLevel::class)],
            'min_words' => ['required', 'integer', 'min:50', 'max:500'],
            'time_limit_minutes' => ['required', 'integer', 'min:5', 'max:120'],
            'guidance' => ['nullable', 'string'],
            'outline_sections' => ['nullable', 'array'],
            'outline_sections.*.section' => ['required_with:outline_sections', 'string'],
            'outline_sections.*.hint' => ['nullable', 'string'],
            'status' => ['required', Rule::enum(ContentStatus::class)],
            'published_at' => ['nullable', 'date'],
            'order_index' => ['nullable', 'integer', 'min:0'],
        ]);

        $imagePath = $prompt->image_path;
        if ($request->boolean('remove_image')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = null;
        } elseif ($request->hasFile('image')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('writing/charts', 'public');
        }

        // Format outline
        $suggestedOutline = [];
        if (!empty($validated['outline_sections'])) {
            foreach ($validated['outline_sections'] as $sec) {
                if (!empty(trim($sec['section'] ?? ''))) {
                    $suggestedOutline[] = [
                        'section' => trim($sec['section']),
                        'hint' => trim($sec['hint'] ?? ''),
                    ];
                }
            }
        }

        $prompt->update([
            'task_type' => $validated['task_type'],
            'prompt_type' => $validated['prompt_type'],
            'topic_id' => $validated['topic_id'] ?? null,
            'title' => $validated['title'],
            'slug' => Str::slug($validated['slug']),
            'prompt_text' => $validated['prompt_text'],
            'image_path' => $imagePath,
            'level' => $validated['level'],
            'min_words' => $validated['min_words'],
            'time_limit_minutes' => $validated['time_limit_minutes'],
            'guidance' => $validated['guidance'] ?? null,
            'suggested_outline' => $suggestedOutline,
            'status' => $validated['status'],
            'published_at' => $validated['published_at'] ?? ($validated['status'] === ContentStatus::PUBLISHED->value && !$prompt->published_at ? now() : $prompt->published_at),
            'order_index' => $validated['order_index'] ?? 0,
        ]);

        return redirect()->route('admin.writing.prompts.show', $prompt)
            ->with('success', 'Đã cập nhật đề bài Writing thành công!');
    }

    /**
     * Delete writing prompt.
     */
    public function destroy(WritingPrompt $prompt): RedirectResponse
    {
        if ($prompt->image_path && Storage::disk('public')->exists($prompt->image_path)) {
            Storage::disk('public')->delete($prompt->image_path);
        }

        $prompt->delete();

        return redirect()->route('admin.writing.prompts.index')
            ->with('success', 'Đã xóa đề bài Writing thành công!');
    }

    /**
     * Quick toggle published status.
     */
    public function toggleStatus(WritingPrompt $prompt): RedirectResponse
    {
        $newStatus = $prompt->status === ContentStatus::PUBLISHED ? ContentStatus::DRAFT : ContentStatus::PUBLISHED;
        
        $prompt->update([
            'status' => $newStatus,
            'published_at' => $newStatus === ContentStatus::PUBLISHED && ! $prompt->published_at ? now() : $prompt->published_at,
        ]);

        return back()->with('success', "Đã chuyển trạng thái đề bài sang: {$newStatus->label()}");
    }
}
