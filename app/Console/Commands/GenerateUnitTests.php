<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateUnitTests extends Command
{
    protected $signature = 'make:unit-tests';

    protected $description = 'Generate unit tests for Services and Repositories';

    public function handle(): int
    {
        $this->info("Generating unit tests...");

        // Generate Service tests
        $services = $this->getServices();
        $this->info("Creating " . count($services) . " service tests...");
        $progressBar = $this->output->createProgressBar(count($services));

        foreach ($services as $serviceName) {
            $this->generateServiceTest($serviceName);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine();

        // Generate Repository tests
        $repositories = $this->getRepositories();
        $this->info("Creating " . count($repositories) . " repository tests...");
        $progressBar = $this->output->createProgressBar(count($repositories));

        foreach ($repositories as $repositoryName) {
            $this->generateRepositoryTest($repositoryName);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);
        $this->info("✅ Unit tests generated successfully!");

        return self::SUCCESS;
    }

    protected function getServices(): array
    {
        $serviceFiles = File::files(app_path('Services'));
        return collect($serviceFiles)
            ->map(fn($file) => pathinfo($file->getFilename(), PATHINFO_FILENAME))
            ->reject(fn($name) => $name === 'BaseService')
            ->values()
            ->toArray();
    }

    protected function getRepositories(): array
    {
        $repoFiles = File::files(app_path('Repositories/Eloquent'));
        return collect($repoFiles)
            ->map(fn($file) => pathinfo($file->getFilename(), PATHINFO_FILENAME))
            ->reject(fn($name) => $name === 'BaseRepository')
            ->values()
            ->toArray();
    }

    protected function generateServiceTest(string $serviceName): void
    {
        $testPath = base_path("tests/Unit/Services/{$serviceName}Test.php");

        if (File::exists($testPath)) {
            return;
        }

        File::ensureDirectoryExists(dirname($testPath));
        $stub = $this->getServiceTestStub($serviceName);
        File::put($testPath, $stub);
    }

    protected function generateRepositoryTest(string $repositoryName): void
    {
        $testPath = base_path("tests/Unit/Repositories/{$repositoryName}Test.php");

        if (File::exists($testPath)) {
            return;
        }

        File::ensureDirectoryExists(dirname($testPath));
        $stub = $this->getRepositoryTestStub($repositoryName);
        File::put($testPath, $stub);
    }

    protected function getServiceTestStub(string $serviceName): string
    {
        $modelName = str_replace('Service', '', $serviceName);
        $modelVariable = Str::camel($modelName);

        return <<<PHP
<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Models\\{$modelName};
use App\Repositories\Contracts\\{$modelName}RepositoryInterface;
use App\Services\\{$serviceName};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class {$serviceName}Test extends TestCase
{
    use RefreshDatabase;

    protected {$serviceName} \$service;
    protected {$modelName}RepositoryInterface \$repository;

    protected function setUp(): void
    {
        parent::setUp();
        \$this->repository = app({$modelName}RepositoryInterface::class);
        \$this->service = new {$serviceName}(\$this->repository);
    }

    public function test_can_get_all_{$modelVariable}s(): void
    {
        {$modelName}::factory()->count(5)->create();

        \$result = \$this->service->getAll();

        \$this->assertCount(5, \$result);
    }

    public function test_can_get_paginated_{$modelVariable}s(): void
    {
        {$modelName}::factory()->count(20)->create();

        \$result = \$this->service->getPaginated(10);

        \$this->assertEquals(10, \$result->perPage());
        \$this->assertEquals(20, \$result->total());
    }

    public function test_can_create_{$modelVariable}(): void
    {
        \$data = {$modelName}::factory()->make()->toArray();

        \$result = \$this->service->create(\$data);

        \$this->assertInstanceOf({$modelName}::class, \$result);
        \$this->assertDatabaseHas('{$modelVariable}s', ['id' => \$result->id]);
    }

    public function test_can_update_{$modelVariable}(): void
    {
        \${$modelVariable} = {$modelName}::factory()->create();
        \$updateData = {$modelName}::factory()->make()->toArray();

        \$result = \$this->service->update(\${$modelVariable}->id, \$updateData);

        \$this->assertInstanceOf({$modelName}::class, \$result);
    }

    public function test_can_delete_{$modelVariable}(): void
    {
        \${$modelVariable} = {$modelName}::factory()->create();

        \$result = \$this->service->delete(\${$modelVariable}->id);

        \$this->assertTrue(\$result);
        \$this->assertSoftDeleted('{$modelVariable}s', ['id' => \${$modelVariable}->id]);
    }

    public function test_can_find_{$modelVariable}_by_id(): void
    {
        \${$modelVariable} = {$modelName}::factory()->create();

        \$result = \$this->service->findById(\${$modelVariable}->id);

        \$this->assertInstanceOf({$modelName}::class, \$result);
        \$this->assertEquals(\${$modelVariable}->id, \$result->id);
    }
}

PHP;
    }

    protected function getRepositoryTestStub(string $repositoryName): string
    {
        $modelName = str_replace('Repository', '', $repositoryName);
        $modelVariable = Str::camel($modelName);

        return <<<PHP
<?php

declare(strict_types=1);

namespace Tests\Unit\Repositories;

use App\Models\\{$modelName};
use App\Repositories\Eloquent\\{$repositoryName};
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class {$repositoryName}Test extends TestCase
{
    use RefreshDatabase;

    protected {$repositoryName} \$repository;

    protected function setUp(): void
    {
        parent::setUp();
        \$this->repository = new {$repositoryName}(new {$modelName}());
    }

    public function test_can_get_all_records(): void
    {
        {$modelName}::factory()->count(3)->create();

        \$result = \$this->repository->all();

        \$this->assertCount(3, \$result);
    }

    public function test_can_create_record(): void
    {
        \$data = {$modelName}::factory()->make()->toArray();

        \$result = \$this->repository->create(\$data);

        \$this->assertInstanceOf({$modelName}::class, \$result);
        \$this->assertDatabaseHas('{$modelVariable}s', ['id' => \$result->id]);
    }

    public function test_can_find_record_by_id(): void
    {
        \${$modelVariable} = {$modelName}::factory()->create();

        \$result = \$this->repository->find(\${$modelVariable}->id);

        \$this->assertInstanceOf({$modelName}::class, \$result);
        \$this->assertEquals(\${$modelVariable}->id, \$result->id);
    }

    public function test_can_update_record(): void
    {
        \${$modelVariable} = {$modelName}::factory()->create();
        \$updateData = {$modelName}::factory()->make()->toArray();

        \$result = \$this->repository->update(\${$modelVariable}->id, \$updateData);

        \$this->assertInstanceOf({$modelName}::class, \$result);
    }

    public function test_can_delete_record(): void
    {
        \${$modelVariable} = {$modelName}::factory()->create();

        \$result = \$this->repository->delete(\${$modelVariable}->id);

        \$this->assertTrue(\$result);
        \$this->assertSoftDeleted('{$modelVariable}s', ['id' => \${$modelVariable}->id]);
    }
}

PHP;
    }
}
