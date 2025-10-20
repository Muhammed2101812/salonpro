<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateApiScaffold extends Command
{
    protected $signature = 'make:api-scaffold {model} {--skip-service} {--skip-resource} {--skip-requests} {--skip-controller}';

    protected $description = 'Generate complete API scaffold (Service, Resource, Requests, Controller) for a model';

    public function handle(): int
    {
        $modelName = $this->argument('model');

        $this->info("Generating API scaffold for {$modelName}...");

        if (!$this->option('skip-service')) {
            $this->generateService($modelName);
        }

        if (!$this->option('skip-resource')) {
            $this->generateResource($modelName);
        }

        if (!$this->option('skip-requests')) {
            $this->generateRequests($modelName);
        }

        if (!$this->option('skip-controller')) {
            $this->generateController($modelName);
        }

        $this->info("✅ API scaffold for {$modelName} generated successfully!");

        return self::SUCCESS;
    }

    protected function generateService(string $modelName): void
    {
        $servicePath = app_path("Services/{$modelName}Service.php");

        if (File::exists($servicePath)) {
            $this->warn("  Service already exists, skipping...");
            return;
        }

        $stub = <<<PHP
<?php

declare(strict_types=1);

namespace App\Services;

use App\Repositories\Contracts\\{$modelName}RepositoryInterface;

class {$modelName}Service extends BaseService
{
    public function __construct({$modelName}RepositoryInterface \$repository)
    {
        parent::__construct(\$repository);
    }
}

PHP;

        File::put($servicePath, $stub);
        $this->line("  ✓ Service created");
    }

    protected function generateResource(string $modelName): void
    {
        $resourcePath = app_path("Http/Resources/{$modelName}Resource.php");

        if (File::exists($resourcePath)) {
            $this->warn("  Resource already exists, skipping...");
            return;
        }

        $stub = <<<'PHP'
<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class {MODEL}Resource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            // Add fields here
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}

PHP;

        $content = str_replace('{MODEL}', $modelName, $stub);
        File::put($resourcePath, $content);
        $this->line("  ✓ Resource created");
    }

    protected function generateRequests(string $modelName): void
    {
        // Store Request
        $storeRequestPath = app_path("Http/Requests/Store{$modelName}Request.php");

        if (!File::exists($storeRequestPath)) {
            $storeStub = $this->getStoreRequestStub($modelName);
            File::put($storeRequestPath, $storeStub);
            $this->line("  ✓ StoreRequest created");
        } else {
            $this->warn("  StoreRequest already exists, skipping...");
        }

        // Update Request
        $updateRequestPath = app_path("Http/Requests/Update{$modelName}Request.php");

        if (!File::exists($updateRequestPath)) {
            $updateStub = $this->getUpdateRequestStub($modelName);
            File::put($updateRequestPath, $updateStub);
            $this->line("  ✓ UpdateRequest created");
        } else {
            $this->warn("  UpdateRequest already exists, skipping...");
        }
    }

    protected function getStoreRequestStub(string $modelName): string
    {
        $lowerModel = Str::lower(Str::snake($modelName));

        return <<<PHP
<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Store{$modelName}Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Add validation rules here
        ];
    }

    public function attributes(): array
    {
        return [
            // Add Turkish attribute names here
        ];
    }
}

PHP;
    }

    protected function getUpdateRequestStub(string $modelName): string
    {
        return <<<PHP
<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class Update{$modelName}Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Add validation rules here (use 'sometimes' for optional updates)
        ];
    }

    public function attributes(): array
    {
        return [
            // Add Turkish attribute names here
        ];
    }
}

PHP;
    }

    protected function generateController(string $modelName): void
    {
        $controllerPath = app_path("Http/Controllers/API/{$modelName}Controller.php");

        if (File::exists($controllerPath)) {
            $this->warn("  Controller already exists, skipping...");
            return;
        }

        $varName = Str::camel($modelName);
        $pluralVar = Str::plural($varName);
        $turkishName = $this->getTurkishName($modelName);
        $turkishPlural = $this->getTurkishPlural($modelName);

        $stub = <<<PHP
<?php

declare(strict_types=1);

namespace App\Http\Controllers\API;

use App\Http\Requests\Store{$modelName}Request;
use App\Http\Requests\Update{$modelName}Request;
use App\Http\Resources\\{$modelName}Resource;
use App\Services\\{$modelName}Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class {$modelName}Controller extends BaseController
{
    public function __construct(
        protected {$modelName}Service \${$varName}Service
    ) {}

    public function index(Request \$request): JsonResponse|AnonymousResourceCollection
    {
        \$perPage = (int) \$request->get('per_page', 15);

        if (\$request->has('per_page')) {
            \${$pluralVar} = \$this->{$varName}Service->getPaginated(\$perPage);

            return \$this->sendPaginated(
                {$modelName}Resource::collection(\${$pluralVar}),
                '{$turkishPlural} başarıyla getirildi'
            );
        }

        \${$pluralVar} = \$this->{$varName}Service->getAll();

        return {$modelName}Resource::collection(\${$pluralVar});
    }

    public function store(Store{$modelName}Request \$request): JsonResponse
    {
        \${$varName} = \$this->{$varName}Service->create(\$request->validated());

        return \$this->sendSuccess(
            new {$modelName}Resource(\${$varName}),
            '{$turkishName} başarıyla oluşturuldu',
            201
        );
    }

    public function show(string \$id): JsonResponse
    {
        \${$varName} = \$this->{$varName}Service->findByIdOrFail(\$id);

        return \$this->sendSuccess(
            new {$modelName}Resource(\${$varName}),
            '{$turkishName} başarıyla getirildi'
        );
    }

    public function update(Update{$modelName}Request \$request, string \$id): JsonResponse
    {
        \${$varName} = \$this->{$varName}Service->update(\$id, \$request->validated());

        return \$this->sendSuccess(
            new {$modelName}Resource(\${$varName}),
            '{$turkishName} başarıyla güncellendi'
        );
    }

    public function destroy(string \$id): JsonResponse
    {
        \$this->{$varName}Service->delete(\$id);

        return \$this->sendSuccess(
            null,
            '{$turkishName} başarıyla silindi'
        );
    }
}

PHP;

        File::put($controllerPath, $stub);
        $this->line("  ✓ Controller created");
    }

    protected function getTurkishName(string $modelName): string
    {
        // Simple mapping - can be extended
        $map = [
            'Appointment' => 'Randevu',
            'Customer' => 'Müşteri',
            'Employee' => 'Çalışan',
            'Service' => 'Hizmet',
            'Product' => 'Ürün',
            'Branch' => 'Şube',
            'Sale' => 'Satış',
            'Payment' => 'Ödeme',
            'Expense' => 'Gider',
        ];

        return $map[$modelName] ?? $modelName;
    }

    protected function getTurkishPlural(string $modelName): string
    {
        $map = [
            'Appointment' => 'Randevular',
            'Customer' => 'Müşteriler',
            'Employee' => 'Çalışanlar',
            'Service' => 'Hizmetler',
            'Product' => 'Ürünler',
            'Branch' => 'Şubeler',
            'Sale' => 'Satışlar',
            'Payment' => 'Ödemeler',
            'Expense' => 'Giderler',
        ];

        return $map[$modelName] ?? Str::plural($modelName);
    }
}
