<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\ProductAttributeRepositoryInterface;
use App\Services\Contracts\ProductAttributeServiceInterface;

class ProductAttributeService extends BaseService implements ProductAttributeServiceInterface
{
    public function __construct(
        protected ProductAttributeRepositoryInterface $attributeRepository
    ) {
        parent::__construct($attributeRepository);
    }

    public function findByCode(string $code): mixed
    {
        $attribute = $this->attributeRepository->findByCode($code);

        if (!$attribute) {
            throw new \RuntimeException("Attribute with code '{$code}' not found");
        }

        return $attribute;
    }

    public function getFilterable(): mixed
    {
        return $this->attributeRepository->getFilterable();
    }

    public function getRequired(): mixed
    {
        return $this->attributeRepository->getRequired();
    }

    public function getAllSorted(): mixed
    {
        return $this->attributeRepository->getAllSorted();
    }
}
