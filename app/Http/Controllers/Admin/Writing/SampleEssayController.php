<?php

namespace App\Http\Controllers\Admin\Writing;

use App\Http\Controllers\Controller;
use App\Models\SampleEssay;
use App\Models\WritingPrompt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SampleEssayController extends Controller
{
    /**
     * Show form for adding sample essay to prompt.
     */
    public function create(WritingPrompt $prompt): View
    {
        return view('admin.writing.sample_essays.create', [
            'prompt' => $prompt,
        ]);
    }

    /**
     * Store new sample essay.
     */
    public function store(Request $request, WritingPrompt $prompt): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'band_score' => ['required', 'numeric', 'min:4.0', 'max:9.0'],
            'author_type' => ['required', 'string', 'max:100'],
            'essay_text' => ['required', 'string', 'min:50'],
            'analysis_notes' => ['nullable', 'string'],
            'highlighted_vocabulary' => ['nullable', 'array'],
            'highlighted_vocabulary.*.word' => ['required_with:highlighted_vocabulary', 'string'],
            'highlighted_vocabulary.*.meaning' => ['nullable', 'string'],
            'highlighted_vocabulary.*.band' => ['nullable', 'string'],
            'highlighted_vocabulary.*.note' => ['nullable', 'string'],
            'highlighted_structures' => ['nullable', 'array'],
            'highlighted_structures.*.pattern' => ['required_with:highlighted_structures', 'string'],
            'highlighted_structures.*.note' => ['nullable', 'string'],
            'status' => ['required', 'in:published,draft'],
            'order_index' => ['nullable', 'integer', 'min:0'],
        ]);

        // Clean arrays
        $vocab = [];
        if (!empty($validated['highlighted_vocabulary'])) {
            foreach ($validated['highlighted_vocabulary'] as $v) {
                if (!empty(trim($v['word'] ?? ''))) {
                    $vocab[] = [
                        'word' => trim($v['word']),
                        'meaning' => trim($v['meaning'] ?? ''),
                        'band' => trim($v['band'] ?? ''),
                        'note' => trim($v['note'] ?? ''),
                    ];
                }
            }
        }

        $structures = [];
        if (!empty($validated['highlighted_structures'])) {
            foreach ($validated['highlighted_structures'] as $s) {
                if (!empty(trim($s['pattern'] ?? ''))) {
                    $structures[] = [
                        'pattern' => trim($s['pattern']),
                        'note' => trim($s['note'] ?? ''),
                    ];
                }
            }
        }

        $wordCount = count(preg_split('/\s+/', trim($validated['essay_text'])));

        SampleEssay::create([
            'writing_prompt_id' => $prompt->id,
            'title' => $validated['title'],
            'band_score' => $validated['band_score'],
            'author_type' => $validated['author_type'],
            'essay_text' => $validated['essay_text'],
            'word_count' => $wordCount,
            'analysis_notes' => $validated['analysis_notes'] ?? null,
            'highlighted_vocabulary' => $vocab,
            'highlighted_structures' => $structures,
            'status' => $validated['status'],
            'order_index' => $validated['order_index'] ?? 0,
        ]);

        return redirect()->route('admin.writing.prompts.show', $prompt)
            ->with('success', 'Đã thêm bài mẫu Writing thành công!');
    }

    /**
     * Show form for editing sample essay.
     */
    public function edit(WritingPrompt $prompt, SampleEssay $essay): View
    {
        return view('admin.writing.sample_essays.edit', [
            'prompt' => $prompt,
            'essay' => $essay,
        ]);
    }

    /**
     * Update sample essay.
     */
    public function update(Request $request, WritingPrompt $prompt, SampleEssay $essay): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'band_score' => ['required', 'numeric', 'min:4.0', 'max:9.0'],
            'author_type' => ['required', 'string', 'max:100'],
            'essay_text' => ['required', 'string', 'min:50'],
            'analysis_notes' => ['nullable', 'string'],
            'highlighted_vocabulary' => ['nullable', 'array'],
            'highlighted_vocabulary.*.word' => ['required_with:highlighted_vocabulary', 'string'],
            'highlighted_vocabulary.*.meaning' => ['nullable', 'string'],
            'highlighted_vocabulary.*.band' => ['nullable', 'string'],
            'highlighted_vocabulary.*.note' => ['nullable', 'string'],
            'highlighted_structures' => ['nullable', 'array'],
            'highlighted_structures.*.pattern' => ['required_with:highlighted_structures', 'string'],
            'highlighted_structures.*.note' => ['nullable', 'string'],
            'status' => ['required', 'in:published,draft'],
            'order_index' => ['nullable', 'integer', 'min:0'],
        ]);

        // Clean arrays
        $vocab = [];
        if (!empty($validated['highlighted_vocabulary'])) {
            foreach ($validated['highlighted_vocabulary'] as $v) {
                if (!empty(trim($v['word'] ?? ''))) {
                    $vocab[] = [
                        'word' => trim($v['word']),
                        'meaning' => trim($v['meaning'] ?? ''),
                        'band' => trim($v['band'] ?? ''),
                        'note' => trim($v['note'] ?? ''),
                    ];
                }
            }
        }

        $structures = [];
        if (!empty($validated['highlighted_structures'])) {
            foreach ($validated['highlighted_structures'] as $s) {
                if (!empty(trim($s['pattern'] ?? ''))) {
                    $structures[] = [
                        'pattern' => trim($s['pattern']),
                        'note' => trim($s['note'] ?? ''),
                    ];
                }
            }
        }

        $wordCount = count(preg_split('/\s+/', trim($validated['essay_text'])));

        $essay->update([
            'title' => $validated['title'],
            'band_score' => $validated['band_score'],
            'author_type' => $validated['author_type'],
            'essay_text' => $validated['essay_text'],
            'word_count' => $wordCount,
            'analysis_notes' => $validated['analysis_notes'] ?? null,
            'highlighted_vocabulary' => $vocab,
            'highlighted_structures' => $structures,
            'status' => $validated['status'],
            'order_index' => $validated['order_index'] ?? 0,
        ]);

        return redirect()->route('admin.writing.prompts.show', $prompt)
            ->with('success', 'Đã cập nhật bài mẫu thành công!');
    }

    /**
     * Delete sample essay.
     */
    public function destroy(WritingPrompt $prompt, SampleEssay $essay): RedirectResponse
    {
        $essay->delete();

        return redirect()->route('admin.writing.prompts.show', $prompt)
            ->with('success', 'Đã xóa bài mẫu thành công!');
    }
}
