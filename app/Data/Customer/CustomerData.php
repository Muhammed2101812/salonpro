<?php

namespace App\Data\Customer;

use Spatie\LaravelData\Attributes\Validation\Email;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class CustomerData extends Data
{
    public function __construct(
        #[Required, Max(255)]
        public string $first_name,

        #[Required, Max(255)]
        public string $last_name,

        #[Nullable, Email, Max(255)]
        public ?string $email,

        #[Nullable, Max(20)]
        public ?string $phone,

        #[Nullable]
        public ?string $birth_date,

        #[Nullable, Max(10)]
        public ?string $gender,

        #[Nullable]
        public ?string $notes,

        #[Nullable]
        public ?string $address,

        #[Nullable, Max(100)]
        public ?string $city,

        #[Nullable, Max(100)]
        public ?string $country,

        #[Nullable]
        public ?int $branch_id = null,
    ) {}

    public static function fromModel(\App\Models\Customer $customer): self
    {
        return new self(
            first_name: $customer->first_name,
            last_name: $customer->last_name,
            email: $customer->email,
            phone: $customer->phone,
            birth_date: $customer->birth_date?->format('Y-m-d'),
            gender: $customer->gender,
            notes: $customer->notes,
            address: $customer->address,
            city: $customer->city,
            country: $customer->country,
            branch_id: $customer->branch_id,
        );
    }

    public function getFullName(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }
}
