<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\BranchSetting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\BranchSetting>
 */
class BranchSettingFactory extends Factory
{
    protected $model = BranchSetting::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Add factory definitions here
        ];
    }
}
