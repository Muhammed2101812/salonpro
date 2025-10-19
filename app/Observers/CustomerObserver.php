<?php

namespace App\Observers;

use App\Models\Customer;
use App\Traits\Loggable;

class CustomerObserver
{
    use Loggable;

    /**
     * Handle the Customer "created" event.
     */
    public function created(Customer $customer): void
    {
        $this->logCustomer('Customer created', [
            'customer_id' => $customer->id,
            'name' => $customer->first_name.' '.$customer->last_name,
        ]);

        $this->logAudit('Customer created', [
            'customer_id' => $customer->id,
            'action' => 'create',
        ]);
    }

    /**
     * Handle the Customer "updated" event.
     */
    public function updated(Customer $customer): void
    {
        $this->logCustomer('Customer updated', [
            'customer_id' => $customer->id,
            'changes' => $customer->getChanges(),
        ]);

        $this->logAudit('Customer updated', [
            'customer_id' => $customer->id,
            'action' => 'update',
            'changes' => $customer->getChanges(),
        ]);
    }

    /**
     * Handle the Customer "deleted" event.
     */
    public function deleted(Customer $customer): void
    {
        $this->logCustomer('Customer deleted', [
            'customer_id' => $customer->id,
            'soft_delete' => true,
        ]);

        $this->logAudit('Customer deleted', [
            'customer_id' => $customer->id,
            'action' => 'delete',
        ]);
    }

    /**
     * Handle the Customer "restored" event.
     */
    public function restored(Customer $customer): void
    {
        $this->logCustomer('Customer restored', [
            'customer_id' => $customer->id,
        ]);

        $this->logAudit('Customer restored', [
            'customer_id' => $customer->id,
            'action' => 'restore',
        ]);
    }

    /**
     * Handle the Customer "force deleted" event.
     */
    public function forceDeleted(Customer $customer): void
    {
        $this->logCustomer('Customer permanently deleted', [
            'customer_id' => $customer->id,
        ], 'warning');

        $this->logAudit('Customer permanently deleted', [
            'customer_id' => $customer->id,
            'action' => 'force_delete',
        ]);
    }
}
