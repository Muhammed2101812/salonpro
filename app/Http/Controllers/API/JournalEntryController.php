<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\StoreJournalEntryRequest;
use App\Http\Requests\UpdateJournalEntryRequest;
use App\Http\Resources\JournalEntryResource;
use App\Services\JournalEntryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class JournalEntryController extends BaseController
{
    public function __construct(
        protected JournalEntryService $journalEntryService
    ) {}

    public function index(Request $request): JsonResponse|AnonymousResourceCollection
    {
        $perPage = (int) $request->get('per_page', 15);

        if ($request->has('per_page')) {
            $journalEntries = $this->journalEntryService->getPaginated($perPage);

            return $this->sendPaginated(
                JournalEntryResource::collection($journalEntries),
                'JournalEntries başarıyla getirildi'
            );
        }

        $journalEntries = $this->journalEntryService->getAll();

        return JournalEntryResource::collection($journalEntries);
    }

    public function store(StoreJournalEntryRequest $request): JsonResponse
    {
        $journalEntry = $this->journalEntryService->create($request->validated());

        return $this->sendSuccess(
            new JournalEntryResource($journalEntry),
            'JournalEntry başarıyla oluşturuldu',
            201
        );
    }

    public function show(string $id): JsonResponse
    {
        $journalEntry = $this->journalEntryService->findByIdOrFail($id);

        return $this->sendSuccess(
            new JournalEntryResource($journalEntry),
            'JournalEntry başarıyla getirildi'
        );
    }

    public function update(UpdateJournalEntryRequest $request, string $id): JsonResponse
    {
        $journalEntry = $this->journalEntryService->update($id, $request->validated());

        return $this->sendSuccess(
            new JournalEntryResource($journalEntry),
            'JournalEntry başarıyla güncellendi'
        );
    }

    public function destroy(string $id): JsonResponse
    {
        $this->journalEntryService->delete($id);

        return $this->sendSuccess(
            null,
            'JournalEntry başarıyla silindi'
        );
    }
}
