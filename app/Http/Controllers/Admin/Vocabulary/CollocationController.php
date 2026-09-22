<?php

namespace App\Http\Controllers\Admin\Vocabulary;

use App\Enums\CollocationType;
use App\Enums\ContentStatus;
use App\Enums\VocabularyLevel;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Vocabulary\StoreCollocationRequest;
use App\Http\Requests\Admin\Vocabulary\UpdateCollocationRequest;
use App\Models\Collocation;
use App\Models\VocabularyTopic;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CollocationController extends Controller
{
    /**
     * Display a listing of collocations with advanced filtering.
     */
    public function index(Request $request): View
    {
        $query = Collocation::with('topic')->ordered();

        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        if ($request->filled('topic_id')) {
            $query->where('topic_id', $request->input('topic_id'));
        }

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        if ($request->filled('level')) {
            $query->where('level', $request->input('level'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $collocations = $query->paginate(15)->withQueryString();

        // High-level statistics
        $stats = [
            'total' => Collocation::count(),
            'published' => Collocation::where('status', ContentStatus::PUBLISHED->value)->count(),
            'draft' => Collocation::where('status', ContentStatus::DRAFT->value)->count(),
            'academic_phrases' => Collocation::where('type', CollocationType::PHRASE->value)->count(),
        ];

        return view('admin.vocabulary.collocations.index', [
            'collocations' => $collocations,
            'topics' => VocabularyTopic::ordered()->get(),
            'types' => CollocationType::cases(),
            'levels' => VocabularyLevel::cases(),
            'statuses' => ContentStatus::cases(),
            'stats' => $stats,
            'search' => $request->input('search'),
            'selectedTopicId' => $request->input('topic_id'),
            'selectedType' => $request->input('type'),
            'selectedLevel' => $request->input('level'),
            'selectedStatus' => $request->input('status'),
        ]);
    }

    /**
     * Show the form for creating a new collocation.
     */
    public function create(Request $request): View
    {
        $maxOrder = Collocation::max('sort_order') ?? 0;

        return view('admin.vocabulary.collocations.create', [
            'topics' => VocabularyTopic::ordered()->get(),
            'types' => CollocationType::cases(),
            'levels' => VocabularyLevel::cases(),
            'statuses' => ContentStatus::cases(),
            'selectedTopicId' => $request->input('topic_id'),
            'nextOrder' => $maxOrder + 1,
        ]);
    }

    /**
     * Store a newly created collocation.
     */
    public function store(StoreCollocationRequest $request): RedirectResponse
    {
        $collocation = Collocation::create($request->validated());

        return redirect()->route('admin.vocabulary.collocations.index')
            ->with('success', "Đã thêm collocation \"{$collocation->phrase}\" thành công.");
    }

    /**
     * Show the form for editing the specified collocation.
     */
    public function edit(Collocation $collocation): View
    {
        return view('admin.vocabulary.collocations.edit', [
            'collocation' => $collocation,
            'topics' => VocabularyTopic::ordered()->get(),
            'types' => CollocationType::cases(),
            'levels' => VocabularyLevel::cases(),
            'statuses' => ContentStatus::cases(),
        ]);
    }

    /**
     * Update the specified collocation.
     */
    public function update(UpdateCollocationRequest $request, Collocation $collocation): RedirectResponse
    {
        $collocation->update($request->validated());

        return redirect()->route('admin.vocabulary.collocations.index')
            ->with('success', "Đã cập nhật collocation \"{$collocation->phrase}\" thành công.");
    }

    /**
     * Remove the specified collocation.
     */
    public function destroy(Collocation $collocation): RedirectResponse
    {
        $phrase = $collocation->phrase;
        $collocation->delete();

        return redirect()->route('admin.vocabulary.collocations.index')
            ->with('success', "Đã xóa collocation \"{$phrase}\".");
    }

    /**
     * Quick toggle collocation status (published / draft).
     */
    public function toggleStatus(Collocation $collocation): RedirectResponse
    {
        $newStatus = $collocation->status === ContentStatus::PUBLISHED
            ? ContentStatus::DRAFT
            : ContentStatus::PUBLISHED;

        $collocation->update(['status' => $newStatus]);

        return back()->with('success', "Đã chuyển trạng thái collocation \"{$collocation->phrase}\" sang \"{$newStatus->label()}\".");
    }
}
