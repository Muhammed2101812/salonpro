<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateTests extends Command
{
    protected $signature = 'make:api-tests {--controllers=*}';

    protected $description = 'Generate feature tests for API controllers';

    public function handle(): int
    {
        $controllers = $this->option('controllers');

        if (empty($controllers)) {
            // Get all API controllers
            $controllerFiles = File::files(app_path('Http/Controllers/API'));
            $controllers = collect($controllerFiles)
                ->map(fn($file) => pathinfo($file->getFilename(), PATHINFO_FILENAME))
                ->reject(fn($name) => in_array($name, ['BaseController', 'AuthController', 'DashboardController']))
                ->values()
                ->toArray();
        }

        $this->info("Generating feature tests for " . count($controllers) . " controllers...");

        $progressBar = $this->output->createProgressBar(count($controllers));

        foreach ($controllers as $controllerName) {
            $this->generateFeatureTest($controllerName);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);
        $this->info("✅ Feature tests generated successfully!");

        return self::SUCCESS;
    }

    protected function generateFeatureTest(string $controllerName): void
    {
        $modelName = str_replace('Controller', '', $controllerName);
        $testPath = base_path("tests/Feature/{$modelName}Test.php");

        if (File::exists($testPath)) {
            return; // Skip if already exists
        }

        $stub = $this->getTestStub($modelName);
        File::put($testPath, $stub);
    }

    protected function getTestStub(string $modelName): string
    {
        $modelVariable = Str::camel($modelName);
        $routeName = Str::kebab(Str::plural($modelName));

        return <<<PHP
<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\\{$modelName};
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class {$modelName}Test extends TestCase
{
    use RefreshDatabase;

    protected User \$user;

    protected function setUp(): void
    {
        parent::setUp();
        \$this->user = User::factory()->create();
    }

    public function test_can_list_{$modelVariable}s(): void
    {
        {$modelName}::factory()->count(3)->create();

        \$response = \$this->actingAs(\$this->user, 'sanctum')
            ->getJson('/api/v1/{$routeName}');

        \$response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id']
                ]
            ]);
    }

    public function test_can_create_{$modelVariable}(): void
    {
        \$data = {$modelName}::factory()->make()->toArray();

        \$response = \$this->actingAs(\$this->user, 'sanctum')
            ->postJson('/api/v1/{$routeName}', \$data);

        \$response->assertCreated()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);

        \$this->assertDatabaseHas('{$routeName}', [
            'id' => \$response->json('data.id')
        ]);
    }

    public function test_can_show_{$modelVariable}(): void
    {
        \${$modelVariable} = {$modelName}::factory()->create();

        \$response = \$this->actingAs(\$this->user, 'sanctum')
            ->getJson("/api/v1/{$routeName}/{\${$modelVariable}->id}");

        \$response->assertOk()
            ->assertJsonStructure([
                'success',
                'data' => ['id']
            ]);
    }

    public function test_can_update_{$modelVariable}(): void
    {
        \${$modelVariable} = {$modelName}::factory()->create();
        \$updateData = {$modelName}::factory()->make()->toArray();

        \$response = \$this->actingAs(\$this->user, 'sanctum')
            ->putJson("/api/v1/{$routeName}/{\${$modelVariable}->id}", \$updateData);

        \$response->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'data' => ['id']
            ]);
    }

    public function test_can_delete_{$modelVariable}(): void
    {
        \${$modelVariable} = {$modelName}::factory()->create();

        \$response = \$this->actingAs(\$this->user, 'sanctum')
            ->deleteJson("/api/v1/{$routeName}/{\${$modelVariable}->id}");

        \$response->assertOk()
            ->assertJson([
                'success' => true
            ]);

        \$this->assertSoftDeleted('{$routeName}', [
            'id' => \${$modelVariable}->id
        ]);
    }

    public function test_unauthorized_access_is_denied(): void
    {
        \$response = \$this->getJson('/api/v1/{$routeName}');

        \$response->assertUnauthorized();
    }
}

PHP;
    }
}
