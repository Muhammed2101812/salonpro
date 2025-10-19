<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\InventoryMovementRepositoryInterface;
use App\Repositories\Contracts\ProductRepositoryInterface;

class InventoryMovementService extends BaseService
{
    public function __construct(
        InventoryMovementRepositoryInterface $repository,
        protected ProductRepositoryInterface $productRepository
    ) {
        parent::__construct($repository);
    }

    /**
     * Create a new inventory movement and update product stock.
     */
    public function create(array $data): \Illuminate\Database\Eloquent\Model
    {
        // Get product
        $product = $this->productRepository->findOrFail($data['product_id']);

        // Store quantity before
        $data['quantity_before'] = $product->stock_quantity;

        // Calculate new quantity
        if ($data['type'] === 'in') {
            $newQuantity = $product->stock_quantity + abs($data['quantity']);
        } elseif ($data['type'] === 'out') {
            $newQuantity = $product->stock_quantity - abs($data['quantity']);
        } else { // adjustment
            $newQuantity = $data['quantity'];
        }

        $data['quantity_after'] = max(0, $newQuantity); // Prevent negative stock

        // Create movement
        $movement = parent::create($data);

        // Update product stock
        $this->productRepository->update($product->id, [
            'stock_quantity' => $data['quantity_after'],
        ]);

        return $movement;
    }
}
