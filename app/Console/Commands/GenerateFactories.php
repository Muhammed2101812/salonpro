<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateFactories extends Command
{
    protected $signature = 'make:factories {--models=*}';

    protected $description = 'Generate factory classes for specified models or all models';

    public function handle(): int
    {
        $models = $this->option('models');

        if (empty($models)) {
            // Get all models
            $modelFiles = File::files(app_path('Models'));
            $models = collect($modelFiles)
                ->map(fn($file) => pathinfo($file->getFilename(), PATHINFO_FILENAME))
                ->reject(fn($name) => $name === 'BaseModel')
                ->values()
                ->toArray();
        }

        $this->info("Generating factories for " . count($models) . " models...");

        $progressBar = $this->output->createProgressBar(count($models));

        foreach ($models as $modelName) {
            $this->generateFactory($modelName);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);
        $this->info("✅ Factories generated successfully!");

        return self::SUCCESS;
    }

    protected function generateFactory(string $modelName): void
    {
        $factoryPath = database_path("factories/{$modelName}Factory.php");

        if (File::exists($factoryPath)) {
            return; // Skip if already exists
        }

        $stub = $this->getFactoryStub($modelName);
        File::put($factoryPath, $stub);
    }

    protected function getFactoryStub(string $modelName): string
    {
        return <<<PHP
<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\\{$modelName};
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\\{$modelName}>
 */
class {$modelName}Factory extends Factory
{
    protected \$model = {$modelName}::class;

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

PHP;
    }
}
