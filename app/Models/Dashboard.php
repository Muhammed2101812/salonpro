<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Dashboard model for authorization purposes.
 * This is a virtual model that doesn't have a database table.
 */
class Dashboard extends Model
{
    /**
     * The table associated with the model.
     * This model is virtual and doesn't use a database table.
     *
     * @var string|null
     */
    protected $table = null;
}
