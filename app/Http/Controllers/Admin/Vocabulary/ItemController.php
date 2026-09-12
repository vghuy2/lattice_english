<?php

namespace App\Http\Controllers\Admin\Vocabulary;

use App\Enums\PartOfSpeech;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Vocabulary\StoreVocabularyItemRequest;
use App\Http\Requests\Admin\Vocabulary\UpdateVocabularyItemRequest;
use App\Models\VocabularyItem;
use App\Models\VocabularyLesson;
use App\Services\VocabularyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemController extends Controller
{
    public function __construct(
        protected VocabularyService $vocabularyService
    ) {}

    /**
     * Display items of a specific lesson.
     */
    public function index(Request $request, VocabularyLesson $lesson): View
    {
        $query = $lesson->items()->ordered();

        if ($request->filled('search')) {
            $query->search($request->input('search'));
        }

        $items = $query->paginate(20)->withQueryString();

        return view('admin.vocabulary.items.index', [
            'lesson' => $lesson,
            'items' => $items,
            'search' => $request->input('search'),
        ]);
    }

    /**
     * Show the form for creating a new vocabulary item.
     */
    public function create(VocabularyLesson $lesson): View
    {
        $maxOrder = $lesson->items()->max('sort_order') ?? 0;

        return view('admin.vocabulary.items.create', [
            'lesson' => $lesson,
            'partsOfSpeech' => PartOfSpeech::cases(),
            'nextOrder' => $maxOrder + 1,
        ]);
    }

    /**
     * Store a newly created item.
     */
    public function store(StoreVocabularyItemRequest $request, VocabularyLesson $lesson): RedirectResponse
    {
        $data = $request->validated();
        $data['lesson_id'] = $lesson->id;

        $audioFile = $request->hasFile('audio') ? $request->file('audio') : null;
        if (! $audioFile && $request->filled('audio') && is_string($request->input('audio'))) {
            $data['audio'] = $request->input('audio');
            $audioFile = null;
        }

        $item = $this->vocabularyService->saveItem($data, null, $audioFile);

        return redirect()->route('admin.vocabulary.lessons.items.index', $lesson)
            ->with('success', "Đã thêm từ \"{$item->word}\" vào bài học thành công.");
    }

    /**
     * Show the form for editing the specified item.
     */
    public function edit(VocabularyLesson $lesson, VocabularyItem $item): View
    {
        return view('admin.vocabulary.items.edit', [
            'lesson' => $lesson,
            'item' => $item,
            'partsOfSpeech' => PartOfSpeech::cases(),
        ]);
    }

    /**
     * Update the specified item.
     */
    public function update(UpdateVocabularyItemRequest $request, VocabularyLesson $lesson, VocabularyItem $item): RedirectResponse
    {
        $data = $request->validated();

        $audioFile = $request->hasFile('audio') ? $request->file('audio') : null;
        if (! $audioFile && $request->filled('audio') && is_string($request->input('audio'))) {
            $data['audio'] = $request->input('audio');
            $audioFile = null;
        }

        $this->vocabularyService->saveItem($data, $item, $audioFile);

        return redirect()->route('admin.vocabulary.lessons.items.index', $lesson)
            ->with('success', "Đã cập nhật từ vựng \"{$item->word}\" thành công.");
    }

    /**
     * Remove the specified item.
     */
    public function destroy(VocabularyLesson $lesson, VocabularyItem $item): RedirectResponse
    {
        $word = $item->word;
        $item->delete();

        return redirect()->route('admin.vocabulary.lessons.items.index', $lesson)
            ->with('success', "Đã xóa từ \"{$word}\" khỏi bài học.");
    }

    /**
     * Reorder items inside a lesson.
     */
    public function reorder(Request $request, VocabularyLesson $lesson): JsonResponse|RedirectResponse
    {
        $orders = $request->input('orders', []);

        foreach ($orders as $order => $itemId) {
            VocabularyItem::where('lesson_id', $lesson->id)
                ->where('id', $itemId)
                ->update(['sort_order' => $order]);
        }

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Cập nhật thứ tự từ vựng thành công.']);
        }

        return back()->with('success', 'Đã lưu thứ tự từ vựng.');
    }
}
