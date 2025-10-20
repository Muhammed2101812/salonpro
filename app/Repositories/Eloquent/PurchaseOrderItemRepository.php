<?php

namespace App\Repositories\Eloquent;

use App\Models\PurchaseOrderItem;
use App\Repositories\Contracts\PurchaseOrderItemRepositoryInterface;

class PurchaseOrderItemRepository implements PurchaseOrderItemRepositoryInterface
{
    public function getByPurchaseOrder(int $purchaseOrderId): \Illuminate\Database\Eloquent\Collection
    {
        return PurchaseOrderItem::with(['product', 'purchaseOrder'])
            ->where('purchase_order_id', $purchaseOrderId)
            ->get();
    }

    public function findById(int $id): ?PurchaseOrderItem
    {
        return PurchaseOrderItem::with(['product', 'purchaseOrder'])
            ->find($id);
    }

    public function create(array $data): PurchaseOrderItem
    {
        return PurchaseOrderItem::create($data);
    }

    public function update(PurchaseOrderItem $item, array $data): bool
    {
        return $item->update($data);
    }

    public function delete(PurchaseOrderItem $item): bool
    {
        return $item->delete();
    }

    public function createMultiple(int $purchaseOrderId, array $items): array
    {
        $createdItems = [];
        
        foreach ($items as $itemData) {
            $itemData['purchase_order_id'] = $purchaseOrderId;
            $createdItems[] = $this->create($itemData);
        }
        
        return $createdItems;
    }

    public function updateReceivedQuantity(PurchaseOrderItem $item, int $quantity): bool
    {
        return $item->update(['received_quantity' => $quantity]);
    }
}
