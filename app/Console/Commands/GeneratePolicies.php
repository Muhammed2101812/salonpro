<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class GeneratePolicies extends Command
{
    protected $signature = 'make:policies {--models=*}';

    protected $description = 'Generate policy classes for models';

    public function handle(): int
    {
        $models = $this->option('models');

        if (empty($models)) {
            // Core models that need policies
            $models = [
                'Appointment',
                'Branch',
                'Customer',
                'Employee',
                'Product',
                'Service',
                'Sale',
                'Payment',
                'Expense',
                'Invoice',
                'PurchaseOrder',
                'StockAudit',
                'MarketingCampaign',
                'LoyaltyProgram',
                'SystemSetting',
            ];
        }

        $this->info("Generating policies for " . count($models) . " models...");

        $progressBar = $this->output->createProgressBar(count($models));

        foreach ($models as $modelName) {
            $this->generatePolicy($modelName);
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);
        $this->info("✅ Policies generated successfully!");
        $this->info("Register them in AuthServiceProvider!");

        return self::SUCCESS;
    }

    protected function generatePolicy(string $modelName): void
    {
        $policyPath = app_path("Policies/{$modelName}Policy.php");

        if (File::exists($policyPath)) {
            return;
        }

        File::ensureDirectoryExists(app_path('Policies'));

        $stub = <<<PHP
<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\\{$modelName};
use App\Models\User;

class {$modelName}Policy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User \$user): bool
    {
        return \$user->hasPermissionTo('view-{$this->getPermissionName($modelName)}');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User \$user, {$modelName} \${$this->getVariableName($modelName)}): bool
    {
        return \$user->hasPermissionTo('view-{$this->getPermissionName($modelName)}');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User \$user): bool
    {
        return \$user->hasPermissionTo('create-{$this->getPermissionName($modelName)}');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User \$user, {$modelName} \${$this->getVariableName($modelName)}): bool
    {
        return \$user->hasPermissionTo('update-{$this->getPermissionName($modelName)}');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User \$user, {$modelName} \${$this->getVariableName($modelName)}): bool
    {
        return \$user->hasPermissionTo('delete-{$this->getPermissionName($modelName)}');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User \$user, {$modelName} \${$this->getVariableName($modelName)}): bool
    {
        return \$user->hasPermissionTo('restore-{$this->getPermissionName($modelName)}');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User \$user, {$modelName} \${$this->getVariableName($modelName)}): bool
    {
        return \$user->hasPermissionTo('force-delete-{$this->getPermissionName($modelName)}');
    }
}

PHP;

        File::put($policyPath, $stub);
    }

    protected function getVariableName(string $modelName): string
    {
        return lcfirst($modelName);
    }

    protected function getPermissionName(string $modelName): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '-$0', $modelName));
    }
}
