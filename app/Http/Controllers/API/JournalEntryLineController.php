<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreJournalEntryLineRequest;
use App\Http\Requests\UpdateJournalEntryLineRequest;
use App\Http\Resources\JournalEntryLineResource;
use App\Services\JournalEntryLineService;
use App\Models\JournalEntryLine;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class JournalEntryLineController extends BaseController
{
    public function __construct(
        protected JournalEntryLineService $journalEntryLineService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $this->authorize('viewAny', JournalEntryLine::class);

        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $journalEntryLines = $this->journalEntryLineService->getPaginated($perPage);

            return $this->sendPaginated(
                JournalEntryLineResource::collection($journalEntryLines),
                'JournalEntryLines başarıyla getirildi'
            );
        }

        $journalEntryLines = $this->journalEntryLineService->getAll();

        return JournalEntryLineResource::collection($journalEntryLines);
    }

    public function store(StoreJournalEntryLineRequest $request): JsonResponse
    {
        $this->authorize('create', JournalEntryLine::class);

        $journalEntryLine = $this->journalEntryLineService->create($request->validated());

        return $this->sendSuccess(
            new JournalEntryLineResource($journalEntryLine),
            'JournalEntryLine başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $journalEntryLine = $this->journalEntryLineService->findByIdOrFail($id);

        return $this->sendSuccess(
            new JournalEntryLineResource($journalEntryLine),
            'JournalEntryLine başarıyla getirildi'
        );
    }

    public function update(UpdateJournalEntryLineRequest $request, string $id): JsonResponse
    {
        $journalEntryLine = $this->journalEntryLineService->update($id, $request->validated());

        return $this->sendSuccess(
            new JournalEntryLineResource($journalEntryLine),
            'JournalEntryLine başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->journalEntryLineService->delete($id);

        return $this->sendSuccess(
            null,
            'JournalEntryLine başarıyla silindi'
        );
    }
}
