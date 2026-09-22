<?php

namespace App\Http\Controllers\Admin\Writing;

use App\Enums\ContentStatus;
use App\Enums\VocabularyLevel;
use App\Enums\WritingPromptType;
use App\Enums\WritingTaskType;
use App\Http\Controllers\Controller;
use App\Models\VocabularyTopic;
use App\Models\WritingPrompt;
use App\Services\Cache\RedisCacheKeys;
use Illuminate\Http\JsonResponse;
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

        RedisCacheKeys::invalidatePrompt($prompt->id);

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

        RedisCacheKeys::invalidatePrompt($prompt->id);

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

        $promptId = $prompt->id;
        $prompt->delete();

        RedisCacheKeys::invalidatePrompt($promptId);

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

        RedisCacheKeys::invalidatePrompt($prompt->id);

        return back()->with('success', "Đã chuyển trạng thái đề bài sang: {$newStatus->label()}");
    }

    /**
     * Parse and structure IELTS Writing prompt from OCR text or uploaded diagram image.
     */
    public function ocrImage(Request $request): JsonResponse
    {
        $rawText = $request->input('raw_text');

        if ($request->hasFile('image')) {
            $request->validate([
                'image' => ['required', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:5120'],
            ]);
        }

        if (empty($rawText) && ! $request->hasFile('image')) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy nội dung văn bản hoặc hình ảnh để phân tích.',
            ], 422);
        }

        $parsed = $this->parseStructuredPrompt((string) $rawText);

        return response()->json([
            'success' => true,
            'data' => $parsed,
        ]);
    }

    /**
     * Smart parser for extracted IELTS Writing prompt text.
     */
    protected function parseStructuredPrompt(string $rawText): array
    {
        $cleaned = trim(preg_replace('/\r\n|\r/', "\n", $rawText));
        $lines = array_values(array_filter(array_map('trim', explode("\n", $cleaned))));
        $textLower = mb_strtolower($cleaned);

        // Detect Task Type (Task 1 vs Task 2)
        $task1Keywords = ['chart', 'graph', 'diagram', 'table', 'pie', 'bar', 'map', 'process', 'summarise the information', 'summarize the information', 'features, and make comparisons'];
        $task2Keywords = ['agree or disagree', 'discuss both', 'advantages and disadvantages', 'to what extent', 'positive or negative', 'opinion'];

        $task1Matches = 0;
        foreach ($task1Keywords as $kw) {
            if (str_contains($textLower, $kw)) {
                $task1Matches++;
            }
        }

        $task2Matches = 0;
        foreach ($task2Keywords as $kw) {
            if (str_contains($textLower, $kw)) {
                $task2Matches++;
            }
        }

        $taskType = ($task1Matches >= $task2Matches && $task1Matches > 0) ? WritingTaskType::TASK_1->value : WritingTaskType::TASK_2->value;
        if (str_contains($textLower, 'task 1') || str_contains($textLower, 'writing task 1')) {
            $taskType = WritingTaskType::TASK_1->value;
        } elseif (str_contains($textLower, 'task 2') || str_contains($textLower, 'writing task 2')) {
            $taskType = WritingTaskType::TASK_2->value;
        }

        // Detect Prompt Type
        $promptType = $taskType === WritingTaskType::TASK_1->value ? WritingPromptType::BAR_CHART->value : WritingPromptType::OPINION->value;
        if ($taskType === WritingTaskType::TASK_1->value) {
            if (str_contains($textLower, 'line graph') || str_contains($textLower, 'line chart')) {
                $promptType = WritingPromptType::LINE_GRAPH->value;
            } elseif (str_contains($textLower, 'pie chart')) {
                $promptType = WritingPromptType::PIE_CHART->value;
            } elseif (str_contains($textLower, 'table')) {
                $promptType = WritingPromptType::TABLE->value;
            } elseif (str_contains($textLower, 'map') || str_contains($textLower, 'maps') || str_contains($textLower, 'plan')) {
                $promptType = WritingPromptType::MAP->value;
            } elseif (str_contains($textLower, 'process') || str_contains($textLower, 'cycle') || str_contains($textLower, 'flow chart')) {
                $promptType = WritingPromptType::PROCESS_DIAGRAM->value;
            } elseif (str_contains($textLower, 'bar chart') || str_contains($textLower, 'bar graph')) {
                $promptType = WritingPromptType::BAR_CHART->value;
            }
        } else {
            if (str_contains($textLower, 'discuss both')) {
                $promptType = WritingPromptType::DISCUSSION->value;
            } elseif (str_contains($textLower, 'advantages') && str_contains($textLower, 'disadvantages')) {
                $promptType = WritingPromptType::ADVANTAGE_DISADVANTAGE->value;
            } elseif (str_contains($textLower, 'cause') || str_contains($textLower, 'problem') || str_contains($textLower, 'solution')) {
                $promptType = WritingPromptType::PROBLEM_SOLUTION->value;
            } else {
                $promptType = WritingPromptType::OPINION->value;
            }
        }

        // Extract Prompt Statement
        $promptText = '';
        if (preg_match('/(The\s+[\w\s,]+(?:below|above)?\s+(?:shows|illustrates|gives|compares|presents|depicts)[\s\S]*?(?:where relevant\.|150 words\.|words\.|\.))/i', $cleaned, $matches)) {
            $promptText = trim($matches[0]);
        } elseif (preg_match('/(You should spend about (?:20|40) minutes on this task\.[\s\S]*)/i', $cleaned, $matches)) {
            $promptText = trim($matches[1]);
        } elseif (! empty($lines)) {
            $promptText = implode(' ', array_slice($lines, 0, 4));
        }

        $promptText = (string) preg_replace('/\s+/', ' ', $promptText);

        // Derive Title from Prompt Text or first line
        if (preg_match('/(?:shows|illustrates|gives information about|compares)\s+([^.]+)/i', $promptText, $titleMatches)) {
            $rawTitle = trim($titleMatches[1]);
            $rawTitle = (string) preg_replace('/\s+Summarise.*$/i', '', $rawTitle);
            $rawTitle = (string) preg_replace('/\s+Write at least.*$/i', '', $rawTitle);
            $title = Str::title(Str::limit(trim($rawTitle), 80, ''));
        } elseif (! empty($lines)) {
            $firstLine = reset($lines);
            $title = Str::title(Str::limit($firstLine, 80, ''));
        } else {
            $title = $taskType === WritingTaskType::TASK_1->value ? 'IELTS Writing Task 1 Diagram' : 'IELTS Writing Task 2 Essay';
        }

        // Suggested Outline based on Task Type
        if ($taskType === WritingTaskType::TASK_1->value) {
            $outline = [
                ['section' => 'Introduction', 'hint' => 'Paraphrase lại câu hỏi đề bài bằng từ đồng nghĩa (1-2 câu).'],
                ['section' => 'Overview', 'hint' => 'Nêu 2-3 xu hướng bao quát nhất hoặc các điểm cao nhất/thấp nhất (không đưa số liệu chi tiết).'],
                ['section' => 'Body Paragraph 1', 'hint' => 'Mô tả chi tiết nhóm số liệu nổi bật đầu tiên kèm số liệu và so sánh cụ thể.'],
                ['section' => 'Body Paragraph 2', 'hint' => 'Mô tả chi tiết nhóm số liệu còn lại, chỉ ra sự tương phản hoặc tương đồng.'],
            ];
            $minWords = 150;
            $timeLimit = 20;
        } else {
            $outline = [
                ['section' => 'Introduction', 'hint' => 'Dẫn dắt chủ đề (paraphrase đề bài) và nêu rõ luận điểm / lập trường cá nhân (Thesis Statement).'],
                ['section' => 'Body Paragraph 1', 'hint' => 'Luận điểm thứ nhất: Topic sentence + giải thích nguyên nhân + ví dụ thực tế minh họa.'],
                ['section' => 'Body Paragraph 2', 'hint' => 'Luận điểm thứ hai: Topic sentence + phân tích sâu chiều ngược lại hoặc bổ trợ + ví dụ.'],
                ['section' => 'Conclusion', 'hint' => 'Khẳng định lại quan điểm chính và đưa ra lời kết/dự đoán tương lai (1-2 câu).'],
            ];
            $minWords = 250;
            $timeLimit = 40;
        }

        return [
            'title' => $title,
            'prompt_text' => $promptText ?: $cleaned,
            'task_type' => $taskType,
            'prompt_type' => $promptType,
            'min_words' => $minWords,
            'time_limit_minutes' => $timeLimit,
            'suggested_outline' => $outline,
            'raw_text' => $cleaned,
        ];
    }
}
