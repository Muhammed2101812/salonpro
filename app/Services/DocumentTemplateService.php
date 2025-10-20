<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\DocumentTemplateRepositoryInterface;

class DocumentTemplateService extends BaseService
{
    public function __construct(DocumentTemplateRepositoryInterface $repository)
    {
        parent::__construct($repository);
    }
}
