<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Resources\SaleResource;
use App\Services\SaleService;
use Illuminate\Http\Request;

class SaleController extends BaseController
{
    public function __construct(protected SaleService $saleService) {}

    public function index(Request $request)
    {
        $perPage = (int) $request->get('per_page', 15);
        if ($request->has('per_page')) {
            return $this->sendPaginated(SaleResource::collection($this->saleService->getPaginated($perPage)), 'Sales retrieved');
        }

        return SaleResource::collection($this->saleService->getAll());
    }

    public function store(Request $request)
    {
        return $this->sendSuccess(new SaleResource($this->saleService->create($request->all())), 'Sale created', 201);
    }

    public function show(string $id)
    {
        return $this->sendSuccess(new SaleResource($this->saleService->findByIdOrFail($id)), 'Sale retrieved');
    }
}
